<?php


class ModelNeftNeft extends Model
{


    public function getAllPayment($data)
    {

        $sql = "SELECT bt.transaction_log_id,bt.bank_account_no,bt.method,bt.currency,bt.customer_id,bt.email,bt.transaction_reference_id,bt.transaction_naration,bt.transaction_date,bt.comment,bt.date_added,bt.date_modified,opp.transaction_id,opp.processor_type,tl.ledger_id,tl.transaction_type,bt.amount,tl.description,tl.order_id,tl.ledger_id FROM " . DB_PREFIX . "apsinno_bank_transactions bt left join " . DB_PREFIX . "apsinno_order_payment_processor opp on bt.transaction_log_id=opp.transaction_log_id left join " . DB_PREFIX . "apsinno_order_transaction_ledger tl on tl.transaction_id = opp.transaction_id " ; 
        
     

        $implode = [];

        $implode[] = " (tl.transaction_type IS NULL OR  tl.transaction_type='debit')";

        if (!empty($data['bank_account_no']) && !is_null($data['bank_account_no']) ) {
            $implode[] = " bt.bank_account_no = '" . $this->db->escape($data['bank_account_no']) . "'";
        }

        if (!empty($data['method']) && !is_null($data['method']) ) {
            $implode[] = " bt.method = '" . $this->db->escape($data['method']) . "'";
        }

        if (isset($data['fill_email']) && !is_null($data['fill_email']) && $data['fill_email'] !=''  ) {
            $implode[] = " bt.email ='" . $this->db->escape($data['fill_email']) . "'";
        }


        if (isset($data['transaction_reference_id']) && !is_null($data['transaction_reference_id'])) {
            $implode[] = " bt.transaction_reference_id ='" . $this->db->escape($data['transaction_reference_id']) . "'";
        }


        if (isset($data['transaction_date']) && !is_null($data['transaction_date'])) {
            $implode[] = " bt.transaction_date ='" . $this->db->escape($data['transaction_date']) . "'";
        }

        if (isset($data['currency']) && !is_null($data['currency'])) {
            $implode[] = " bt.currency ='" . $this->db->escape($data['currency']) . "'";
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
            // print_r($all_payments );die;
        if ($all_payments) {
            return $all_payments;
        } else {
            return [];
        }

    }

    public function getTotalAllPayment($data)
    {
      

        $sql = "SELECT bt.transaction_log_id,bt.bank_account_no,bt.method,bt.currency,bt.customer_id,bt.email,bt.transaction_reference_id,bt.transaction_naration,bt.transaction_date,bt.comment,bt.date_added,bt.date_modified,opp.transaction_id,opp.processor_type,tl.ledger_id,tl.transaction_type,tl.amount,tl.description,tl.order_id,tl.ledger_id FROM " . DB_PREFIX . "apsinno_bank_transactions bt left join " . DB_PREFIX . "apsinno_order_payment_processor opp on bt.transaction_log_id=opp.transaction_log_id left join " . DB_PREFIX . "apsinno_order_transaction_ledger tl on tl.transaction_id = opp.transaction_id " ; 
        
     

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
        $transaction = [];
        $sql = "SELECT bt.transaction_log_id,bt.bank_account_no,bt.method,bt.currency,bt.customer_id,bt.email,bt.transaction_reference_id,bt.transaction_naration,bt.transaction_date,bt.comment,bt.date_added,bt.date_modified,opp.transaction_id,opp.processor_type,tl.ledger_id,tl.transaction_type,bt.amount,tl.description,tl.order_id,tl.ledger_id FROM " . DB_PREFIX . "apsinno_bank_transactions bt left join " . DB_PREFIX . "apsinno_order_payment_processor opp on bt.transaction_log_id=opp.transaction_log_id left join " . DB_PREFIX . "apsinno_order_transaction_ledger tl on tl.transaction_id = opp.transaction_id " ; 

        $implode = [];

        $implode[] = " (tl.transaction_type IS NULL OR  tl.transaction_type='debit')";

        if (!empty($transaction_id) && !is_null($transaction_id) ) {
            $implode[] = " bt.transaction_log_id = '" . $this->db->escape($transaction_id) . "'";
        }

        if ($implode) {
            $sql .= " where " . implode(" AND ", $implode);
        }
     
        $transaction = $this->db->query($sql)->row;



        return $transaction;
    }


    public function getPayment($transaction_log_id)
    {
        $payment = [];
        $sql = "SELECT * FROM " . DB_PREFIX . "apsinno_razorpay_transactions where rt.transaction_log_id = '" . $transaction_log_id . "'";
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

        if( !isset($data['order_id']) || !$data['order_id'] || $data['order_id'] == 0 ){
            $data['order_id'] = 0;
        }
    
      //  $this->log->write("razorpay_payment_id:---" . $data['razorpay_payment_id']);
       
        $data['bank_account_no'] =( isset($data['bank_account_no']) && $data['bank_account_no']) ? $data['bank_account_no'] : NULL;
        $data['method'] = (isset($data['method']) && $data['method']) ? $data['method'] : NULL;
        $data['amount'] = (isset($data['amount']) && $data['amount'] )? $data['amount'] : NULL;
        $data['currency'] = (isset($data['currency']) && $data['currency']) ? $data['currency'] : NULL;
        $data['customer_id'] =( isset($data['customer_id']) && $data['customer_id']) ? $data['customer_id'] : NULL;
        $data['email'] = (isset($data['email']) && $data['email'] )? $data['email'] : NULL;
        $data['transaction_reference_id'] = (isset($data['transaction_reference_id']) && $data['transaction_reference_id']) ? $data['transaction_reference_id'] : NULL;
        $data['narration'] = (isset($data['narration']) && $data['narration'] ) ? $data['narration'] : NULL;
        $data['transaction_date'] = (isset($data['transaction_date']) && $data['transaction_date']) ? $data['transaction_date'] : NULL;
        $data['comment'] = (isset($data['comment']) && $data['comment']) ? $data['comment'] : NULL;

        try {
            $this->db->beginTransaction();

        $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_bank_transactions SET bank_account_no = '" . $this->db->escape($data['bank_account_no'])
        . "', method = '" . $this->db->escape($data['method'])
        . "', amount = '" . $this->db->escape($data['amount'])
        . "', currency = '" . $this->db->escape($data['currency'])
        . "', customer_id = '" . $this->db->escape($data['customer_id'])
        . "', email = '" . $this->db->escape($data['email'])
        . "', transaction_reference_id = '" . $this->db->escape($data['transaction_reference_id'])
        . "', transaction_naration = '" . $this->db->escape($data['narration'])
        . "', comment = '" . $this->db->escape($data['comment'])
        . "', transaction_date = '" . $this->db->escape($data['transaction_date'])
        . "', date_added = NOW()" 
        . ", date_modified = NOW()" );

   
        $transaction_log_id = $this->db->getLastId();

        if ($transaction_log_id) {
            $this->db->query(
                "INSERT INTO " . DB_PREFIX . "apsinno_order_payment_processor SET processor_type = 'bank', transaction_log_id = " . $transaction_log_id
            );

            $ledger = $this->db->query("select transaction_id from " . DB_PREFIX . "apsinno_order_payment_processor where transaction_log_id = " . $transaction_log_id)->row;
        
         // for credit transaction
            if (isset($ledger['transaction_id']) && $ledger['transaction_id']) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_order_transaction_ledger SET transaction_id = " . $ledger['transaction_id']
                    . ", transaction_type = 'credit"
                    . "', order_id = '" . $this->db->escape($data['order_id'])
                    . "', amount = " . $this->db->escape($data['amount'])
                    . ", description = 'recieved payment from razorpay against order id: not assinged" 
                    . "'");

                $credit_auto_legder_id = $this->db->getLastId();
            }


            // for debit transaction

            if (isset($ledger['transaction_id']) && $ledger['transaction_id']) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_order_transaction_ledger SET transaction_id = " . $ledger['transaction_id']
                    . ", transaction_type = 'debit"
                    . "', order_id = '" . $this->db->escape($data['order_id'])
                    . "', amount = " . $this->db->escape($data['amount'])
                    . ", description = 'debit payment against order id: not assigned" 
                    . "'");

