<?php
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$product_id = strtolower($js['product_id']);
	
	if(!empty($product_id)){
		 Mage::app();
		 // Register a secure admin environment
         Mage::register('isSecureArea', 1);
		 $product = Mage::getModel('catalog/product')->load($product_id);
		 //$query = "DELETE FROM `catalog_product_entity` WHERE entity_id =  '".$product_id."'";
		 //$result = $db_handle->deleteQuery($query);
		if($product){
			try {
				$product->delete();
				$json = array("error" => false, "message" => true);
			} catch (Exception $e) {
				$json = array("error" => true, "message" => false);
			}
	    } else {
		    $json = array("error" => true, "message" => "Product ID not exists!");
	    }		  
		
	}else{
		
		$json = array("error" => true, "message" => "Product ID not blank!");
	}		
		
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>