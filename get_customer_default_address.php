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
                					
			$customerAddress = array();
			$customer = Mage::getModel('customer/customer')->load($customer_id);
        	if($customer->getId()){					
				if($customer->getAddresses()){		       
																
						$defaultBilling = $customer->getDefaultBillingAddress();
						$defaultShipping = $customer->getDefaultShippingAddress();
					if($defaultBilling && $defaultShipping){
						$customerAddress['default_billing_address'] = $defaultBilling->getData();
						$customerAddress['default_shipping_address'] = $defaultShipping->getData();
						$json = array("error" => false, "message" => $customerAddress); 
					}else{
						
						$json = array("error" => true, "message" => "No default address found!");
					}
			    }else{
					
					    $json = array("error" => true, "message" => "No address found!");
						
				}					
                
				
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