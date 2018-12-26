<?php
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$seller_id = strtolower($js['seller_id']);
	$firstname  = $js['firstname'];
	$lastname   = $js['lastname'];	
	//$username = strtolower($js['username']);
	//$email = strtolower($js['email']);
	$country_id = $js['country_id'];
	$city = $js['city'];
	$postcode = $js['postcode'];
	$telephone = $js['telephone'];
	Mage::app();
	if(!empty($seller_id)){		

			$query = "SELECT * FROM `marketplace_userdata` WHERE `mageuserid` = '".$seller_id."' ";
		    $result = $db_handle->numRows($query);
			if($result > 0){
								
			    $sellerDetail = array();
				//$customerAddress = array();
				$customer = Mage::getModel('customer/customer')->load($seller_id);
				
				$customer->setFirstname($firstname);
                $customer->setLastname($lastname);	
				//$customer->setEmail($email);
				try {
					
				    $customer->save();
                    //$address = Mage::getModel("customer/address");
                    //$address = Mage::getModel('customer/address')->load($addressId);					
					if($customer->getAddresses())
				    {	
				        foreach($customer->getAddresses() as $address){
						$addressId = $address->getId();
						$address = Mage::getModel('customer/address')->load($addressId);
						$address->setCountryId($country_id)
						        ->setFirstname($customer->getFirstname())
								//->setMiddleName($customer->getMiddlename())
								->setLastname($customer->getLastname())
								//->setRegionId('1') //state/province, only needed if the country is USA
								->setPostcode($postcode)
								->setCity($city)
								->setTelephone($telephone)
								//->setFax('0038511223355')
								//->setCompany('Inchoo')
								//->setStreet('Kersov')
								->setIsDefaultBilling('1')
								->setIsDefaultShipping('1')
								->setSaveInAddressBook('1');					
					            $address->save();	
						 break;
						}
					}else{
						$address = Mage::getModel("customer/address");
						$address->setCustomerId($customer->getId())
								->setFirstname($customer->getFirstname())
								//->setMiddleName($customer->getMiddlename())
								->setLastname($customer->getLastname())
								->setCountryId($country_id)
								//->setRegionId('1') //state/province, only needed if the country is USA
								->setPostcode($postcode)
								->setCity($city)
								->setTelephone($telephone)
								//->setFax('0038511223355')
								//->setCompany('Inchoo')
								//->setStreet('Kersov')
								->setIsDefaultBilling('1')
								->setIsDefaultShipping('1')
								->setSaveInAddressBook('1');					
					            $address->save();	
					
				    }
					
					$json = array("error" => false, "message" => true);
					
				}catch( Exception $e){
					
					$json = array("error" => true, "message" => false);
				}	
				
								
                 //$sellerDetail
				
            }else{
				
				$json = array("error" => false, "message" =>"Seller ID Invalid!");
				
			}		
		
	}else{
				
		    $json = array("error" => true, "message" => "SellerID not blank!");
	}
	
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>