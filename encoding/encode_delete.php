<?php
require_once("dbcontroller.php");
$db_handle = new DBController();

// read JSon input
$jsondata=file_get_contents('php://input');
$js = json_decode($jsondata, true);
//$tag_type = $js['tag_type'];
$tag_id = $js['tag_id'];
$jeptag_id = $js['jeptag_id'];
if(!empty($jeptag_id) && !empty($tag_id)){
    $query = "SELECT * FROM `encode` WHERE `jeptag_id` = '".$jeptag_id."' AND `tag_id` = '".$tag_id."' ";
    $result = $db_handle->numRows($query);
    if($result == 1){

			 $query = "DELETE FROM `encode` WHERE `jeptag_id` = '".$jeptag_id."' AND `tag_id` = '".$tag_id."'";
			 $result = $db_handle->deleteQuery($query);
			 if($result){
				 
				  $json = array("error" => false, "message" => "Deleted Successfully!");
				 
			 }
			 else{
				 
				  $json = array("error" => false, "message" => "Database Error!");
			 }		 
			
	}else{
		
        $json = array("error" => true, "message" => "Jeptag ID not exists!");
    }
        
}else{
		$json = array("error" => true, "message" => "Jeptag ID and Tag ID not blank!");
}


@mysql_close($conn);
 
/* Output header */
 
 header('Content-type: application/json, charset=UTF-8');
 echo json_encode($json);


?>