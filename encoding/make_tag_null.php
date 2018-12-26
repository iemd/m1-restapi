<?php
require_once("dbcontroller.php");
$db_handle = new DBController();

// read JSon input
$jsondata=file_get_contents('php://input');
$js = json_decode($jsondata, true);

$tag_id = $js['tag_id'];

if(!empty($tag_id)){
	
    $query = "SELECT * FROM `encode` WHERE `tag_id` = '".$tag_id."'";
    $result = $db_handle->numRows($query);
    if($result == 1){
			$query = "UPDATE `encode` SET `bussiness_customer_id` = null, `product_id` = null WHERE  `tag_id` = '".$tag_id."'";
			$result = $db_handle->updateQuery($query);
			if($result == 1){
			$query = "SELECT * FROM `encode` WHERE `tag_id` = '".$tag_id."'";
			$result = $db_handle->runQuery($query);	

				$json = array("error" => false, "message" => $result);
				
			}else{
								
				$json = array("error" => true, "message" => "Database Error!");  
			}
		
		
    }else{	

		$json = array("error" => true,  "message" => "Tag Not Found!"); 
    }
        
}else{
	
		$json = array("error" => true, "message" => "TagID not blank!");
}


@mysql_close($conn);
 
/* Output header */
 
 header('Content-type: application/json, charset=UTF-8');
 echo json_encode($json);


?>