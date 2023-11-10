<?php

// Start the session
session_start();
date_default_timezone_set('Asia/Kolkata');
define('VERSION', '3.0.3.8');
$cron_url = "/home/robomart/web/robomart.com/public_html";
$current_directory = getcwd();

$currentTimestamp = time();
$formattedDateTime = date('Y_m_d-H_i_s', $currentTimestamp);
 // Create the full file path
//  $cron_running_start_time = date("d-m-Y") ;
 $path = $current_directory . '/cron_logs/' . 'zoho_product_regular_sync_and_update-cron-running-log_' . $formattedDateTime  . '.txt';
 $log_file = fopen($path, "a+") or die("Unable to open file!");
 $text = "\n cron start time is: " .  date("l jS \of F Y H:i:s A") ;
 fwrite($log_file, $text );
 fclose($log_file);

require_once($cron_url . '/config.php');
require_once(DIR_SYSTEM . 'startup.php');

// Registry
$registry = new Registry();

// Config
$config = new Config();
$config->load('default');
$config->load('catalog');
$registry->set('config', $config);

// Log
$log = new Log($config->get('error_filename'));
$registry->set('log', $log);

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);


// Language
$language = new Language($config->get('language_directory'));
$registry->set('language', $language);

// Config Autoload
if ($config->has('config_autoload')) {
  foreach ($config->get('config_autoload') as $value) {
    $loader->config($value);
  }
}

// Library Autoload
if ($config->has('library_autoload')) {
  foreach ($config->get('library_autoload') as $value) {
    $loader->library($value);
  }
}




$db = new DB($config->get('db_engine'), $config->get('db_hostname'), $config->get('db_username'), $config->get('db_password'), $config->get('db_database'), $config->get('db_port'));

$registry->set('db', $db);


$store_id = 0;

// Settings
$query = $db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' OR store_id = '" . (int) $store_id . "' ORDER BY store_id ASC");

foreach ($query->rows as $result) {
  if (!$result['serialized']) {
    $config->set($result['key'], $result['value']);
  } else {
    $config->set($result['key'], json_decode($result['value'], true));
  }
}




if( isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] ){

  if( ( strpos( $_SERVER['argv'][1] ,"--product_id=") !== false  )   || ( strpos( $_SERVER['argv'][1] ,"--sku=") !== false  )){
      $argument = explode("=",$_SERVER['argv'][1] );
      
    
        if( isset($argument[0]) && $argument[0] == '--product_id'   ){
          if( !isset($argument[1]) || !is_numeric($argument[1] )  ){
            die("Product id should be numeric value. Please check ");
          }
          $products_data = $db->query("SELECT p.product_id,pd.description, pef.hsn_or_sac_zoho ,pef.hsn_or_sac_zoho ,pef.composite_item_zoho ,pef.initial_stock_rate_zoho  ,pef.inventory_account_id_zoho  ,pef.is_taxable_zoho,pef.item_type_zoho  ,pef.purchase_category ,pef.product_type_zoho  ,pef.sale_account_id_zoho ,pef.purchase_account_id_zoho  ,pef.tax_id_inter_state_zoho,pef.tax_id_intra_state_zoho  ,pef.unit_zoho  ,pef.contry_of_origin_id  ,pef.short_name,pef.short_description  ,zp.zoho_product_id, p.sku, p.image, pd.name, p.model, p.price, p.quantity, zp.last_synchronized_date,zp.last_synchronized_status,p.status , pef.zoho_inventory_category_id, pef.warehouse_location_code,pef.product_inventory_status_id
          FROM robomart_v3_zoho_product  zp 
          LEFT JOIN robomart_v3_product p ON (p.product_id = zp.product_id) 
          LEFT JOIN robomart_v3_product_description pd ON (p.product_id = pd.product_id) 
          LEFT JOIN robomart_v3_apsinno_product_extra_fields pef ON (pef.product_id = p.product_id) 
          WHERE pd.language_id = '1' AND (p.product_id=".  $argument[1]  .") GROUP BY p.product_id ORDER BY p.product_id ASC");

          if( $products_data->num_rows < 1 ){
            die("Product id not found. \n");
          } 

        }else  if( isset($argument[0]) && $argument[0] == '--sku'   ){
          $products_data = $db->query("SELECT p.product_id,pd.description, pef.hsn_or_sac_zoho ,pef.hsn_or_sac_zoho ,pef.composite_item_zoho ,pef.initial_stock_rate_zoho  ,pef.inventory_account_id_zoho  ,pef.is_taxable_zoho,pef.item_type_zoho  ,pef.purchase_category ,pef.product_type_zoho  ,pef.sale_account_id_zoho ,pef.purchase_account_id_zoho  ,pef.tax_id_inter_state_zoho,pef.tax_id_intra_state_zoho  ,pef.unit_zoho  ,pef.contry_of_origin_id  ,pef.short_name,pef.short_description  ,zp.zoho_product_id, p.sku, p.image, pd.name, p.model, p.price, p.quantity, zp.last_synchronized_date,zp.last_synchronized_status,p.status , pef.zoho_inventory_category_id, pef.warehouse_location_code,pef.product_inventory_status_id
          FROM robomart_v3_zoho_product  zp 
          LEFT JOIN robomart_v3_product p ON (p.product_id = zp.product_id) 
          LEFT JOIN robomart_v3_product_description pd ON (p.product_id = pd.product_id) 
          LEFT JOIN robomart_v3_apsinno_product_extra_fields pef ON (pef.product_id = p.product_id) 
          WHERE pd.language_id = '1' AND (p.sku='".  $argument[1]  ."') GROUP BY p.product_id ORDER BY p.product_id ASC");

          if( $products_data->num_rows < 1 ){
            die("SKU not found. \n");
          } 
        }
  }else{
    die('Argument Error: Format not recognized. Correct format is : --product_id=value or --sku=value' . "\n");
  }

 
 
}else{ 
        $products_data = $db->query("SELECT p.product_id,pd.description, pef.hsn_or_sac_zoho ,pef.hsn_or_sac_zoho ,pef.composite_item_zoho ,pef.initial_stock_rate_zoho  ,pef.inventory_account_id_zoho  ,pef.is_taxable_zoho,pef.item_type_zoho  ,pef.purchase_category ,pef.product_type_zoho  ,pef.sale_account_id_zoho ,pef.purchase_account_id_zoho  ,pef.tax_id_inter_state_zoho,pef.tax_id_intra_state_zoho  ,pef.unit_zoho  ,pef.contry_of_origin_id  ,pef.short_name,pef.short_description  ,zp.zoho_product_id, p.sku, p.image, pd.name, p.model, p.price, p.quantity, zp.last_synchronized_date,zp.last_synchronized_status,p.status , pef.zoho_inventory_category_id, pef.warehouse_location_code,pef.product_inventory_status_id
        FROM robomart_v3_zoho_product  zp 
        LEFT JOIN robomart_v3_product p ON (p.product_id = zp.product_id) 
        LEFT JOIN robomart_v3_product_description pd ON (p.product_id = pd.product_id) 
        LEFT JOIN robomart_v3_apsinno_product_extra_fields pef ON (pef.product_id = p.product_id) 
        WHERE pd.language_id = '1' AND (zp.last_synchronized_status = 'pending' OR zp.last_synchronized_status = 'resync-pending') GROUP BY p.product_id ORDER BY p.product_id ASC");

        if( $products_data->num_rows < 1 ){
          die("There is no products in cronjob. Please assign products to cronjob to execute. \n");
        } 

      }



$products = $products_data->rows;
//  print_r($products);die;

syncProductsToZoho($products, $config, $db, $log);
echo "\n";

$path = $current_directory . '/cron_logs/' . 'zoho_product_regular_sync_and_update-cron-running-log_' . $formattedDateTime  . '.txt';
$log_file = fopen($path, "a+") or die("Unable to open file!");
$text = "\n cron end time is: " .  date("l jS \of F Y H:i:s A") ;
fwrite($log_file, $text );
fclose($log_file);
// print_r($products);die;
// var_dump($config->get('config_autoload'));die;
// print_r($products);die;


function syncProductsToZoho($products, $config, $db, $log)
{
  $custom_field  =  $db->query("SELECT * FROM " . DB_PREFIX . "apsinno_zoho_custom_field_name_to_field_id")->rows;
  $count = 0;
  // print_r($config->get('module_opc_zoho_domain'));die;
  
  try {
    if ($products) {
      foreach ($products as $product) {
           if(!$config->get('module_opc_zoho_composite_product_sync')){
            if ( isset($product['composite_item_zoho']) && $product['composite_item_zoho'] == 'yes') {
              date_default_timezone_set('Asia/Kolkata');
              $dir = dirname(__FILE__);
          
              // Create the full file path
              $path = $dir . '/cron_logs/' . 'zoho_product_regular_sync_and_update-log_' . date("d-m-Y") . '.txt';
              $log_file = fopen($path, "a+") or die("Unable to open file!");
          
            
              $text = "\n" . date("l jS \of F Y H:i:s A") . " | product_id: " . $product['product_id']. " | status: failed | this is composite product so its cannot be synchronize please check zoho configuration settings for synchronization";
            
              print_r("\n product_id: " . $product['product_id']. " | status: failed | this is composite product so its cannot be synchronize please check zoho configuration settings for synchronization");

              fwrite($log_file, $text);
              fclose($log_file);

              continue;
             
            }
           }
      

        if (!isset($product['short_description'])) {
          $product['short_description'] = '';
        }
        if (!isset($product['upc'])) {
          $product['upc'] = '';
        }
        if (!isset($product['ean'])) {
          $product['ean'] = '';
        }
        if (!isset($product['isbn'])) {
          $product['isbn'] = '';
        }
        if (!isset($product['mpn'])) {
          $product['mpn'] = '';
        }
        if (!isset($product['purchase_description'])) {
          $product['purchase_description'] = '';
        }



					if ($product['status'] == 1) {
						$product_status = 'active';
					}else{
						$product_status = 'inactive';
					}
					
					if (isset($product['warehouse_location_code']) && $product['warehouse_location_code']) {
						$warehouse_code = $product['warehouse_location_code'];
					}else{
						$warehouse_code = '';
					}
					if (isset($product['product_inventory_status_id']) && $product['product_inventory_status_id'] == '1' ) {
						$product_inventory_status_id = 'Yes';
					}else{
						$product_inventory_status_id = 'No';
					}

					if (isset($product['zoho_inventory_category_id']) && $product['zoho_inventory_category_id'] ) {
						$Catagory_id =  $db->query("SELECT zoho_inventory_category_name FROM " . DB_PREFIX . "apsinno_zoho_inventory_category WHERE zoho_inventory_category_id ='". $product['zoho_inventory_category_id'] ."'" )->row;
					    if(isset($Catagory_id['zoho_inventory_category_name'])){
							$zoho_catagory_id = $Catagory_id['zoho_inventory_category_name'];
						}
					}else{
						    $zoho_catagory_id = '';
					}


          if(isset($product['description']) && $product['description'] ){
            $product_description = substr(strip_tags(html_entity_decode($product['description'])), 0, 3599);
          }else{
            $product_description = '';
          }

          if(isset($product['is_taxable_zoho']) && is_null( $product['is_taxable_zoho'] ) ){
						$is_taxable_zoho = false;
					  }else{
						  $is_taxable_zoho = true;
					  }
          

        $data = array(
          "group_name" => substr($product['name'], 0, 99),
          "product_id" => $product['product_id'],
          "description" =>'',
          // "description" => substr(strip_tags(html_entity_decode($product['short_description'])), 0, 6000),
          "name" => $product['name'],
          "rate" => $product['price'],
          "sku" => $product['sku'],
          "upc" => $product['upc'],
          "ean" => $product['ean'],
          "isbn" => $product['isbn'],
          "part_number" => $product['mpn'],


          "is_taxable" => $is_taxable_zoho,
          "unit" => $product['unit_zoho'],
          "item_type" => "inventory",
          "product_type" => $product['product_type_zoho'],
          "inventory_account_id" => $product['inventory_account_id_zoho'],
          "hsn_or_sac" => $product['hsn_or_sac_zoho'],
          "account_id" => $product['sale_account_id_zoho'],
          "purchase_account_id" => $product['purchase_account_id_zoho'],
          "item_tax_preferences" => [
            [
              "tax_id" => $product['tax_id_inter_state_zoho'],
              "tax_specification" => "inter"
            ],
            [
              "tax_id" => $product['tax_id_intra_state_zoho'],
              "tax_specification" => "intra"
            ]
          ],
          "custom_fields" => [
            [
              "field_id" => $custom_field[0]['custom_field_api_id_for_zoho'] ,
              "customfield_id" => $custom_field[0]['custom_field_api_id_for_zoho'] ,
              "index" => 1,
              "value" => $custom_field[0]['value'] 
            ],
            [
              "field_id" => $custom_field[1]['custom_field_api_id_for_zoho'] ,
              "customfield_id" => $custom_field[1]['custom_field_api_id_for_zoho'] ,
              "index" => 2,
              "value" => $custom_field[1]['value'] 
            ],
            [
              "field_id" => $custom_field[2]['custom_field_api_id_for_zoho'] ,
              "customfield_id" => $custom_field[2]['custom_field_api_id_for_zoho'] ,
              "index" => 3,
              "value" =>substr(strip_tags(html_entity_decode($product['short_name'])), 0, 6000)
             //  "value" =>''
            ],
            [
              "field_id" => $custom_field[3]['custom_field_api_id_for_zoho'] ,
              "customfield_id" => $custom_field[3]['custom_field_api_id_for_zoho'] ,
              "index" => 4,
              "value" => $product['purchase_category']
            ],
            [
              "field_id" => $custom_field[4]['custom_field_api_id_for_zoho'] ,
              "customfield_id" => $custom_field[4]['custom_field_api_id_for_zoho'] ,
              "index" => 5,
              // "value" => substr(strip_tags(html_entity_decode($product['short_description'])), 0, 6000)
              "value" =>''
            ],
            [
              "field_id" => $custom_field[5]['custom_field_api_id_for_zoho'] ,
              "customfield_id" => $custom_field[5]['custom_field_api_id_for_zoho'] ,
              "index" => 5,
              "value" =>$product['product_id']
            ],
            [
              "field_id" => $custom_field[6]['custom_field_api_id_for_zoho'] ,
              "customfield_id" => $custom_field[6]['custom_field_api_id_for_zoho'] ,
              "index" => 6,
              "value" => $product_status
            ],
            [
              "field_id" => $custom_field[7]['custom_field_api_id_for_zoho'] ,
              "customfield_id" => $custom_field[7]['custom_field_api_id_for_zoho'] ,
              "index" => 7,
              "value" => $zoho_catagory_id
            ],
            [
              "field_id" => $custom_field[8]['custom_field_api_id_for_zoho'] ,
              "customfield_id" => $custom_field[8]['custom_field_api_id_for_zoho'] ,
              "index" => 8,
              "value" =>	$warehouse_code
            ],
            [
              "field_id" => $custom_field[9]['custom_field_api_id_for_zoho'] ,
              "customfield_id" => $custom_field[9]['custom_field_api_id_for_zoho'] ,
              "index" => 9,
              "value" => $product_inventory_status_id
            ],
            [
              "field_id" => $custom_field[10]['custom_field_api_id_for_zoho'] ,
              "customfield_id" => $custom_field[10]['custom_field_api_id_for_zoho'] ,
              "index" => 10,
              "value" =>''
            ]
            
          ],
          // "purchase_description" => substr(strip_tags(html_entity_decode($product['short_description'])), 0, 2000),
          "purchase_description" => '',
        );

        $zoho_product = getSyncProduct($product['product_id'], $db);
        //    print_r($zoho_product );die;
        if (isset($zoho_product['zoho_product_id']) && $zoho_product['zoho_product_id']) {
          $url = "https://inventory.zoho" . $config->get('module_opc_zoho_domain') . "/api/v1/items/" . $zoho_product['zoho_product_id'];

          $method = "PUT";
        } else {
          $url = "https://inventory.zoho" . $config->get('module_opc_zoho_domain') . "/api/v1/items";

          $method = "POST";

          $data['initial_stock'] = 0;

          $data['initial_stock_rate'] = 0;
        }
       
        $response = execute_curl($url, $method, $data, '', $config, $db, $log);
        // print_r($response);die;
        if ($response && isset($response['item']['item_id']) && $response['item']['item_id']) {
          saveSyncProduct($product['product_id'], $response['item']['item_id'], $config, $db, $log);

          $count++;

          execute_curl("https://inventory.zoho" . $config->get('module_opc_zoho_domain') . "/api/v1/items/" . $response['item']['item_id'] . "/image", "DELETE", $data, '', $config, $db, $log);
          if( isset( $product['image'] ) && $product['image'] ){
            $data = array(
              'item_id' => $response['item']['item_id'],
              'image' => new CURLFile(
                DIR_IMAGE . $product['image'],
                'application/octet-string'
              ),
               );
               
              
               $sql = "SELECT * FROM " . DB_PREFIX . "apsinno_zoho_access_token WHERE timestamp >  SUBTIME(CURRENT_TIMESTAMP(), (Select expires_in from  " . DB_PREFIX . "apsinno_zoho_access_token order by `timestamp` desc limit 1) ) ORDER  BY `timestamp` DESC limit 1";
  
               $result = $db->query($sql)->row;
  
               if (isset($result['access_token']) && $result['access_token']) {
                 $curl = curl_init();
  
                 curl_setopt_array($curl, array(
                 CURLOPT_URL => 'https://inventory.zoho' . $config->get("module_opc_zoho_domain") . '/api/v1/items/' . $response['item']['item_id'] . '/images',
                 CURLOPT_RETURNTRANSFER => true,
                 CURLOPT_CUSTOMREQUEST => 'POST',
                 CURLOPT_SSL_VERIFYHOST => false,
                 CURLOPT_SSL_VERIFYPEER => false,
                 CURLOPT_POSTFIELDS => $data,
                   CURLOPT_HTTPHEADER => array(
                   "Authorization: Zoho-oauthtoken ". $result['access_token'],
                   ),
                 ));
  
                $image_response =  curl_exec($curl);
                // $log->write('image_response');
                // $log->write($image_response);
                curl_close($curl);
               }
          }


        


        } else {
          if (isset($response['code']) && isset($response['message'])) {
            $log->write("Zoho Inventory Product Error. OpenCart Product ID:" . $product['product_id'] . ". Error Code: " . $response['code'] . " Error Message: " . $response['message']);
          }
        }
      }
    }
  } catch (\Exception $e) {

  }

  return $count;
}


function getSyncProduct($product_id = 0, $db)
{
  return $db->query("SELECT * FROM " . DB_PREFIX . "zoho_product WHERE product_id = " . (int) $product_id)->row;
}

function execute_curl($url = '', $method = '', $data = array(), $params = '', $config, $db, $log)
{

  if ($url && $method) {

 
    $db->query("DELETE FROM " . DB_PREFIX . "apsinno_zoho_access_token WHERE id NOT IN ( SELECT id FROM ( SELECT id FROM " . DB_PREFIX . "apsinno_zoho_access_token ORDER BY id DESC LIMIT 5 ) AS last_five_records )");
    
    $sql = "SELECT * FROM " . DB_PREFIX . "apsinno_zoho_access_token WHERE timestamp >  SUBTIME(CURRENT_TIMESTAMP(), (Select expires_in from  " . DB_PREFIX . "apsinno_zoho_access_token order by `timestamp` desc limit 1) ) ORDER  BY `timestamp` DESC limit 1";

    $result = $db->query($sql)->row;
    $response = [];

    if (isset($result['access_token']) && $result['access_token']) {
      $response = pushProductToZoho($url, $method, $data, $params, $config, $db, $log, $result['access_token']);
      $_SESSION['token'] = $result['access_token'];
    } else { 
      $new_token = updateAccessToken($config, $log);

      if( isset($new_token['access_token']) && $new_token['access_token'] ){
        $sql = "INSERT INTO " . DB_PREFIX . "apsinno_zoho_access_token SET access_token ='" . $new_token['access_token'] . "', expires_in='" . $new_token['expires_in'] . "', timestamp = NOW()";

        $db->query($sql);

      $response = pushProductToZoho($url, $method, $data, $params, $config, $db, $log, $new_token['access_token']);
      $_SESSION['token'] = $new_token['access_token'];

      }

     


    }

    if (isset($response['code']) && $response['code'] != 0) {
      $new_token = updateAccessToken($config, $log);

      if( isset($new_token['access_token']) && $new_token['access_token'] ){ 
        $sql = "INSERT INTO " . DB_PREFIX . "apsinno_zoho_access_token SET access_token ='" . $new_token['access_token'] . "', expires_in='" . $new_token['expires_in'] . "', timestamp = NOW()";

        $db->query($sql);
  
        $response = pushProductToZoho($url, $method, $data, $params, $config, $db, $log, $new_token['access_token']);
        $_SESSION['token'] = $new_token['access_token'];
      }
     

    }

    if ($method == 'PUT') {
      $request_type = "UPDATE";
    } else if ($method == 'POST') {
      $request_type = "INSERT";
    } else if ($method == 'DELETE') {
      $request_type = "DELETE";
    } else {
      $request_type = "Unknown";
    }
    $zoho_product = getSyncProduct($data['product_id'], $db);

    if (isset($response['item']['item_id']) && $response['item']['item_id']) {
      $text = "\n" . date("l jS \of F Y H:i:s A") . " | product_id: " . $data['product_id'] . " | zoho_item_id: " . $response['item']['item_id'] . " | request type: " . $request_type . " | status: success | " . $response['message'];
      print_r("\n product_id: " . $data['product_id'] . " | zoho_item_id: " . $response['item']['item_id'] . " | request type: " . $request_type . " | status: success | " . $response['message']);
    } else {
      if($request_type == 'DELETE' ){
        $text = "\n" . date("l jS \of F Y H:i:s A") . " | product_id: " . $data['product_id'] . " | zoho_item_id: " . $zoho_product['zoho_product_id'] . " | request type: " . $request_type . " | status: success | " . $response['message'];
        print_r("\n product_id: " . $data['product_id'] . " | zoho_item_id: " . $zoho_product['zoho_product_id'] . " | request type: " . $request_type . " | status: success | " . $response['message']);
      }else{ 
       

        if($response['code'] == 1001 ){
          $text = "\n" . date("l jS \of F Y H:i:s A") . " | product_id: " . $data['product_id'] . " | zoho_item_id: " . $zoho_product['zoho_product_id'] . " | request type: " . $request_type . " | status: failed | " . $response['message'];
          print_r("\n product_id: " . $data['product_id'] . " | zoho_item_id: " . $zoho_product['zoho_product_id'] . " | request type: " . $request_type . " | status: failed | " . $response['message'] . " Please check the product name or SKU ");
        }else{
          $text = "\n" . date("l jS \of F Y H:i:s A") . " | product_id: " . $data['product_id'] . " | zoho_item_id: " . $zoho_product['zoho_product_id'] . " | request type: " . $request_type . " | status: failed | " . $response['message'];
          print_r("\n product_id: " . $data['product_id'] . " | zoho_item_id: " . $zoho_product['zoho_product_id'] . " | request type: " . $request_type . " | status: failed | " . $response['message'] );
        }
       
      }
    
    }

    date_default_timezone_set('Asia/Kolkata');
    $dir = dirname(__FILE__);

    // Create the full file path
    $path = $dir . '/cron_logs/' . 'zoho_product_regular_sync_and_update-log_' . date("d-m-Y") . '.txt';
    $log_file = fopen($path, "a+") or die("Unable to open file!");

    fwrite($log_file, $text);
    fclose($log_file);

    // print_r($response);die;

    $log->write("zoho api response:");
    if (isset($response['message'])) {
      $log->write($response['message']);
    }


    if (!$response['error']) {
      return $response;
    } else {
      return false;
    }
    //  } access token end 

  }

  return false;
}

function pushProductToZoho($url = '', $method = '', $data = array(), $params = '', $config, $db, $log, $token)
{
  $url = $url . '?organization_id=' . $config->get('module_opc_zoho_organization_id') . $params;
  $curl = curl_init();

  if ($data) {
    $post_data = array(
      'JSONString' => json_encode($data),
    
    );
    
 
      // print_r($method);die;
    curl_setopt_array(
      $curl,
      array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS => $post_data,
        CURLOPT_HTTPHEADER => array(
          "Authorization: Zoho-oauthtoken " . $token,
        ),
      )
    );
  } else {
    curl_setopt_array(
      $curl,
      array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => array(
          "Authorization: Zoho-oauthtoken " . $token,
        ),
      )
    );
  }

  $response = json_decode(curl_exec($curl), 1);

    // print_r($response);die;
  $err = curl_error($curl);
  $response['error'] = $err;
  curl_close($curl);


  return $response;
}

function updateAccessToken($config, $log)
{
  $curl = curl_init();   
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => "https://accounts.zoho" . $config->get('module_opc_zoho_domain') . "/oauth/v2/token?refresh_token=" . $config->get('module_opc_zoho_refresh_token') . "&client_id=" . $config->get('module_opc_zoho_client_id') . "&client_secret=" . $config->get('module_opc_zoho_client_secret') . "&grant_type=refresh_token",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
    )
  );

  $result = json_decode(curl_exec($curl), 1);

  $err = curl_error($curl);

  curl_close($curl);

  if (!$err) {
    return $result;
  }
}


