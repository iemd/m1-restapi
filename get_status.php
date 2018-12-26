<?php
	//require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//Mage::app();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$product_id = $js['product_id'];
	if(!empty($product_id)){
		
		$query = "SELECT * FROM `marketplace_product` WHERE `mageproductid` = '".$product_id."'";
		$result = $db_handle->numRows($query);
		    if($result > 0){
				$result = $db_handle->runQuery($query);
				$status = "";
				$error = "";
				foreach($result as $model){
					
					if($model['status'] == 1){$status = 'true';$error = 'false';}
					if($model['status'] == 0){$status = 'false';$error = 'true';}
					
				}	
				 $json = array("error" => $error, "message" => $status);
				
			}else{
				
			  $json = array("error" => true, "message" => "Product not found!");	
				
			}	
		
	}else{
			
		$json = array("error" => true, "message" => "ProductID not blank!");
	}
	
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>