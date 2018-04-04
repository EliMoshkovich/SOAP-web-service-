<?php
	//require_once('./service.php');
	
	$client = new SoapClient('http://localhost:6543/service.wsdl', array('cache_wsdl' => WSDL_CACHE_NONE, 'encoding'=>'UTF-8','trace' => 1));
	$url = $_SERVER['REQUEST_URI'];
	$parts = preg_split("#/#", $url); // Geting the url parts /coupons and /coupon_id
	$length = count($parts);

	// Now, I'm checking if the url contains the 'coupun id'. if it does - calling the function which handle the 'POST' method,
	// Else  - calling to get all coupons.
	if($length == 2 && $parts[1] == 'coupons' || $parts[1] == 'coupons.php'){
		try{
			$response = $client->__soapCall("getCoupons",array());
			echo $response;
		}
		catch(Exception $e){
			echo $e;
			echo "REQUEST:\n" . $client->__getLastRequest() . "\n";
			echo "RESPONSE:\n" . $client->__getLastResponse() . "\n";
		}
	}
	else if($length == 3){
		// Length is 3, thus the url contains a copund_id. 
		try{
			$id = $parts[2];
			$response = $client->__soapCall("setClippedCoupon",array($id));
			echo $response;
		}
		catch(Exception $e){
			echo "REQUEST:\n" . $client->__getLastRequest() . "\n";
			echo "RESPONSE:\n" . $client->__getLastResponse() . "\n";
		}
	}
	else{
		echo "No such route";
	}

	
	/* $functions = $client->__getFunctions ();
	echo $functions[0]; */
	

?>
