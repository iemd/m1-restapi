<?php
	require_once '../../app/Mage.php';
	Mage::app();
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$tag_id = $js['tag_id'];
    if(!empty($tag_id)){		
	   	$query = "SELECT * FROM `encode` WHERE `tag_id` = '".$tag_id."'";
		$result = $db_handle->numRows($query);
        if($result > 0){
			$productDetail = array();
			$result = $db_handle->runQuery($query);
			$productDetail['product_id'] = $result[0]['product_id'];
            $productCollection = Mage::getModel('catalog/product')->getCollection()
										->addAttributeToSelect('*')
										->addFieldToFilter('entity_id', $productDetail['product_id'])
										->load();
			$cnt = $productCollection->count();

			if($cnt == 1){
				foreach($productCollection as $product){
				
					$productDetail['product_url'] = $product->getProductUrl();
					$productDetail['name'] = $product->getName();
					$productDetail['latitude'] = $result[0]['chip_latitude'];
					$productDetail['longitude'] = $result[0]['chip_longitude'];
					$productDetail['address'] = $result[0]['chip_address'];
				
				}			
				$json = array("error" => false, "type" =>"1", "message" => $productDetail);				
					
			}else{
				unset($productDetail['product_id']);
				$productDetail['jeptag_id'] = $result[0]['jeptag_id'];
				$productDetail['alert'] = "This is new tag!";
				$json = array("error" => false, "type" =>"2", "message" => $productDetail);  
				
			}			
		}else{
				
			$json = array("error" => true, "message" => "Not Found!");  
		}
    }else{
			
		$json = array("error" => true, "message" => "TagID not blank!");
	}
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>