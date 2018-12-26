<?php
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$seller_id = strtolower($js['seller_id']);
	$address_id = strtolower($js['address_id']);
	Mage::app();
	if(!empty($seller_id) && !empty($address_id)){		

			$query = "SELECT * FROM `marketplace_userdata` WHERE `mageuserid` = '".$seller_id."' ";
		    $result = $db_handle->numRows($query);
			if($result > 0){			   
				
				$customer = Mage::getModel('customer/customer')->load($seller_id);
        						
				if($customer->getAddresses())
				{	
					foreach($customer->getAddresses() as $address)
					{
						if($address->getId() == $address_id){
							
							$customAddress = Mage::getModel('customer/address')->load($address_id);
							$customAddress->setIsDefaultShipping('1');
							$customAddress->setIsDefaultBilling('1');
						
							try {
									$customAddress->save();
									$json = array("error" => false, "message" => true);
								}
								catch (Exception $ex) {
									
									$json = array("error" => true, "message" => false);
							    }
												
						}
					
					}
			    }else{
					
					    $json = array("error" => true, "message" => "No Address Found!");
						
				}					
               				
            }else{
				
				$json = array("error" => true, "message" =>"SellerID Invalid!");
				
			}		
		
	}else{
				
		    $json = array("error" => true, "message" => "SellerID and AddressID not blank!");
	}
	
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>