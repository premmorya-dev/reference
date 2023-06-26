<?php

require_once DIR_SYSTEM . 'library/razorpay/razorpay-sdk/Razorpay.php';
require_once DIR_SYSTEM . 'library/razorpay/razorpay-lib/createwebhook.php';


use Mpdf\Tag\H1;
use Razorpay\Api\Api;
use Razorpay\Api\Errors;

class ModelApsRazorpayApsRazorpay extends Model
{


    public function getAllPayment($data)
    {


        $sql ="SELECT * FROM " . DB_PREFIX . "apsinno_razorpay_transactions rt left join " . DB_PREFIX . "apsinno_order_transaction_ledger  tl on rt.oc_order_id=tl.order_id ";

        $implode = [];

        $implode[] = " tl.transaction_type='credit'";

        if (!empty($data['filter_rp_transaction_id'])) {
            $implode[] = " rt.razarpay_payment_id LIKE '" . $this->db->escape($data['filter_rp_transaction_id']) . "%'";
        }

        if (!empty($data['filter_oc_id'])) {
            $implode[] = " rt.oc_order_id LIKE '" . $this->db->escape($data['filter_oc_id']) . "%'";
        }


        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $implode[] = " rt.captured ='" . $this->db->escape($data['filter_status']) . "'";
        }

        if ($implode) {
            $sql .= " where " . implode(" AND ", $implode);
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }


