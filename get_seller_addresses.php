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
        						
				if($customer->getAddresses())
				{	
			        $i=0;
					foreach($customer->getAddresses() as $address)
					{
						$sellerAddress[] = $address->getData();
						$addressId = $address->getId();
						$address = Mage::getModel('customer/address')->load($addressId);
						$customer = $address->getCustomer();
						$defaultBilling = $customer->getDefaultBillingAddress();
						$defaultShipping = $customer->getDefaultBillingAddress();
						if($defaultBilling) {
							if ($defaultBilling->getId() == $addressId) {
								
								$sellerAddress[$i]['is_default_billing'] = "1";
								
							} else {
								
								$sellerAddress[$i]['is_default_billing'] = "0";
							}
						} else {
							
							   $sellerAddress[$i]['is_default_billing'] = "0";
						}
																		
						if($defaultShipping) {
							if ($defaultShipping->getId() == $addressId) {
								
								$sellerAddress[$i]['is_default_shipping'] = "1";
								
							} else {
								
								$sellerAddress[$i]['is_default_shipping'] = "0";
							}
						} else {
							
							   $sellerAddress[$i]['is_default_shipping'] = "0";
						}
						
					$i++;	
					}
			    }else{
					
					    $json = array("error" => true, "message" => "Not Found!");
						
				}					
                $json = array("error" => false, "message" => $sellerAddress); 
				
            }else{
				
				$json = array("error" => true, "message" =>"Seller ID Invalid!");
				
			}		
		
	}else{
				
		    $json = array("error" => true, "message" => "Seller ID not blank!");
	}
	
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>