<?php
require_once("dbcontroller.php");
$db_handle = new DBController();

// read JSon input
$jsondata=file_get_contents('php://input');
$js = json_decode($jsondata, true);

$user_id = strtolower($js['user_id']);
$pass = $js['password'];
//$regId = $js['regId']; 
//$salt = substr(md5(uniqid(rand(), true)), 0, 9);
if(!empty($user_id) && !empty($pass)){
    $query = "SELECT * FROM `users` WHERE `user_id` = '".$user_id."' ";
    $result = $db_handle->numRows($query);
    if($result == 1){
         $result = $db_handle->runQuery($query);
         foreach($result as $model){
         $id = $model['id'];
         //$login_status = $model['login_status'];
         $salt = $model['salt'];
         $password = $model['password'];
		 $pass_word = (md5($salt . md5($salt . md5($pass))));
         if($pass_word == $password ){
			 
			 $query = "SELECT id,user_id,created_at,updated_at FROM `users` WHERE `user_id` = '".$user_id."' ";
             $result = $db_handle->runQuery($query);
             $json = array("error" => false, "message" => $result);
             
             //$query = "UPDATE `users` SET `login_status` = 'Y', `registration_id` = '".$regId."' WHERE `id` = '".$id."'";
			 //$query = "UPDATE `users` SET `login_status` = 'Y', WHERE `id` = '".$id."'";
             //$result = $db_handle->updateQuery($query);
         }else{
             $json = array("error" => true, "message" => "Username or Password Invalid");
         }
         }
    }else{
        $json = array("error" => true, "message" => "Invalid Try!");
    }
        
}else{
    $json = array("error" => true, "message" => "Username and Password not blank!");
}


@mysql_close($conn);
 
/* Output header */
 
 header('Content-type: application/json, charset=UTF-8');
 echo json_encode($json);

?>