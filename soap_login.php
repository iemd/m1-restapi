<?php
	//require_once '../app/Mage.php';
	//Mage::app();
	//require_once("dbcontroller.php");
	//$db_handle = new DBController();
	//read JSon input
	//$jsondata=file_get_contents('php://input');
	//$js = json_decode($jsondata, true);
		
	    $result=array();
		require_once 'includes/soap_config.php';
		
	    $proxy = new SoapClient('http://'.$config["hostname"].'/api/v2_soap/?wsdl', array('trace'=>1));
        $sessionId = $proxy->login($config["login"], $config["password"]);
		$result['session_id'] = $sessionId;
        $shoppingCartIncrementId = $proxy->shoppingCartCreate($sessionId, '1');
		$result['cart_id'] = $shoppingCartIncrementId;
        if (!empty($sessionId) && !empty($shoppingCartIncrementId)) {
			
               $json = array("error" => false, "message" => $result);
			   
        } else {
			
			 $json = array("error" => false, "message" => "Soap Error!");
             
        }		

    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>