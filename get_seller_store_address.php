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
				
			   $customer = Mage::getModel('customer/customer')->load($seller_id);
        						
				if($customer->getAddresses())
				{
					$storeAddress = array();
					foreach($customer->getAddresses() as $address)
					{
                      $address_id = $address->getId(); 
					  $status = $address->getStatus();	
					  break;	
					}
					if($status == 1){
						
						$address = Mage::getModel('customer/address')->load($address_id);
						$street = $address->getStreet();
						$storeAddress['street'] = $street[0].' '.$street[1];
						$storeAddress['city'] = $address->getCity();
					    $storeAddress['state'] = $address->getRegion();
					    $storeAddress['postcode'] = $address->getPostcode();
						$storeAddress['country_id'] = $address->getCountry();
					    $storeAddress['geo_latitude'] = $address->getGeoLatitude();
					    $storeAddress['geo_longitude'] = $address->getGeoLongitude();
						$json = array("error" => false, "message" => $storeAddress); 
									
					}else{
						
						$json = array("error" => true, "message" => "No address approved!"); 
					}			 
					
			    }else{
					
					$json = array("error" => true, "message" => "Not Found!");
						
				}			
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