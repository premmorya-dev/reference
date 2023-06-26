<?php
class ModelExtensionModuleApsExtraField extends Model {
		
	public function install() {



        $this->db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "apsinno_address_extra_fields` (
          `bluepay_hosted_order_id` INT(11) NOT NULL AUTO_INCREMENT,
          `order_id` INT(11) NOT NULL,
          `transaction_id` VARCHAR(50),
          `date_added` DATETIME NOT NULL,
          `date_modified` DATETIME NOT NULL,
          `release_status` INT(1) DEFAULT 0,
          `void_status` INT(1) DEFAULT 0,
          `rebate_status` INT(1) DEFAULT 0,
          `currency_code` CHAR(3) NOT NULL,
          `total` DECIMAL( 10, 2 ) NOT NULL,
          PRIMARY KEY (`bluepay_hosted_order_id`)
        ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "apsinno_country_code` (
          `bluepay_hosted_order_id` INT(11) NOT NULL AUTO_INCREMENT,
          `order_id` INT(11) NOT NULL,
          `transaction_id` VARCHAR(50),
          `date_added` DATETIME NOT NULL,
          `date_modified` DATETIME NOT NULL,
          `release_status` INT(1) DEFAULT 0,
          `void_status` INT(1) DEFAULT 0,
          `rebate_status` INT(1) DEFAULT 0,
          `currency_code` CHAR(3) NOT NULL,
          `total` DECIMAL( 10, 2 ) NOT NULL,
          PRIMARY KEY (`bluepay_hosted_order_id`)
        ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "apsinno_order_extra_fields` (
          `bluepay_hosted_order_id` INT(11) NOT NULL AUTO_INCREMENT,
          `order_id` INT(11) NOT NULL,
          `transaction_id` VARCHAR(50),
          `date_added` DATETIME NOT NULL,
          `date_modified` DATETIME NOT NULL,
          `release_status` INT(1) DEFAULT 0,
          `void_status` INT(1) DEFAULT 0,
          `rebate_status` INT(1) DEFAULT 0,
          `currency_code` CHAR(3) NOT NULL,
          `total` DECIMAL( 10, 2 ) NOT NULL,
          PRIMARY KEY (`bluepay_hosted_order_id`)
        ) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");



        $this->db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "apsinno_order_payment_processor` (
          `ledger_id` int NOT NULL COMMENT 'ledger auto increment id',
  `processor_type` varchar(50) NOT NULL COMMENT 'processor type ex:paytm,payu',
  `transaction_log_id` int NOT NULL COMMENT 'response id of payment processor'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;");


        $this->db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "apsinno_order_payments` (
          `order_id` int NOT NULL,
  `ledger_auto_id` int NOT NULL,
  KEY `ledger_auto_id` (`ledger_auto_id`),
  CONSTRAINT `robomart_v3_apsinno_order_payments_ibfk_1` FOREIGN KEY (`ledger_auto_id`) REFERENCES `robomart_v3_apsinno_order_transaction_ledger` (`ledger_auto_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;");



    

        $this->db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "apsinno_order_transaction_ledger` (
          `ledger_auto_id` int NOT NULL AUTO_INCREMENT COMMENT 'ledger auto increment id',
  `ledger_id` int NOT NULL DEFAULT '0',
  `transaction_type` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT 'transaction type debit or credit',
  `amount` decimal(15,2) NOT NULL COMMENT 'amount of debit or credit transaction',
  `description` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT 'narration of the transaction',
  PRIMARY KEY (`ledger_auto_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;");



        $this->db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "apsinno_razorpay_transactions` (
          `transaction_auto_id` int NOT NULL AUTO_INCREMENT,
          `razarpay_payment_id` varchar(50) NOT NULL,
          `razorpay_order_id` varchar(50) NOT NULL,
          `oc_order_id` int NOT NULL,
          `customer_id` int NOT NULL,
          `amount` int NOT NULL,
          `currency` varchar(50) NOT NULL,
          `entity` varchar(50) NOT NULL,
          `method` varchar(50) NOT NULL,
          `refund_status` varchar(50) NOT NULL,
          `captured` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `description` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `card_id` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `card_entity` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `card_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `card_last4` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `card_network` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `card_type` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `card_issuer` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `card_international` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `card_sub_type` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `card_token_iin` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `bank` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `wallet` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `vpa` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `email` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `contact` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `fee` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `tax` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `error_code` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `error_description` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `error_source` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `error_step` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `error_reason` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
          `auth_code` varchar(50) NOT NULL,
          `created_at` varchar(50) NOT NULL,
          PRIMARY KEY (`transaction_auto_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;");



	}
}