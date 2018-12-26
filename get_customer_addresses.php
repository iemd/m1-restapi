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
			    $customerAddress = array();
				       						
				if($customer->getAddresses())
				{	
			        $i=0;
					foreach($customer->getAddresses() as $address)
					{
						$customerAddress[] = $address->getData();
						$addressId = $address->getId();
						$address = Mage::getModel('customer/address')->load($addressId);
						$customer = $address->getCustomer();
						$defaultBilling = $customer->getDefaultBillingAddress();
						$defaultShipping = $customer->getDefaultBillingAddress();
						if($defaultBilling) {
							if ($defaultBilling->getId() == $addressId) {
								
								$customerAddress[$i]['is_default_billing'] = "1";
								
							} else {
								
								$customerAddress[$i]['is_default_billing'] = "0";
							}
						} else {
							
							   $customerAddress[$i]['is_default_billing'] = "0";
						}
																		
						if($defaultShipping) {
							if ($defaultShipping->getId() == $addressId) {
								
								$customerAddress[$i]['is_default_shipping'] = "1";
								
							} else {
								
								$customerAddress[$i]['is_default_shipping'] = "0";
							}
						} else {
							
							   $customerAddress[$i]['is_default_shipping'] = "0";
						}
						
					$i++;	
					}
			    }else{
					
					    $json = array("error" => true, "message" => "Not Found!");
						
				}					
                $json = array("error" => false, "message" => $customerAddress); 
				
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