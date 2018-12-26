<?php
require_once("dbcontroller.php");
$db_handle = new DBController();

// read JSon input
$jsondata=file_get_contents('php://input');
$js = json_decode($jsondata, true);
    $query = "SELECT * FROM `encode`";
    $result = $db_handle->numRows($query);
    if($result == 1){
         $result = $db_handle->runQuery($query);
    
             
             $json = array("error" => false, "message" => $result);

         }else{
              $json = array("error" => true, "message" => "Invalid Try!");
         }
  


@mysql_close($conn);
 
/* Output header */
 
 header('Content-type: application/json, charset=UTF-8');
 echo json_encode($json);

?>