                $debit_auto_legder_id = $this->db->getLastId();
            }
        
        }
        $this->db->commitTransaction();
    }   catch (Exception $e) {
               
        $this->db->rollbackTransaction();
        $this->log->write( "Transaction failed: " . $e->getMessage() );          
    }


    }

    public function editPayment($data)
    {
        if( !isset($data['order_id']) || !$data['order_id'] || $data['order_id'] == 0 ){
            $data['order_id'] = 0;
        }
      
        $data['bank_account_no'] = isset($data['bank_account_no']) ? $data['bank_account_no'] : '';
        $data['method'] = isset($data['method']) ? $data['method'] : '';
        $data['amount'] = isset($data['amount']) ? $data['amount'] : '';
        $data['currency'] = isset($data['currency']) ? $data['currency'] : '';
        $data['customer_id'] = isset($data['customer_id']) ? $data['customer_id'] : '';
        $data['email'] = isset($data['email']) ? $data['email'] : '';
        $data['transaction_reference_id'] = isset($data['transaction_reference_id']) ? $data['transaction_reference_id'] : '';
        $data['narration'] = isset($data['narration']) ? $data['narration'] : '';
        $data['transaction_date'] = isset($data['transaction_date']) ? $data['transaction_date'] : '';
        $data['comment'] = isset($data['comment']) ? $data['comment'] : '';
        $data['transaction_log_id'] = isset($data['transaction_log_id']) ? $data['transaction_log_id'] : '';
       
       
        try {
            $this->db->beginTransaction();
            
        $this->db->query("UPDATE " . DB_PREFIX . "apsinno_bank_transactions SET bank_account_no = '" . $this->db->escape($data['bank_account_no'])
        . "', method = '" . $this->db->escape($data['method'])
        . "', amount = '" . $this->db->escape($data['amount'])
        . "', currency = '" . $this->db->escape($data['currency'])
        . "', customer_id = '" . $this->db->escape($data['customer_id'])
        . "', email = '" . $this->db->escape($data['email'])
        . "', transaction_reference_id = '" . $this->db->escape($data['transaction_reference_id'])
        . "', transaction_naration = '" . $this->db->escape($data['narration'])
        . "', comment = '" . $this->db->escape($data['comment'])
        . "', transaction_date = '" . $this->db->escape($data['transaction_date'])
        . "', date_added = NOW()" 
        . ", date_modified = NOW() Where transaction_log_id='" .$this->db->escape($data['transaction_log_id']) ."'" );


        $transaction_log_id =  $data['transaction_log_id'];
     
        if ($transaction_log_id) {
         
            $ledger = $this->db->query("select transaction_id from " . DB_PREFIX . "apsinno_order_payment_processor where transaction_log_id = " . $transaction_log_id)->row;
        
             $this->db->query("DELETE FROM " . DB_PREFIX . "apsinno_order_transaction_ledger where transaction_id = ". $ledger['transaction_id'] );
            
         // for credit transaction
            if (isset($ledger['transaction_id']) && $ledger['transaction_id']) {
              
                $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_order_transaction_ledger SET transaction_id = " . $ledger['transaction_id']
                    . ", transaction_type = 'credit"
                    . "', order_id = '" . $this->db->escape($data['order_id'])
                    . "', amount = " . $this->db->escape($data['amount'])
                    . ", description = 'recieved payment from razorpay against order id: not assinged" 
                    . "'");

                $credit_auto_legder_id = $this->db->getLastId();
            }


            // for debit transaction

            if (isset($ledger['transaction_id']) && $ledger['transaction_id']) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_order_transaction_ledger SET transaction_id = " . $ledger['transaction_id']
                    . ", transaction_type = 'debit"
                    . "', order_id = '" . $this->db->escape($data['order_id'])
                    . "', amount = " . $this->db->escape($data['amount'])
                    . ", description = 'debit payment against order id: not assigned" 
                    . "'");

                $debit_auto_legder_id = $this->db->getLastId();
            }
        
        }

        $this->db->commitTransaction();
    }   catch (Exception $e) {
               
        $this->db->rollbackTransaction();
        $this->log->write( "Transaction failed: " . $e->getMessage() );          
    }

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