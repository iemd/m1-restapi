<?php
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$seller_id = strtolower($js['seller_id']);
	Mage::app();
	if(!empty($seller_id)){		

			$query = "SELECT * FROM `marketplace_userdata` WHERE `mageuserid` = '".$seller_id."' ";
		    $result = $db_handle->numRows($query);
			if($result > 0){
				
			    $sellerAddress = array();
				
				$customer = Mage::getModel('customer/customer')->load($seller_id);
        						
				if($customer->getAddresses()){		       
					
											
						$defaultBilling = $customer->getDefaultBillingAddress();
						$defaultShipping = $customer->getDefaultShippingAddress();
						if($defaultBilling && $defaultShipping){
							
							$sellerAddress['default_billing_address'] = $defaultBilling->getData();
						    $sellerAddress['default_shipping_address'] = $defaultShipping->getData();
							$json = array("error" => false, "message" => $sellerAddress); 
							
						}else{
							
							$json = array("error" => true, "message" => "No default address found!");
							
						}					
			    }else{
					
					    $json = array("error" => true, "message" => "No address found!");
						
				}              
				
            }else{
				
				$json = array("error" => true, "message" =>"SellerID Invalid!");
				
			}		
		
	}else{
				
		    $json = array("error" => true, "message" => "SellerID not blank!");
	}
	
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>