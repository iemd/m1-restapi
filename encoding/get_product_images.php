<?php
	require_once '../../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	Mage::app();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$tag_id = $js['tag_id'];
	if(!empty($tag_id)){		
		$query = "SELECT * FROM `encode` WHERE `tag_id` = '".$tag_id."'";
		$result = $db_handle->numRows($query);
		    if($result == 1){
				$result = $db_handle->runQuery($query);
				$bussiness_customer_id = "";
				$product_id = "";
				foreach($result as $model){

					$bussiness_customer_id = $model['bussiness_customer_id'];
					$product_id = $model['product_id'];
				}	
				$product = Mage::getModel('catalog/product')->load($product_id);
				$customer = Mage::getModel('customer/customer')->load($bussiness_customer_id);
				$productDetail = array();
				if($customer->getId() && $product->getId()){
					$gallery_images = $product->getMediaGalleryImages();
					$items = array();
					foreach($gallery_images as $g_image){
							$items[] = $g_image['url'];
					}
					$productDetail['images'] = $items;	
					$json = array("error" => false, "message" => $productDetail);	
				}else{
					
					$json = array("error" => true, "message" => "TagID not verified by any product!");
					
				} 				
				
			}else{
				
			  $json = array("error" => true, "message" => "TagID not found!");	
				
			}	
		
	}else{
			
		$json = array("error" => true, "message" => "TagID not blank!");
	}
	
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>