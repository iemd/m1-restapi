<?php
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$seller_email = strtolower($js['email']);
	$password = $js['password'];
	Mage::app();
	if(!empty($seller_email)){
			
		$customer = Mage::getModel("customer/customer");
		$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
		$customer->loadByEmail($seller_email); //load customer by email id
		$seller_id = $customer->getId();
	    $query = "SELECT * FROM `marketplace_userdata` WHERE `mageuserid` = '".$seller_id."' ";
		$result = $db_handle->numRows($query);
		if($customer->getId() && ($result > 0)){
			try {
				
				$customer = Mage::getModel("customer/customer");
		        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
		        $customer->loadByEmail($seller_email); //load customer by email id
				
				$customer->setPassword($password);
				$customer->save();
				$json = array("error" => false, "message" => true);
				
			}catch(Exception $ex) {
				
				$json = array("error" => false, "message" => $ex->getMessage());
			   
            }		
		}else{
			
			$json = array("error" => true, "message" => "Seller with this email not exists!");
			
		}	
	}else{
				
		$json = array("error" => true, "message" => "Email not blank!");
	}
	
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>