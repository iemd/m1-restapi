<?php
	require_once '../app/Mage.php';
	Mage::app();
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$seller_id = strtolower($js['seller_id']);
	if(!empty($seller_id)){
		$query = "SELECT * FROM `marketplace_product` WHERE `userid` = '".$seller_id."' ";
		$result = $db_handle->numRows($query);
		    if($result > 0){
				$result = $db_handle->runQuery($query);
				foreach($result as $model){
					$productIds[] = $model['mageproductid'];
				}	
			    $products = Mage::getModel('catalog/product')->getCollection()
						   ->addAttributeToSelect('*')
                           ->addAttributeToFilter('entity_id', array('in' => $productIds));
                //$products->getSelect()->order("find_in_set(entity_id,'".implode(',',$productIds)."')");
                $result= $products->getData();
 			
				$json = array("error" => false, "message" => $result);
            }else{
				$json = array("error" => true, "message" => "Seller ID Invalid");
            }

    }else{
			
		$json = array("error" => true, "message" => "Seller ID not blank!");
	}
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>