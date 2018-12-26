<?php
	require_once '../app/Mage.php';
	Mage::app();
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$email = strtolower($js['email']);
	$pass = $js['password'];
    if(!empty($email) && !empty($pass)){
		
	   	$users = mage::getModel('customer/customer')->getCollection()
              ->addAttributeToSelect('*')
		   ->addAttributeToFilter('email',$email); 
		//$result = Mage::getModel('customer/customer')->authenticate($email, $pass);
		$result = count($users);
		if($result == 1){
		    foreach ($users as $user)
			$result = $user->getData();
			$hash = $user->getData('password_hash');
            $hashPassword = explode(':', $hash);
            $password = $hashPassword[0];
            $salt = $hashPassword[1];
			$pass_word = md5($salt.$pass);
			if($pass_word == $password ){
             
             $json = array("error" => false, "message" => $result);

            }else{
					$json = array("error" => true, "message" => "Email or Password Invalid");
            }
			
		}	
		else{
			$json = array("error" => true, "message" => "Invalid Try!");
		}
    }else{
			
		$json = array("error" => true, "message" => "Username and Password not blank!");
	}
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>