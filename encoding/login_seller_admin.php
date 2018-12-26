<?php
	require_once '../../app/Mage.php';
	Mage::app();
	require_once("../dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$email = strtolower($js['username']);
	$pass = $js['password'];
	if(!empty($email) && !empty($pass)){
			$websiteId = 1;
			$customer = Mage::getModel("customer/customer");
            $customer->setWebsiteId($websiteId);
            $customer->loadByEmail($email);
 		    if($customer->getId()){
				$seller_id = $customer->getId(); 
				$query = "SELECT * FROM `marketplace_userdata` WHERE `mageuserid` = '".$seller_id."' ";
		        $result = $db_handle->numRows($query);
			    if($result > 0){
					try{			    
   
				      $seller = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->authenticate($email, $pass);
					  if($seller){
						$json = array("error" => false, "role" =>"seller", "message" => $customer->getData());  
					  }else{
						  
						$json = array("error" => true, "message" => "Email or Password Invalid!");
						  
					  }					  
		 
					}catch( Exception $e ){
					
						$json = array("error" => true, "message" => "Email or Password Invalid!");
					}	
					
				
			    }else{
					
					$json = array("error" => true, "message" => "Invalid Seller Email!");
					
				}
            }
			if(!$customer->getId()){ // Admin login if email is invalid. 
				try{
			    
                $admin = Mage::getModel('admin/user')->authenticate($email, $pass);	
				if($admin){
						$json = array("error" => false, "role" =>"admin", "message" => "Login Success!");  
			    }else{
					
					$json = array("error" => true, "message" => "Username or Password Invalid!");						  
				}		
		 
			   }catch( Exception $e ){
					
			    $json = array("error" => true, "message" => "Username or Password Invalid!");
		       }			
				
			}
    }else{
			
		$json = array("error" => true, "message" => "Email, Password and UserType not blank!");
	}
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>