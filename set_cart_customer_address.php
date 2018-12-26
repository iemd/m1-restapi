<?php
	require_once '../app/Mage.php';
	Mage::app();
	//require_once("dbcontroller.php");
	//$db_handle = new DBController();
	
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$session_id = strtolower($js['session_id']);
	$cart_id = $js['cart_id'];
	$customer_id = $js['customer_id'];
	if(!empty($session_id) && !empty($cart_id) && !empty($customer_id)){
				
		require_once 'includes/soap_config.php';
		$client = new SoapClient('http://'.$config["hostname"].'/api/v2_soap/?wsdl', array('trace'=>1));
		
		$customer = Mage::getModel('customer/customer')->load($customer_id);
		
		if($customer->getAddresses()){
			$arrAddresses = array();
		    $billing = array();
			$shipping = array();
			$defaultBilling = $customer->getDefaultBillingAddress();
			$defaultShipping = $customer->getDefaultBillingAddress();
		    if ($defaultBilling->getId() && $defaultShipping->getId()){
				
				$billing['mode'] = 'billing';
				$billing['address_id'] = $defaultBilling->getId();
				$shipping['mode'] = 'shipping';
				$shipping['address_id'] = $defaultShipping->getId();
				$arrAddresses = array($shipping,$billing);
				try{
					$result = $client->shoppingCartCustomerAddresses($session_id, $cart_id,$arrAddresses);
					if($result){
						
						$json = array("error" => false, "message" => $result);
						   
					} else {
						
						$json = array("error" => false, "message" => "Try Again!");
						 
					}
				} catch (Exception $e){
				 //$response['error_code'] = $e->getCode();
				 //$response['message'] = $e->getMessage();
				 $json = array("error" => true, "message" => $e->getMessage());
				}			
			}else{
				
				$json = array("error" => true, "message" =>"Address not found!");
			} 		
			
		}else{
			
			$json = array("error" => true, "message" =>"Address not found!");
		}         
    }else{
			
		$json = array("error" => true, "message" => "SessionID, CartID and CustomerID not blank!");
	}
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>