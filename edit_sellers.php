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
				
			    $sellerDetail = array();
				//$customerAddress = array();
				$customer = Mage::getModel('customer/customer')->load($seller_id);
        		$sellerDetail['fullname'] = $customer->getFirstname().' '.$customer->getLastname();
				$sellerDetail['email'] = $customer->getEmail();
				
				if($customer->getAddresses())
				{	
			
			        $defaultBilling = $customer->getDefaultBillingAddress();
					$defaultShipping = $customer->getDefaultShippingAddress();
					if($defaultBilling && $defaultShipping){
						$street = $defaultShipping->getStreet();
						$sellerDetail['storename'] = $defaultShipping->getFirstname();
						$sellerDetail['storeid'] = $defaultShipping->getMiddlename();
						$sellerDetail['street'] = $street[0].' '.$street[1];
						$sellerDetail['state'] = $defaultShipping->getRegion();						
						$sellerDetail['country_id'] = $defaultShipping->getCountry();
						$sellerDetail['city'] = $defaultShipping->getCity();
						$sellerDetail['postcode'] = $defaultShipping->getPostcode();
						$sellerDetail['telephone'] = $defaultShipping->getTelephone();
													
					}else{
						foreach($customer->getAddresses() as $address)
					    {
							$street = $address->getStreet(); 
							//$customerAddress = $address->toArray();
							$sellerDetail['storename'] = $address->getFirstname();
						    $sellerDetail['storeid'] = $address->getMiddlename();
							$sellerDetail['street'] = $street[0].' '.$street[1];
						    $sellerDetail['state'] = $address->getRegion();		
							$sellerDetail['country_id'] = $address->getCountry();
							$sellerDetail['city'] = $address->getCity();
							$sellerDetail['postcode'] = $address->getPostcode();
							$sellerDetail['telephone'] = $address->getTelephone();
							break;
					    }
						
					}
			    }else{
					    $sellerDetail['storename'] = '';
						$sellerDetail['storeid'] = '';
					    $sellerDetail['street'] = '';
						$sellerDetail['state'] = '';	
					    $sellerDetail['country_id'] = '';
					    $sellerDetail['city'] = '';
						$sellerDetail['postcode'] = '';
						$sellerDetail['telephone'] = '';
				}					
                $json = array("error" => false, "message" => $sellerDetail); //$sellerDetail
				
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