        $all_payments = $this->db->query($sql)->rows;
        //    print_r($all_payments );die;
        if ($all_payments) {
            return $all_payments;
        } else {
            return [];
        }

    }

    public function getTotalAllPayment($data)
    {

        $sql ="SELECT * FROM " . DB_PREFIX . "apsinno_razorpay_transactions rt left join " . DB_PREFIX . "apsinno_order_transaction_ledger  tl on rt.oc_order_id=tl.order_id ";

        $implode = [];

        $implode[] = " tl.transaction_type='credit'";

        if (!empty($data['filter_rp_transaction_id'])) {
            $implode[] = " rt.razarpay_payment_id LIKE '" . $this->db->escape($data['filter_rp_transaction_id']) . "%'";
        }

        if (!empty($data['filter_oc_id'])) {
            $implode[] = " rt.oc_order_id LIKE '" . $this->db->escape($data['filter_oc_id']) . "%'";
        }


        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $implode[] = " rt.captured ='" . $this->db->escape($data['filter_status']) . "'";
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


    public function isPaymentExist($transaction_id)
    {
        $is_payment_exist = $this->db->query("SELECT * FROM " . DB_PREFIX . "apsinno_razorpay_transactions where razarpay_payment_id='" . $transaction_id . "'")->num_rows;

        if ($is_payment_exist > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getTransaction($transaction_id)
    {
        $payment_response = [];
        $transaction = $this->db->query("SELECT * FROM " . DB_PREFIX . "apsinno_razorpay_transactions where razarpay_payment_id='" . $transaction_id . "'")->row;




        if (isset($transaction_id) && $transaction_id) {

            $api = new Api($this->config->get('payment_razorpay_key_id'), $this->config->get('payment_razorpay_key_secret'));

            $payments = (array) $api->payment->fetch($transaction_id);
         
            if ($payments) {
                foreach ($payments as $payment) {
                    $card_detail = [];
                    $auth_detail = [];
                    $notes = [];
                    $order_amount = 0;
                    if (isset($payment['card']) && $payment['card']) {
                        foreach ((array) $payment['card'] as $card) {
                            $card_detail = $card;
                        }

                    }

                    if (isset($payment['acquirer_data']) && $payment['acquirer_data']) {
                        foreach ((array) $payment['acquirer_data'] as $auth) {
                            $auth_detail = $auth;
                        }

                    }

                    if (isset($payment['notes']) && $payment['notes']) {
                        foreach ((array) $payment['notes'] as $note) {
                            $notes = $note;
                        }

                    }

                    if (isset($notes['opencart_order_id']) && $notes['opencart_order_id']) {
                        $order_amount = $notes['opencart_order_id'];

                        $order = $this->db->query("SELECT * FROM " . DB_PREFIX . "order where order_id='" . $notes['opencart_order_id'] . "'")->row;
                        $order_amount = (isset($order['total']) && $order['total']) ? $order['total'] : 0 ;
                    }
                    $customer = $this->getCustomerByEmail($payment['email']);

                    $payment_response = [
                        "razorpay_payment_id" => $this->db->escape($payment['id']),
                        "entity" => $this->db->escape($payment['entity']),
                        "amount" => $payment['amount'] / 100,
                        "currency" => $this->db->escape($payment['currency']),
                        "status" => $this->db->escape($payment['status']),
                        "order_id" => $this->db->escape($payment['order_id']),
                        "invoice_id" => $this->db->escape($payment['invoice_id']),
                        "international" => $this->db->escape($payment['international']),
                        "method" => $this->db->escape($payment['method']),
                        "amount_refunded" => $this->db->escape($payment['amount_refunded']),
                        "refund_status" => $this->db->escape($payment['refund_status']),
                        "captured" => $this->db->escape($payment['captured']),
                        "description" => $this->db->escape($payment['description']),
                        "card_id" => isset($card_detail['id']) ? $this->db->escape($card_detail['id']) : '',
                        "card_entity" => isset($card_detail['entity']) ? $this->db->escape($card_detail['entity']) : '',
                        "card_name" => isset($card_detail['name']) ? $this->db->escape($card_detail['name']) : '',
                        "card_last4" => isset($card_detail['last4']) ? $this->db->escape($card_detail['last4']) : '',
                        "card_network" => isset($card_detail['network']) ? $this->db->escape($card_detail['network']) : '',
                        "card_type" => isset($card_detail['type']) ? $this->db->escape($card_detail['type']) : '',
                        "card_issuer" => isset($card_detail['issuer']) ? $this->db->escape($card_detail['issuer']) : '',
                        "card_international" => isset($card_detail['international']) ? $this->db->escape($card_detail['international']) : '',
                        "card_emi" => isset($card_detail['emi']) ? $this->db->escape($card_detail['emi']) : '',
                        "card_sub_type" => isset($card_detail['sub_type']) ? $this->db->escape($card_detail['sub_type']) : '',
                        "card_token_iin" => isset($card_detail['token_iin']) ? $this->db->escape($card_detail['token_iin']) : '',
                        "bank" => $this->db->escape($payment['bank']),
                        "wallet" => $this->db->escape($payment['wallet']),
                        "vpa" => $this->db->escape($payment['vpa']),
                        "email" => isset($payment['email']) ? $this->db->escape($payment['email']) : '',
                        "telephone" => isset($customer['telephone']) ? $this->db->escape($customer['telephone']) : '',
                        "customer_id" => isset($customer['customer_id']) ? $this->db->escape($customer['customer_id']) : '',
                        "contact" => $this->db->escape($payment['contact']),
                        "notes" => isset($notes['opencart_order_id']) ? $this->db->escape($notes['opencart_order_id']) : '',
                       "opencart_order_id" => 0,
                        "order_amount_value" => $order_amount,
                        // "opencart_order_id"  =>    isset($notes['opencart_order_id']) ? $this->db->escape($notes['opencart_order_id']) : '',
                        "fee" => $this->db->escape($payment['fee']),
                        "tax" => $this->db->escape($payment['tax']),
                        "error_code" => $this->db->escape($payment['error_code']),
                        "error_description" => $this->db->escape($payment['error_description']),
                        "error_source" => $this->db->escape($payment['error_source']),
                        "error_step" => $this->db->escape($payment['error_step']),
                        "error_reason" => $this->db->escape($payment['error_reason']),
                        "auth_code" => isset($card_detail['auth_code']) ? $this->db->escape($card_detail['auth_code']) : '',
                        "created_at" => $this->db->escape(date('d-M-Y h:i:A', $payment['created_at'])),



                    ];


                }
            }

        }



        return $payment_response;
    }


    public function getPayment($transaction_log_id)
    {
        $payment = [];
        $sql = "SELECT * FROM " . DB_PREFIX . "apsinno_razorpay_transactions rt left join " . DB_PREFIX . "apsinno_order_transaction_ledger tl on rt.oc_order_id=tl.order_id where rt.transaction_log_id = '" . $transaction_log_id . "' and tl.transaction_type='credit' ";
        $payment = $this->db->query($sql)->row;

        return $payment;



    }

    public function getCustomerByEmail($email)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

        return $query->row;
    }

    public function addPayment($data)
    {

        $transaction_log_id = 0;
        $this->log->write("razorpay_payment_id:---" . $data['razorpay_payment_id']);
        if (isset($data['razorpay_payment_id']) && $data['razorpay_payment_id']) {

            try {
                $this->db->beginTransaction();
          
            
            $api = new Api($this->config->get('payment_razorpay_key_id'), $this->config->get('payment_razorpay_key_secret'));

            $payments = (array) $api->payment->fetch($data['razorpay_payment_id']);

            if ($payments) {
                foreach ($payments as $payment) {
                    $card_detail = [];
                    $auth_detail = [];
                    $notes = [];
                    if (isset($payment['card']) && $payment['card']) {
                        foreach ((array) $payment['card'] as $card) {
                            $card_detail = $card;
                        }

                    }

                    if (isset($payment['acquirer_data']) && $payment['acquirer_data']) {
                        foreach ((array) $payment['acquirer_data'] as $auth) {
                            $auth_detail = $auth;
                        }

                    }

                    if (isset($payment['notes']) && $payment['notes']) {
                        foreach ((array) $payment['notes'] as $note) {
                            $notes = $note;
                        }

                    }
                    $payment['order_id'] = isset($payment['order_id']) ? $payment['order_id'] : '';
                    $data['order_id'] = isset($data['order_id']) ? $data['order_id'] : '';
                    $data['customer_id'] = (isset($data['customer_id']) && $data['customer_id']) ? $data['customer_id'] : 0;
                    $data['total'] = (isset($data['total']) && $data['total']) ? $data['total'] : 0;
                    $payment['currency'] = isset($payment['currency']) ? $payment['currency'] : '';
                    $payment['entity'] = isset($payment['entity']) ? $payment['entity'] : '';
                    $payment['method'] = isset($payment['method']) ? $payment['method'] : '';
                    $payment['refund_status'] = isset($payment['refund_status']) ? $payment['refund_status'] : '';
                    $payment['captured'] = isset($payment['captured']) ? $payment['captured'] : '';
                    $payment['captured'] = isset($payment['captured']) ? $payment['captured'] : '';
                    $payment['description'] = isset($payment['description']) ? $payment['description'] : '';
                    $card_detail['id'] = isset($card_detail['id']) ? $card_detail['id'] : '';
                    $card_detail['entity'] = isset($card_detail['entity']) ? $card_detail['entity'] : '';
                    $card_detail['name'] = isset($card_detail['name']) ? $card_detail['name'] : '';
                    $card_detail['last4'] = isset($card_detail['last4']) ? $card_detail['last4'] : '';
                    $card_detail['network'] = isset($card_detail['network']) ? $card_detail['network'] : '';
                    $card_detail['type'] = isset($card_detail['type']) ? $card_detail['type'] : '';
                    $card_detail['issuer'] = isset($card_detail['issuer']) ? $card_detail['issuer'] : '';
                    $card_detail['international'] = isset($card_detail['international']) ? $card_detail['international'] : '';
                    $card_detail['sub_type'] = isset($card_detail['sub_type']) ? $card_detail['sub_type'] : '';
                    $card_detail['token_iin'] = isset($card_detail['token_iin']) ? $card_detail['token_iin'] : '';
                    $payment['bank'] = isset($payment['bank']) ? $payment['bank'] : '';
                    $payment['wallet'] = isset($payment['wallet']) ? $payment['wallet'] : '';
                    $payment['vpa'] = isset($payment['vpa']) ? $payment['vpa'] : '';
                    $payment['email'] = isset($payment['email']) ? $payment['email'] : '';
                    $payment['contact'] = isset($payment['contact']) ? $payment['contact'] : '';
                    $payment['fee'] = isset($payment['fee']) ? $payment['fee'] : '';
                    $payment['tax'] = isset($payment['tax']) ? $payment['tax'] : '';
                    $payment['error_code'] = isset($payment['error_code']) ? $payment['error_code'] : '';
                    $payment['error_description'] = isset($payment['error_description']) ? $payment['error_description'] : '';
                    $payment['error_source'] = isset($payment['error_source']) ? $payment['error_source'] : '';
                    $auth_detail['auth_code'] = isset($auth_detail['auth_code']) ? $auth_detail['auth_code'] : '';
                    $payment['created_at'] = isset($payment['created_at']) ? $payment['created_at'] : '';
                    $notes['opencart_order_id'] = isset($notes['opencart_order_id']) ? $notes['opencart_order_id'] : '';





                    $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_razorpay_transactions SET razarpay_payment_id = '" . $this->db->escape($data['razorpay_payment_id'])
                        . "', razorpay_order_id = '" . $this->db->escape($payment['order_id'])
                        . "', oc_order_id = '" . $this->db->escape($data['order_id'])
                        . "', customer_id = " . $data['customer_id']
                        . ", amount = " . $data['total']
                        . ", currency = '" . $this->db->escape($payment['currency'])
                        . "', entity = '" . $this->db->escape($payment['entity'])
                        . "', method = '" . $this->db->escape($payment['method'])
                        . "', refund_status = '" . $this->db->escape($payment['refund_status'])
                        . "', captured = '" . $this->db->escape($payment['captured'])
                        . "', description = '" . $this->db->escape($payment['description'])
                        . "', card_id = '" . $this->db->escape($card_detail['id'])
                        . "', card_entity = '" . $this->db->escape($card_detail['entity'])
                        . "', card_name = '" . $this->db->escape($card_detail['name'])
                        . "', card_last4 = '" . $this->db->escape($card_detail['last4'])
                        . "', card_network = '" . $this->db->escape($card_detail['network'])
                        . "', card_type = '" . $this->db->escape($card_detail['type'])
                        . "', card_issuer = '" . $this->db->escape($card_detail['issuer'])
                        . "', card_international = '" . $this->db->escape($card_detail['international'])
                        . "', card_sub_type = '" . $this->db->escape($card_detail['sub_type'])
                        . "', card_token_iin = '" . $this->db->escape($card_detail['token_iin'])
                        . "', bank = '" . $this->db->escape($payment['bank'])
                        . "', wallet = '" . $this->db->escape($payment['wallet'])
                        . "', vpa = '" . $this->db->escape($payment['vpa'])
                        . "', email = '" . $this->db->escape($payment['email'])
                        . "', contact = '" . $this->db->escape($payment['contact'])
                        . "', fee = '" . $this->db->escape($payment['fee'])
                        . "', tax = '" . $this->db->escape($payment['tax'])
                        . "', error_code = '" . $this->db->escape($payment['error_code'])
                        . "', error_description = '" . $this->db->escape($payment['error_description'])
                        . "', error_source = '" . $this->db->escape($payment['error_source'])
                        . "', auth_code = '" . $this->db->escape($auth_detail['auth_code'])
                        . "', created_at = '" . $this->db->escape($payment['created_at'])
                        . "', entry_type = 'automatic"
                        . "', notes = '" . $this->db->escape($notes['opencart_order_id'])

                        . "'");



                }

                $transaction_log_id = $this->db->getLastId();

                if ($transaction_log_id) {
                    $this->db->query(
                        "INSERT INTO " . DB_PREFIX . "apsinno_order_payment_processor SET processor_type = 'razorpay', transaction_log_id = " . $transaction_log_id
                    );
                }

                if (isset($data['is_order_id_get']) && $data['is_order_id_get'] == 'true') {

                    $ledger = $this->db->query("select transaction_id from " . DB_PREFIX . "apsinno_order_payment_processor where transaction_log_id = " . $transaction_log_id)->row;

                    $this->log->write("test1:  " . $ledger['transaction_id']);
                    // for credit transaction                
                    if (isset($ledger['transaction_id']) && $ledger['transaction_id']) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_order_transaction_ledger SET transaction_id = " . $ledger['transaction_id']
                            . ", transaction_type = 'credit"                          
                            . "', order_id = '" . $this->db->escape($data['order_id'])
                            . "', amount = " . $this->db->escape($data['order_amount_value'])
                            . ", description = 'recieved payment from razorpay against order id: " . $this->db->escape($data['order_id'])
                            . "'");

                        $credit_auto_legder_id = $this->db->getLastId();
                    }


                    // for debit transaction

                    if (isset($ledger['transaction_id']) && $ledger['transaction_id']) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_order_transaction_ledger SET transaction_id = " . $ledger['transaction_id']
                            . ", transaction_type = 'debit"
                            . "', order_id = '" . $this->db->escape($data['order_id'])
                            . "', amount = " . $this->db->escape($data['order_amount_value'])
                            . ", description = 'debit payment against order id: " . $this->db->escape($data['order_id'])
                            . "'");

                        $debit_auto_legder_id = $this->db->getLastId();
                    }


                } else {

                    if ($data['order_amount_value'] == $data['payment_amount_value']) {
                        $ledger = $this->db->query("select transaction_id from " . DB_PREFIX . "apsinno_order_payment_processor where transaction_log_id = " . $transaction_log_id)->row;

                        $this->log->write("test2:  " . $ledger['transaction_id']);
                        // for credit transaction
                        if (isset($ledger['transaction_id']) && $ledger['transaction_id']) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_order_transaction_ledger SET transaction_id = " . $ledger['transaction_id']
                                . ", transaction_type = 'credit"
                                . "', order_id = '" . $this->db->escape($data['order_id'])
                                . "', amount = " . $this->db->escape($data['order_amount_value'])
                                . ", description = 'recieved payment from razorpay against order id: " . $this->db->escape($data['order_id'])
                                . "'");

                            $credit_auto_legder_id = $this->db->getLastId();
                        }


                        // for debit transaction

                        if (isset($ledger['transaction_id']) && $ledger['transaction_id']) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_order_transaction_ledger SET transaction_id = " . $ledger['transaction_id']
                                . ", transaction_type = 'debit"
                                . "', order_id = '" . $this->db->escape($data['order_id'])
                                . "', amount = " . $this->db->escape($data['order_amount_value'])
                                . ", description = 'debit payment against order id: " . $this->db->escape($data['order_id'])
                                . "'");

                            $debit_auto_legder_id = $this->db->getLastId();
                        }


                    }

                    if ($data['order_amount_value'] < $data['payment_amount_value']) {
                        $this->log->write("less:  ");
                        $ledger = $this->db->query("select transaction_id from " . DB_PREFIX . "apsinno_order_payment_processor where transaction_log_id = " . $transaction_log_id)->row;

                        // for credit transaction
                        if (isset($ledger['transaction_id']) && $ledger['transaction_id']) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_order_transaction_ledger SET transaction_id = " . $ledger['transaction_id']
                                . ", transaction_type = 'credit"
                                . "', order_id = '" . $this->db->escape($data['order_id'])
                                . "', amount = " . $this->db->escape($data['order_amount_value'])
                                . ", description = 'recieved payment from razorpay against order id: " . $this->db->escape($data['order_id'])
                                . "'");

                            $credit_auto_legder_id = $this->db->getLastId();
                        }

                        $overflow_amount = $data['payment_amount_value'] - $data['order_amount_value'];
                        $this->log->write("order_amount_value:  " . $data['order_amount_value']);
                        $this->log->write("payment_amount_value:  " . $data['payment_amount_value']);
                        $this->log->write("diff:  " . $overflow_amount);
                        // debit with actual amount
                        if (isset($ledger['transaction_id']) && $ledger['transaction_id']) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_order_transaction_ledger SET transaction_id = " . $ledger['transaction_id']
                                . ", transaction_type = 'debit"
                                . "', order_id = '" . $this->db->escape($data['order_id'])
                                . "', amount = " . $this->db->escape($data['order_amount_value'])
                                . ", description = 'debit actual payment against order id: " . $this->db->escape($data['order_id'])
                                . "'");

                            $debit_auto_legder_id = $this->db->getLastId();
                        }
                        // debit with overflow amount
                        if (isset($ledger['transaction_id']) && $ledger['transaction_id']) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_order_transaction_ledger SET transaction_id = " . $ledger['transaction_id']
                                . ", transaction_type = 'debit"
                                . "', order_id = '" . $this->db->escape($data['order_id'])
                                . "', amount = " . $this->db->escape($overflow_amount)
                                . ", description = 'debit overflow payment against order id: " . $this->db->escape($data['order_id'])
                                . "'");


                        }

                        if ($debit_auto_legder_id) {
                            $this->db->query(
                                "INSERT INTO " . DB_PREFIX . "apsinno_order_payments SET order_id = " . $this->db->escape($data['order_id'])
                                . ", ledger_id =" . $debit_auto_legder_id

                            );
                        }



                    }

                    if ($data['order_amount_value'] > $data['payment_amount_value']) {


                        $ledger = $this->db->query("select transaction_id from " . DB_PREFIX . "apsinno_order_payment_processor where transaction_log_id = " . $transaction_log_id)->row;


                        // for credit transaction
                        if (isset($ledger['transaction_id']) && $ledger['transaction_id']) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_order_transaction_ledger SET transaction_id = " . $ledger['transaction_id']
                                . ", transaction_type = 'credit"
                                . "', order_id = '" . $this->db->escape($data['order_id'])
                                . "', amount = " . $this->db->escape($data['order_amount_value'])
                                . ", description = 'recieved payment from razorpay against order id: " . $this->db->escape($data['order_id'])
                                . "'");

                            $credit_auto_legder_id = $this->db->getLastId();
                        }


                        // for debit transaction

                        if (isset($ledger['transaction_id']) && $ledger['transaction_id']) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_order_transaction_ledger SET transaction_id = " . $ledger['transaction_id']
                                . ", transaction_type = 'debit"
                                . "', order_id = '" . $this->db->escape($data['order_id'])
                                . "', amount = " . $this->db->escape($data['order_amount_value'])
                                . ", description = 'debit payment against order id: " . $this->db->escape($data['order_id'] . "and payment amount is sort")
                                . "'");

                            $debit_auto_legder_id = $this->db->getLastId();
                        }


                    }

                }















            }
            $this->db->commitTransaction();
        }   catch (Exception $e) {
               
            $this->db->rollbackTransaction();
            $this->log->write( "Transaction failed: " . $e->getMessage() );          
        }


        } ///

        return $transaction_log_id ? $transaction_log_id : 0;

    }


    public function getCustomer($email)
    {
        $query = $this->db->query("SELECT customer_id,CONCAT(firstname,' ', lastname) as name ,email,telephone  FROM " . DB_PREFIX . "customer WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

        return $query->row;


    }

    public function getOrderById($customer_id)
    {
        $query = $this->db->query("SELECT  order_id,total,date_added,order_status_id FROM " . DB_PREFIX . "order WHERE customer_id = '" . $customer_id . "' and order_status_id='1' order by order_id desc");

        return $query->rows;


    }
}