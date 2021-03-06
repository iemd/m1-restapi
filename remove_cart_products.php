<?php
	//require_once '../app/Mage.php';
	//Mage::app();
	//require_once("dbcontroller.php");
	//$db_handle = new DBController();
	
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$session_id = strtolower($js['session_id']);
	$cart_id = $js['cart_id'];
	$arrProducts = $js['products'];
	if(!empty($session_id) && !empty($cart_id) && !empty($arrProducts)){
		
		require_once 'includes/soap_config.php';
		
			$proxy = new SoapClient('http://'.$config["hostname"].'/api/v2_soap/?wsdl', array('trace'=>1));
		try{	
			$result = $proxy->shoppingCartProductRemove($session_id, $cart_id,$arrProducts, '1');
			if($result){
				
				   $json = array("error" => false, "message" => "Product successfully removed!");
				   
			} else {
				
				 $json = array("error" => false, "message" => "Try again!");
				 
			}		
        } catch (Exception $e){
			 //$response['error_code'] = $e->getCode();
			 //$response['message'] = $e->getMessage();
			 $json = array("error" => true, "message" => $e->getMessage());
		}   
    }else{
			
		$json = array("error" => true, "message" => "SessionID, CartID and Products not blank!");
	}
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>