<?php

if( isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] ){
	$item = $_SERVER['argv'][1];
}else{
	$item = die("Please enter zoho_item_id");
}

 //token
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://accounts.zoho.com/oauth/v2/token?refresh_token=1000.b70a6decf4cd648f310eb16949ff0110.d780e64de1c0f1a0939bd2eef427dd88&client_id=1000.SP92LWZQWF5HX70R4WW1H6O8AX7K2A&client_secret=c61f6cb57cdd0e6d05679b02f5209fdbba2bbd4d48&grant_type=refresh_token",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_SSL_VERIFYPEER => false,
  )
  );
  
  $result = json_decode(curl_exec($curl), 1);

  $err = curl_error($curl);

  curl_close($curl);
  
  if( !isset($result['access_token']) ){
     print_r("\n Getting the access token error. please wait couple of secound its resume automatically.");
	 sleep(100);
	 exit();
  }

//Stock Zero update
  $item_no = $item ;
  $curl = curl_init();
  $url = 'https://www.zohoapis.com/inventory/v1/items/'.$item_no.'?organization_id=708609529';
  $token = "Authorization: Zoho-oauthtoken " . $result['access_token'];
  $method = "PUT";


  curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_CUSTOMREQUEST => $method,
	  CURLOPT_SSL_VERIFYHOST => false,
	  CURLOPT_SSL_VERIFYPEER => false,	
	  CURLOPT_POSTFIELDS => '{"initial_stock":"0","initial_stock_rate":"0"}' ,
	  CURLOPT_HTTPHEADER => array(			
		 $token,
		 'content-type: application/json',
	  ),
	)
	);
    
	$response = json_decode(curl_exec($curl), 1);

	$log = fopen("stock.log", "a") or die("Unable to open file!");
	date_default_timezone_set('Asia/Kolkata'); 
	
	if( isset($response['item']['item_id']) && $response['item']['item_id']){
		$text = "\n".date("l jS \of F Y H:i:s A") . " : zoho_item_id: ".$item_no ." | response: ".  $response['message'];
		print_r("\n item no: " . $item_no. " | status: success | ".$response['message']);
	}else{
		$text = "\n".date("l jS \of F Y H:i:s A") . " : zoho_item_id: ".$item_no."| response: ".  $response['message'];
		print_r("\n item no: " . $item_no. " | status:failed | ".  $response['message']);
	}
	
	
	fwrite($log, $text );
	fclose($log);
	
	// Delete

	//sleep(1);

	//$item_no = $item ;
	//$curl = curl_init();
	//$url = 'https://www.zohoapis.com/inventory/v1/items/'.$item_no.'?organization_id=708609529';
	//$token = "Authorization: Zoho-oauthtoken " . $result['access_token'];
	//$method = "DELETE";
  
  
	//curl_setopt_array($curl, array(
	//	CURLOPT_URL => $url,
	//	CURLOPT_RETURNTRANSFER => true,
	//	CURLOPT_CUSTOMREQUEST => $method,
	//	CURLOPT_SSL_VERIFYHOST => false,
	//	CURLOPT_SSL_VERIFYPEER => false,		
	//	CURLOPT_HTTPHEADER => array(			
	//	   $token,		
	//	),
	//  )
	//  );
	  
	//  $response = json_decode(curl_exec($curl), 1);
  
	//  $log = fopen("stock.log", "a") or die("Unable to open file!");
	//  date_default_timezone_set('Asia/Kolkata'); 
  
	//  $text = "\n".date("l jS \of F Y H:i:s A") . " : zoho_item_id: ".$item_no."| DELETE | response: ".  $response['message'];
	//  print_r("\n item no: " . $item_no. " | status: ".  $response['message']);
  
	  
	//  fwrite($log, $text );
	//  fclose($log);

	  


	//sleep(3);
	

