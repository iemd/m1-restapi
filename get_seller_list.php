<?php
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	Mage::app();
		$query = "SELECT mageuserid FROM `marketplace_userdata`";
		$result = $db_handle->numRows($query);
		if($result > 0){
			$sellerDetail = array();
			$result = $db_handle->runQuery($query);
			$i = 0;
			foreach($result as $row){
				$seller_id = $row['mageuserid'];
				$sellerDetail[$i]['seller_id'] = $row['mageuserid'];
				$customer = Mage::getModel('customer/customer')->load($seller_id);
				$sellerDetail[$i]['fullname'] = $customer->getFirstname().' '.$customer->getLastname();
				$sellerDetail[$i]['email'] = $customer->getEmail();
				if($customer->getAddresses())
				{	
			
			        $defaultBilling = $customer->getDefaultBillingAddress();
					$defaultShipping = $customer->getDefaultShippingAddress();
					if($defaultBilling && $defaultShipping){
						$street = $defaultShipping->getStreet();
						$sellerDetail[$i]['storename'] = $defaultShipping->getFirstname();
						$sellerDetail[$i]['storeid'] = $defaultShipping->getMiddlename();
						$sellerDetail[$i]['street'] = $street[0].' '.$street[1];
						$sellerDetail[$i]['state'] = $defaultShipping->getRegion();						
						$sellerDetail[$i]['country_id'] = $defaultShipping->getCountry();
						$sellerDetail[$i]['city'] = $defaultShipping->getCity();
						$sellerDetail[$i]['postcode'] = $defaultShipping->getPostcode();
						$sellerDetail[$i]['telephone'] = $defaultShipping->getTelephone();
													
					}else{
						foreach($customer->getAddresses() as $address)
					    {
							$street = $address->getStreet(); 
							//$customerAddress = $address->toArray();
							$sellerDetail[$i]['storename'] = $address->getFirstname();
						    $sellerDetail[$i]['storeid'] = $address->getMiddlename();
							$sellerDetail[$i]['street'] = $street[0].' '.$street[1];
						    $sellerDetail[$i]['state'] = $address->getRegion();		
							$sellerDetail[$i]['country_id'] = $address->getCountry();
							$sellerDetail[$i]['city'] = $address->getCity();
							$sellerDetail[$i]['postcode'] = $address->getPostcode();
							$sellerDetail[$i]['telephone'] = $address->getTelephone();
							break;
					    }
						
					}
			    }else{
					    $sellerDetail[$i]['storename'] = '';
						$sellerDetail[$i]['storeid'] = '';
					    $sellerDetail[$i]['street'] = '';
						$sellerDetail[$i]['state'] = '';	
					    $sellerDetail[$i]['country_id'] = '';
					    $sellerDetail[$i]['city'] = '';
						$sellerDetail[$i]['postcode'] = '';
						$sellerDetail[$i]['telephone'] = '';
				}	
			    $i++;			
			}			
			$json = array("error" => false, "message" => $sellerDetail);						
						
        }else{
				
			$json = array("error" => true, "message" => "Not Found!");	
				
	    }	
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>