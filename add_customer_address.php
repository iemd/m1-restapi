<?php
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$customer_id = strtolower($js['customer_id']);
	$firstname  = $js['firstname'];
	$lastname   = $js['lastname'];	
	$street = $js['street'];
	$city = $js['city'];
	$state = $js['state'];
	$country_id = $js['country_id'];
	$postcode = $js['postcode'];
	$telephone = $js['telephone'];
	Mage::app();
	if(!empty($customer_id)){		
			$customer =  Mage::getModel('customer/customer')->load($customer_id);
		    if($customer){
				if($customer->getAddresses())
				{
				    $address  =  Mage::getModel("customer/address");
							 $address->setCustomerId($customer->getId())
								->setFirstname($firstname)
								//->setMiddleName($customer->getMiddlename())
								->setLastname($lastname)
								->setStreet($street)
								->setCity($city)
								->setState($state)
								->setCountryId($country_id)
								//->setRegionId('1') //state/province, only needed if the country is USA
								->setPostcode($postcode)								
								->setTelephone($telephone)
								//->setFax('0038511223355')
								//->setCompany('Inchoo')								
								->setIsDefaultBilling('0')
								->setIsDefaultShipping('0')
								->setSaveInAddressBook('1');					
					     
					try {
						$address->save();
						
						$json = array("error" => false, "message" => true);
						
					}catch( Exception $e){
						
						$json = array("error" => true, "message" => false);
					}              
				}else{
					 $address  =  Mage::getModel("customer/address");
							 $address->setCustomerId($customer->getId())
								->setFirstname($firstname)
								//->setMiddleName($customer->getMiddlename())
								->setLastname($lastname)
								->setStreet($street)
								->setCity($city)
								->setState($state)
								->setCountryId($country_id)
								//->setRegionId('1') //state/province, only needed if the country is USA
								->setPostcode($postcode)								
								->setTelephone($telephone)
								//->setFax('0038511223355')
								//->setCompany('Inchoo')								
								->setIsDefaultBilling('1')
								->setIsDefaultShipping('1')
								->setSaveInAddressBook('1');					
					     
					try {
						$address->save();
						
						$json = array("error" => false, "message" => true);
						
					}catch( Exception $e){
						
						$json = array("error" => true, "message" => false);
					}				
				}
            }else{
				
				$json = array("error" => false, "message" =>"CustomerID Invalid!");
					
			}		
		
	}else{
				
		    $json = array("error" => true, "message" => "CustomerID not blank!");
	}
	
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>