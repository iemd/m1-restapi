<?php
	//require_once '../app/Mage.php';
	//Mage::app();
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$product_id = strtolower($js['product_id']);
    if(!empty($product_id)){		
	   	$query = "SELECT `jeptag_id`,`updated_at`,`created_at` FROM `encode` WHERE `product_id` = '".$product_id."'";
		$result = $db_handle->numRows($query);
		if($result > 0){
			
			    $result = $db_handle->runQuery($query);					
				$json = array("error" => false, "message" => $result);			
		}	
		else{
			
			$json = array("error" => true, "message" => "Not Found!");
		}
    }else{
			
		$json = array("error" => true, "message" => "Product ID not blank!");
	}
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>