function saveSyncProduct($product_id = 0, $zoho_product_id = 0, $config, $db, $log)
{

  if ($product_id) {
    //  $db->query("DELETE FROM " . DB_PREFIX . "zoho_product WHERE product_id = " . (int) $product_id);

    $db->query("UPDATE " . DB_PREFIX . "zoho_product SET product_id = " . (int) $product_id . ", zoho_product_id = '" . $zoho_product_id . "', last_synchronized_date=NOW(), last_synchronized_status='synced' where product_id=" . (int) $product_id);
  }
}


function getProductOptions($product_id, $config, $db, $log)
{
  $product_option_data = array();

  $product_option_query = $db->query("SELECT * FROM `" . DB_PREFIX . "product_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int) $product_id . "' AND od.language_id = '" . (int) $config->get('config_language_id') . "' AND o.type IN ('radio', 'checkbox', 'select')");

  foreach ($product_option_query->rows as $product_option) {
    $product_option_value_data = array();

    $product_option_value_query = $db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON(pov.option_value_id = ov.option_value_id) WHERE pov.product_option_id = '" . (int) $product_option['product_option_id'] . "' ORDER BY ov.sort_order ASC");

    foreach ($product_option_value_query->rows as $product_option_value) {
      $product_option_value_data[] = array(
        'product_option_value_id' => $product_option_value['product_option_value_id'],
        'option_value_id' => $product_option_value['option_value_id'],
        'quantity' => $product_option_value['quantity'],
        'subtract' => $product_option_value['subtract'],
        'price' => $product_option_value['price'],
        'price_prefix' => $product_option_value['price_prefix'],
        'points' => $product_option_value['points'],
        'points_prefix' => $product_option_value['points_prefix'],
        'weight' => $product_option_value['weight'],
        'weight_prefix' => $product_option_value['weight_prefix']
      );
    }

    $product_option_data[] = array(
      'product_option_id' => $product_option['product_option_id'],
      'product_option_value' => $product_option_value_data,
      'option_id' => $product_option['option_id'],
      'name' => $product_option['name'],
      'type' => $product_option['type'],
      'value' => $product_option['value'],
      'required' => $product_option['required']
    );
  }

  return $product_option_data;
}


function getSyncProductGroup($product_id = 0, $config, $db, $log)
{
  return $db->query("SELECT * FROM " . DB_PREFIX . "zoho_product_group WHERE product_id = " . (int) $product_id)->row;
}


function getSyncProductOption($product_id = 0, $product_option_id = 0, $config, $db, $log)
{
  return $db->query("SELECT * FROM " . DB_PREFIX . "zoho_product_option WHERE product_id = " . (int) $product_id . " AND oc_product_option_id = " . (int) $product_option_id)->row;
}

function saveSyncProductGroup($product_id = 0, $zoho_group_id = 0, $config, $db, $log)
{

  if ($zoho_group_id) {
    $db->query("DELETE FROM " . DB_PREFIX . "zoho_product_group WHERE product_id = " . (int) $product_id);

    $db->query("INSERT INTO " . DB_PREFIX . "zoho_product_group SET product_id = " . (int) $product_id . ", zoho_group_id = '" . $zoho_group_id . "'");
  }
}

function saveSyncProductOption($product_id = 0, $product_option_id = 0, $zoho_product_id = 0, $config, $db, $log)
{
  if ($zoho_product_id) {
    $db->query("DELETE FROM " . DB_PREFIX . "zoho_product_option WHERE product_id = " . (int) $product_id . " AND oc_product_option_id = " . (int) $product_option_id);

    $db->query("INSERT INTO " . DB_PREFIX . "zoho_product_option SET product_id = " . (int) $product_id . ", oc_product_option_id = " . (int) $product_option_id . ", zoho_product_id = '" . $zoho_product_id . "'");
  }
}