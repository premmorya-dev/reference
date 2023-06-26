<?php

class ModelLedgerOrder extends Model
{


    public function getAllOrderPayment($data)
    {



        $sql = "select tl.order_id,o.date_added as order_date, tl.ledger_id,o.customer_id,o.email,o.telephone, o.total,opp.transaction_id,tl.transaction_type,tl.amount,tl.description,opp.processor_type,opp.transaction_log_id 
        from " . DB_PREFIX . "apsinno_order_transaction_ledger tl 
        left join " . DB_PREFIX . "order o on tl.order_id=o.order_id 
        left join " . DB_PREFIX . "apsinno_order_payment_processor opp on opp.transaction_id=tl.transaction_id  where tl.order_id != '0' and tl.transaction_type = 'credit' ";

        $implode = [];


        if (!empty($data['filter_order_id'])) {
            $implode[] = " tl.order_id = '" . $this->db->escape($data['filter_order_id']) . "'";
        }

        if (!empty($data['filter_email'])) {
            $implode[] = " o.email = '" . $this->db->escape($data['filter_email']) . "'";
        }


        if (!empty($data['filter_customer_tel'])) {
            $implode[] = " o.telephone ='" . $this->db->escape($data['filter_customer_tel']) . "'";
        }

        if (!empty($data['filter_order_total'])) {
            $implode[] = " o.total ='" . $this->db->escape($data['filter_order_total']) . "'";
        }

        if (!empty($data['filter_payment_processor'])) {
            $implode[] = " opp.processor_type ='" . $this->db->escape($data['filter_payment_processor']) . "'";
        }


        if ($implode) {
            $sql .= " where " . implode(" AND ", $implode);
        }

        $sql .= " order by tl.order_id DESC";

        // die($sql);

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }




        // die($sql);
        $all_payments = $this->db->query($sql)->rows;

        $return_array = [];
        if ($all_payments) {
            foreach ($all_payments as $all_payment) {

                $payment_detail = [];
                if (isset($all_payment['processor_type']) && $all_payment['processor_type']) {


                    $payment = $this->db->query("Select * from " . DB_PREFIX . "apsinno_" . $all_payment['processor_type'] . "_transactions where transaction_log_id='" . $all_payment['transaction_log_id'] . "'")->row;

                    if ($all_payment['processor_type'] == 'razorpay') {
                        $payment_detail = [
                            "transaction_log_id" => $payment['transaction_log_id'],
                            "status" => $payment['captured'],
                            "ref_no" => $payment['razarpay_payment_id'],
                            "payment_date" => $payment['created_at'],
                            "payment_amount" => $payment['amount'],

                        ];
                    }

                    if ($all_payment['processor_type'] == 'bank') {
                        $payment_detail = [
                            "transaction_log_id" => $payment['transaction_log_id'],
                            "status" => 'Paid',
                            "ref_no" => $payment['bank_account_no'],
                            "payment_date" => $payment['transaction_date'],
                            "payment_amount" => $payment['amount'],

                        ];
                    }

                }

                $return_array[] = [
                    "order_id" => $all_payment['order_id'],
                    "order_date" => $all_payment['order_date'],
                    "ledger_id" => $all_payment['ledger_id'],
                    "customer_id" => $all_payment['customer_id'],
                    "email" => $all_payment['email'],
                    "telephone" => $all_payment['telephone'],
                    "total" => $all_payment['total'],
                    "transaction_id" => $all_payment['transaction_id'],
                    "transaction_type" => $all_payment['transaction_type'],
                    "amount" => $all_payment['amount'],
                    "description" => $all_payment['description'],
                    "processor_type" => $all_payment['processor_type'],
                    "transaction_log_id" => $all_payment['transaction_log_id'],
                    "payment" => $payment_detail,
                ];

            }
        }


