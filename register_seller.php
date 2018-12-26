<?php
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$firstname = $js['firstname'];
	$middlename = $js['middlename'];
	$lastname = $js['lastname'];
	$email = $js['email'];
	$storename = ''; //$js['storename']
	$storeid = '';   //$js['storeid']
	$password = $js['password'];
	Mage::app();
	if(!empty($firstname) && !empty($lastname) && !empty($email) && !empty($password)){
		$seller_id = '';
		$fullname = $firstname.' '.$middlename.' '.$lastname;
		$customer = Mage::getModel("customer/customer");
		$customer   ->setWebsiteId(1)
				->setGroupId(1)
				->setFirstname($firstname)
				->setMiddlename($middlename)
				->setLastname($lastname)
				->setEmail($email)
				->setPassword($password);
 
		try{
			$customer->save();
			$seller_id = $customer->getId();
			if(!empty($seller_id)){
			 $query = "INSERT INTO `marketplace_userdata` (`wantpartner`, `paymentsource`, `partnerstatus`, `mageuserid`, `twitterid`, `facebookid`, `bannerpic`, `profileurl`, `shoptitle`, `logopic`, `complocality`, `countrypic`, `compdesi`, `meta_keyword`, `meta_description`, `backgroundth`, `contactnumber`, `returnpolicy`, `shippingpolicy`, `others_info`, `gplus_id`, `youtube_id`, `vimeo_id`, `instagram_id`, `pinterest_id`, `moleskine_id`, `tw_active`, `fb_active`, `gplus_active`, `youtube_active`, `vimeo_active`, `instagram_active`, `pinterest_active`, `moleskine_active`) VALUES ('1', '', 'Seller','".$seller_id."', '', '', '', '".$seller_id."', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '0', '0', '0', '0', '0')";	
			 $result = $db_handle->updateQuery($query);
			 if($result == 1){
				$customer =  Mage::getModel('customer/customer')->load($seller_id);
				$address  =  Mage::getModel("customer/address");
							 $address->setCustomerId($customer->getId())
								->setFirstname($storename)
								->setMiddlename($storeid)
								->setLastname($fullname)
								->setStreet()
								->setCity()
								->setState()
								->setCountryId()
								//->setRegionId('1') //state/province, only needed if the country is USA
								->setPostcode()								
								->setTelephone()
								->setGeoLatitude()
								->setGeoLongitude()
								//->setFax('0038511223355')
								//->setCompany('Inchoo')								
								->setIsDefaultBilling('1')
								->setIsDefaultShipping('1')
								->setSaveInAddressBook('1');
                try{								
				    $address->save();
					$json = array("error" => false, "message" => "Successfully registered!"); 
				}catch (Exception $ex){
					
					$json = array("error" => true, "message" => $ex->getMessage()); //$ex->getMessage()
				}	
			 }
		   }		
		}
		catch (Exception $e){
			$json = array("error" => true, "message" => $e->getMessage()); //$e->getMessage()
		}	
		
	}else{
			
		$json = array("error" => true, "message" => "Firstname, Lastname, Email and Password not blank!");
	}
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>