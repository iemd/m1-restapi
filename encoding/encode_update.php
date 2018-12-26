<?php
require_once("dbcontroller.php");
$db_handle = new DBController();

// read JSon input
$jsondata=file_get_contents('php://input');
$js = json_decode($jsondata, true);
//$tag_type = $js['tag_type'];
$tag_id = $js['tag_id'];
$jeptag_id = $js['jeptag_id'];
$seller_id = $js['seller_id'];

if(!empty($jeptag_id)){
    $query = "SELECT * FROM `encode` WHERE `jeptag_id` = '".$jeptag_id."' ";
    $result = $db_handle->numRows($query);
    if($result == 1){
        $result = $db_handle->runQuery($query);
        foreach($result as $model){
			 $id = $model['id'];
			 $query = "UPDATE `encode` SET `seller_id` = '".$seller_id."' WHERE `jeptag_id` = '".$jeptag_id."' AND `tag_id` = '".$tag_id."'";
			 $result = $db_handle->updateQuery($query);
		}
		$query = "SELECT * FROM `encode` WHERE `jeptag_id` = '".$jeptag_id."' ";
        $result = $db_handle->runQuery($query);
		$json = array("error" => false, "message" => $result);
    }else{
        $json = array("error" => true, "message" => "Jeptag ID not exists!");
    }
        
}else{
		$json = array("error" => true, "message" => "Jeptag ID not blank!");
}


@mysql_close($conn);
 
/* Output header */
 
 header('Content-type: application/json, charset=UTF-8');
 echo json_encode($json);


?>