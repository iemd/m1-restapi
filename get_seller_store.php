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
				
			    $storeAddress = array();
				
				$customer = Mage::getModel('customer/customer')->load($seller_id);
        						
				if($customer->getAddresses())
				{	
			        $storeAddress['storename'] = '';
					$storeAddress['storeid'] = '';
					$storeAddress['contact_person_name'] = '';
					foreach($customer->getAddresses() as $address)
					{

						$storeAddress['storename'] = $address->getFirstname();
						$storeAddress['storeid'] = $address->getMiddlename();
						$storeAddress['contact_person_name'] = $address->getLastname();

					  break;	
					}
					$json = array("error" => false, "message" => $storeAddress); 
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