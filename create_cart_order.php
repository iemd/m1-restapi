<?php
	//require_once '../app/Mage.php';
	//Mage::app();
	//require_once("dbcontroller.php");
	//$db_handle = new DBController();
	
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$session_id = strtolower($js['session_id']);
	$cart_id = strtolower($js['cart_id']);
	if(!empty($session_id) && !empty($cart_id)){
				
		require_once 'includes/soap_config.php';
		$client = new SoapClient('http://'.$config["hostname"].'/api/v2_soap/?wsdl', array('trace'=>1));
		try{
			$result = $client->shoppingCartOrder($session_id, $cart_id,'1',null);
			
			if($result){
				$msg = "Order placed successfully! Your order id is: ".$result;
				$json = array("error" => false, "message" => $msg);				   
				
			}else{
				
				 $json = array("error" => false, "message" => "Try Again!");
			}		
        } catch (Exception $e){
				 //$response['error_code'] = $e->getCode();
				 //$response['message'] = $e->getMessage();
				 $json = array("error" => true, "message" => $e->getMessage());
		}	 
    }else{
			
		$json = array("error" => true, "message" => "SessionID and CartID not blank!");
	}
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>