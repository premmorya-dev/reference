<?php

require_once DIR_SYSTEM.'library/razorpay/razorpay-sdk/Razorpay.php';
require_once DIR_SYSTEM.'library/razorpay/razorpay-lib/createwebhook.php';


use Razorpay\Api\Api;
use Razorpay\Api\Errors;

class ModelApsRazorpayApsRazorpay extends Model {

	public function addPayment($data) {
        $this->log->write("razorpay_payment_id:---" . $data['razorpay_payment_id']);
       if( isset($data['razorpay_payment_id']) && $data['razorpay_payment_id'] ){
        
        $api = new Api($this->config->get('payment_razorpay_key_id'), $this->config->get('payment_razorpay_key_secret'));

        $payments = (array)$api->payment->fetch($data['razorpay_payment_id']);

        if( $payments ){
                foreach($payments as $payment ){
                $card_detail = [];
                $auth_detail = [];
                $notes = [];
                    if($payment['card']){
                        foreach((array)$payment['card'] as $card ){ 
                            $card_detail = $card ;
                        }

                    }

                    if($payment['acquirer_data']){
                        foreach((array)$payment['acquirer_data'] as $auth ){ 
                            $auth_detail = $auth;
                        }

                    }    

                    if($payment['notes']){
                        foreach((array)$payment['notes'] as $note ){ 
                            $notes = $note;
                        }

                    }    
              
        $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_razorpay_transactions SET razarpay_payment_id = '" . $this->db->escape($data['razorpay_payment_id']) 
        . "', razorpay_order_id = '" . $this->db->escape($payment['order_id'])
        . "', oc_order_id = '" . $this->db->escape($data['order_id'])
        . "', customer_id = " . $this->db->escape($data['customer_id']) 
        . ", amount = " . $this->db->escape($data['total']) 
        . ", currency = '" . $this->db->escape($payment['currency']) 
        . "', entity = '" . $this->db->escape($payment['entity']) 
        . "', method = '" . $this->db->escape($payment['method']) 
        . "', refund_status = '" . $this->db->escape($payment['refund_status']) 
        . "', captured = '" . $this->db->escape($payment['captured']) 
        . "', description = '" . $this->db->escape($payment['description']) 
        . "', card_id = '" . $this->db->escape($card_detail['card_id']) 
        . "', card_entity = '" . $this->db->escape($card_detail['card_entity']) 
        . "', card_name = '" . $this->db->escape($card_detail['card_name']) 
        . "', card_last4 = '" . $this->db->escape($card_detail['card_last4']) 
        . "', card_network = '" . $this->db->escape($card_detail['card_network']) 
        . "', card_type = '" . $this->db->escape($card_detail['card_type']) 
        . "', card_issuer = '" . $this->db->escape($card_detail['card_issuer']) 
        . "', card_international = '" . $this->db->escape($card_detail['card_international']) 
        . "', card_sub_type = '" . $this->db->escape($card_detail['card_sub_type']) 
        . "', card_token_iin = '" . $this->db->escape($card_detail['card_token_iin'])       
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

                if($transaction_log_id  ){
                    $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_order_payment_processor SET processor_type = 'razorpay', transaction_log_id = " . $transaction_log_id             
                    );
                }
              
                
                $ledger = $this->db->query("select transaction_id from " . DB_PREFIX . "apsinno_order_payment_processor where transaction_log_id = " . $transaction_log_id )->row;
                
              $this->log->write( "test:  " . $ledger['transaction_id']);
               // for credit transaction
                if(isset($ledger['transaction_id']) && $ledger['transaction_id']){
                    $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_order_transaction_ledger SET transaction_id = " . $ledger['transaction_id'] 
                    . ", transaction_type = 'credit" 
                    . "', order_id = '" . $this->db->escape($data['order_id'])              
                    . "', amount = " . $this->db->escape($data['total'])  
                    . ", description = 'recieved payment from razorpay against order id: ".$this->db->escape($data['order_id'])
                    . "'");
    
                    $credit_auto_legder_id = $this->db->getLastId();
                }
               

               // for debit transaction

               if(isset($ledger['transaction_id']) && $ledger['transaction_id']){
                $this->db->query("INSERT INTO " . DB_PREFIX . "apsinno_order_transaction_ledger SET transaction_id = " . $ledger['transaction_id'] 
                . ", transaction_type = 'debit"    
                . "', order_id = '" . $this->db->escape($data['order_id'])           
                . "', amount = " . $this->db->escape($data['total'])  
                . ", description = 'debit payment against order id: ".$this->db->escape($data['order_id'])
                . "'");

                $debit_auto_legder_id = $this->db->getLastId();
               }
                



        }

      

       }
		
	}


    public function confirmPayment($order_id) {

      
        $ledger = $this->db->query("SELECT * FROM " . DB_PREFIX . "apsinno_order_transaction_ledger WHERE order_id=". $order_id . " And transaction_type='credit' ")->row;
     
        if($ledger ){
            $ledger_id = $ledger['ledger_id'] ? $ledger['ledger_id'] : 0 ;
            $credit_total =  $ledger['amount'] ;
           
            $debit_total = $this->db->query("SELECT sum(amount) as total  FROM " . DB_PREFIX . "apsinno_order_transaction_ledger WHERE order_id=". $order_id . " AND transaction_type = 'debit'" )->row;
        
            $debit_total = $debit_total['total'] ?  $debit_total['total']   : 0 ;
          
    
           if( ( $credit_total ==  $debit_total) && $credit_total != 0 && $debit_total !=0 ){
               return true;
           }else{
               return false;
           }
        }else{
            return false;
        }
       
        
    }

} 