<?php
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$firstname = $js['firstname'];
	$lastname = $js['lastname'];
	$email = $js['email'];
	$password = $js['password'];
	Mage::app();
	if(!empty($firstname) && !empty($lastname) && !empty($email) && !empty($password)){
		
		$customer = Mage::getModel("customer/customer");
		$customer   ->setWebsiteId(1)
				->setGroupId(1)
				->setFirstname($firstname)
				->setLastname($lastname)
				->setEmail($email)
				->setPassword($password);
 
		try{
			$customer->save();
			$json = array("error" => false, "message" => "Successfully created!");
		}
		catch (Exception $e){
			$json = array("error" => true, "message" => $e->getMessage());
		}	
		
	}else{
			
		$json = array("error" => true, "message" => "Firstname, Lastname, Email and Password not blank!");
	}
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>