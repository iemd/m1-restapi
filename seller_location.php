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
        						
				if($customer->getAddresses())
				{	
					foreach($customer->getAddresses() as $address)
					{
						//$customerAddress = $address->toArray();
						$sellerDetail['country_id'] = $address->getCountry();
						$sellerDetail['city'] = $address->getCity();
						$sellerDetail['postcode'] = $address->getPostcode();
						
						break;
					}
			    }else{
					    $sellerDetail['country_id'] = "";
					    $sellerDetail['city'] = "";
						$sellerDetail['postcode'] = "";
						
				}					
                $json = array("error" => false, "message" => $sellerDetail); //$sellerDetail
				
            }else{
				
				$json = array("error" => false, "message" =>"Seller ID Invalid!");
				
			}		
		
	}else{
				
		    $json = array("error" => true, "message" => "Seller ID not blank!");
	}
	
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>