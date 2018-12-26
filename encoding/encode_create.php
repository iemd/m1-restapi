<?php
require_once("dbcontroller.php");
$db_handle = new DBController();

// read JSon input
$jsondata=file_get_contents('php://input');
$js = json_decode($jsondata, true);
$tag_type = $js['tag_type'];
$tag_id = $js['tag_id'];
$jeptag_id = $js['jeptag_id'];
if(!empty($tag_type) && !empty($tag_id) && !empty($jeptag_id) ){
	
    $query = "SELECT * FROM `encode` WHERE `tag_id` = '".$tag_id."'";
    $result = $db_handle->numRows($query);
    if($result == 1){
      
		$json = array("error" => false, "message" => "TagID already exists!");
		
    }else{	
		$query = "INSERT INTO `encode` (tag_type,tag_id,jeptag_id,seller_id) VALUES ('$tag_type' ,'$tag_id', '$jeptag_id',NULL)";
		$result = $db_handle->insertQuery($query);
		$last_id = mysql_insert_id();
		$query = "SELECT * FROM `encode` WHERE `id` = '".$last_id."' ";
		$result = $db_handle->runQuery($query);
        $json = array("error" => false, "message" => $result);
    }
        
}else{
	
		$json = array("error" => true, "message" => "TagID, Tag Type and JeptagID not blank!");
}


@mysql_close($conn);
 
/* Output header */
 
 header('Content-type: application/json, charset=UTF-8');
 echo json_encode($json);


?>