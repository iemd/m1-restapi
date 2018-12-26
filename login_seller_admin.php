<?php
	require_once '../../app/Mage.php';
	Mage::app();
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$email = strtolower($js['email']);
	$pass = $js['password'];
	$user_role = strtolower($js['user_role']);
    if(!empty($email) && !empty($pass) && !empty($user_role)){
		if($user_role == 'seller'){
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
					  $json = array("error" => false, "message" => $seller);
		 
					}catch( Exception $e ){
					
						$json = array("error" => true, "message" => "Email or Password Invalid!");
					}	
					
				
			    }else{
					
					$json = array("error" => true, "message" => "Invalid Seller Email!");
					
				}
            }else{
				
				$json = array("error" => true, "message" => "Invalid Email!");
			}	
			
		}
		if($user_role == 'admin'){
			try{
			    
                $admin = Mage::getModel('admin/user')->authenticate($email, $pass);			
			    $json = array("error" => false, "message" => $admin);
		 
			}catch( Exception $e ){
					
			    $json = array("error" => true, "message" => "Email or Password Invalid!");
		    }		
		}	
		
    }else{
			
		$json = array("error" => true, "message" => "Email and Password not blank!");
	}
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>