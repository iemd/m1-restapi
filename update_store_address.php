<?php
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$seller_id = strtolower($js['seller_id']);
	$storename  = $js['storename'];
	$storeid   = $js['storeid'];	
	$contact_person_name = $js['contact_person_name'];
	$company = $js['company'];
	$street = $js['street'];
	$city = $js['city'];
	$state = $js['state'];
	$country_id = $js['country_id'];
	$postcode = $js['postcode'];
	$telephone = $js['telephone'];
	$fax = $js['fax'];
	$latitude = $js['latitude'];
	$longitude = $js['longitude'];
	Mage::app();
	if(!empty($seller_id)){		

			$query = "SELECT * FROM `marketplace_userdata` WHERE `mageuserid` = '".$seller_id."' ";
		    $result = $db_handle->numRows($query);
			if($result > 0){
				$customer  = Mage::getModel('customer/customer')->load($seller_id);
				if($customer->getAddresses())
				{
					
					foreach($customer->getAddresses() as $address){
						$address_id = $address->getId();
						break;
					}  
				    $address = Mage::getModel("customer/address")->load($address_id);
					   		   $address->setCustomerId($customer->getId())
								->setFirstname($storename)
								->setMiddleName($storeid)
								->setLastname($contact_person_name)
								->setStreet($street)
								->setCity($city)
								->setRegion($state)
								->setCountryId($country_id)
								//->setRegionId('1') //state/province, only needed if the country is USA
								->setPostcode($postcode)								
								->setTelephone($telephone)
								->setFax($fax)
								->setCompany($company)
								->setGeoLatitude($latitude)	
								->setGeoLongitude($longitude)
								->setUseForDealerSearch('1')
								->setIsDefaultBilling('1')
								->setIsDefaultShipping('1')
								->setSaveInAddressBook('1');	
					try {
						$address->save();
						
						$json = array("error" => false, "message" => true);
						
					}catch( Exception $e){
						
						$json = array("error" => true, "message" => false);
					}   
				}else{
					
					$json = array("error" => true, "message" => false);
						
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