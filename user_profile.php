<?php
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$customer_id = strtolower($js['customer_id']);
	Mage::app();
	if(!empty($customer_id)){
		
			$customer = Mage::getModel('customer/customer')->load($customer_id);
			if($customer->getId()){				
			    $userProfile = array();
				$userProfile['id'] = $customer->getId();  
				$userProfile['firstname'] = $customer->getFirstname(); 
				$userProfile['lastname'] = $customer->getLastname();
				$userProfile['email'] = $customer->getEmail();
				$userProfile['created_at'] = $customer->getCreatedAt();				
				
			   			
                $json = array("error" => false, "message" => $userProfile); 
				
            }else{
				
				$json = array("error" => true, "message" =>"CustomerID Invalid!");
				
			}		
		
	}else{
				
		    $json = array("error" => true, "message" => "CustomerID not blank!");
	}
	
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>