        //   print_r($return_array );die;
        if ($return_array) {
            return $return_array;
        } else {
            return [];
        }

    }

    public function getTotalAllPayment($data)
    {

        $sql = "select tl.order_id,o.date_added as order_date, tl.ledger_id,o.customer_id,o.email,o.telephone, o.total,opp.transaction_id,tl.transaction_type,tl.amount,tl.description,opp.processor_type,opp.transaction_log_id 
        from " . DB_PREFIX . "apsinno_order_transaction_ledger tl 
        left join " . DB_PREFIX . "order o on tl.order_id=o.order_id 
        left join " . DB_PREFIX . "apsinno_order_payment_processor opp on opp.transaction_id=tl.transaction_id  where tl.order_id != '0' and tl.transaction_type = 'credit' ";

        $implode = [];


        if (!empty($data['filter_order_id'])) {
            $implode[] = " tl.order_id = '" . $this->db->escape($data['filter_order_id']) . "'";
        }

        if (!empty($data['filter_email'])) {
            $implode[] = " o.email = '" . $this->db->escape($data['filter_email']) . "'";
        }


        if (!empty($data['filter_customer_tel'])) {
            $implode[] = " o.telephone ='" . $this->db->escape($data['filter_customer_tel']) . "'";
        }

        if (!empty($data['filter_order_total'])) {
            $implode[] = " o.total ='" . $this->db->escape($data['filter_order_total']) . "'";
        }

        if (!empty($data['filter_payment_processor'])) {
            $implode[] = " opp.processor_type ='" . $this->db->escape($data['filter_payment_processor']) . "'";
        }


        if ($implode) {
            $sql .= " where " . implode(" AND ", $implode);
        }

        $total = $this->db->query($sql)->num_rows;

        if ($total) {
            return $total;
        } else {
            return 0;
        }

    }


    public function getCustomer($email)
    {
        $query = $this->db->query("SELECT customer_id,CONCAT(firstname,' ', lastname) as name ,email,telephone  FROM " . DB_PREFIX . "customer WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

        return $query->row;


    }

    public function getOrderById($customer_id)
    {
        $query = $this->db->query("SELECT o.order_id,o.order_status_id ,o.total,o.date_added,o.order_status_id  FROM " . DB_PREFIX . "order o 
        left join " . DB_PREFIX . "apsinno_order_status_type os 
        on (o.order_status_id = os.order_status_id) 
        where os.`type` ='unpaid' And customer_id ='" . $customer_id . "'
        order by order_id desc");

        return $query->rows;
    }

    public function unsettledTransaction($customer_id)
    {
        $transaction_detail = [];
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "apsinno_order_transaction_ledger tl 
        left join " . DB_PREFIX . "apsinno_order_payment_processor  opp on tl.transaction_id = opp.transaction_id 
        WHERE tl.order_id =0 And tl.transaction_type='debit'")->rows;

        if ($query) {

            foreach ($query as $transaction) {

                if (isset($transaction['processor_type']) && $transaction['processor_type']) {
                    if ($transaction['processor_type'] == 'bank') {
                        $bank = $this->db->query("SELECT * FROM " . DB_PREFIX . "apsinno_bank_transactions WHERE transaction_log_id ='" . $transaction['transaction_log_id'] . "' and customer_id='" . $customer_id . "'")->row;
                        if ($bank) {
                            $transaction_detail[] = [
                                "ledger_id" => $transaction['ledger_id'],
                                "transaction_log_id" => $transaction['transaction_log_id'],
                                "transaction_id" => $transaction['transaction_id'],
                                "processor_type" => $transaction['processor_type'],
                                "ref_no" => $bank['bank_account_no'],
                                "method" => $bank['method'],
                                "amount" => $bank['amount'],
                                "transaction_date" => $bank['transaction_date'],
                            ];
                        }


                    } else if ($transaction['processor_type'] == 'razorpay') {
                        $bank = [];
                        $bank = $this->db->query("SELECT * FROM " . DB_PREFIX . "apsinno_razorpay_transactions WHERE transaction_log_id ='" . $transaction['transaction_log_id'] . "' and customer_id='" . $customer_id . "'")->row;

                        if ($bank) {

                            $transaction_detail[] = [
                                "ledger_id" => $transaction['ledger_id'],
                                "transaction_log_id" => $transaction['transaction_log_id'],
                                "transaction_id" => $transaction['transaction_id'],
                                "processor_type" => $transaction['processor_type'],
                                "ref_no" => $bank['razarpay_payment_id'],
                                "method" => $bank['method'],
                                "amount" => $bank['amount'],
                                "transaction_date" => date('d-M-Y h:i:A', $bank['created_at']),
                            ];
                        }

                    }

                }
            }

        }

        return $transaction_detail;
    }

    public function updateLedger($data)
    {
        if ($data['order_id'] && $data['ledger_id']) {
            foreach ($data['ledger_id'] as $ledger_id) {
               
                   $query = $this->db->query("UPDATE " . DB_PREFIX . "apsinno_order_transaction_ledger set order_id= '".$data['order_id']."' WHERE ledger_id ='". $ledger_id ."'");
            }
        }
    }


}