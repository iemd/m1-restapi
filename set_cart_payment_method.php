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
	$code = strtolower($js['code']);
	if(!empty($session_id) && !empty($cart_id) && !empty($code)){
				
		require_once 'includes/soap_config.php';
		$client = new SoapClient('http://'.$config["hostname"].'/api/v2_soap/?wsdl', array('trace'=>1));
		$paymentMethod = array(
          "method" => $code
        );
		try{
			$result = $client->shoppingCartPaymentMethod($session_id, $cart_id, $paymentMethod,'1');
			
			if($result){
				
				$json = array("error" => false, "message" => $result);				   
				
			}else{
				
				 $json = array("error" => false, "message" => "Try Again!");
			}		
        } catch (Exception $e){
				 //$response['error_code'] = $e->getCode();
				 //$response['message'] = $e->getMessage();
				 $json = array("error" => true, "message" => $e->getMessage());
		}	 
    }else{
			
		$json = array("error" => true, "message" => "SessionID, CartID and MethodCode not blank!");
	}
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>