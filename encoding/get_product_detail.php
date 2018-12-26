<?php
	require_once '../../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	require_once ('../vendor/autoload.php');
    use \Statickidz\GoogleTranslate;
	$trans = new GoogleTranslate();
	$source = 'en';
	Mage::app();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$tag_id = $js['tag_id'];
	$target = strtolower($js['lang']);
	if(empty($target)){$target = 'en';}
	if(!empty($tag_id)){		
		$query = "SELECT * FROM `encode` WHERE `tag_id` = '".$tag_id."'";
		$result = $db_handle->numRows($query);
		    if($result == 1){
				$result = $db_handle->runQuery($query);
				$bussiness_customer_id = "";
				$product_id = "";
				$chip_latitude = '';
				$chip_longitude = '';
				$updated_at = '';
				$created_at = '';
				
				foreach($result as $model){

					$bussiness_customer_id = $model['bussiness_customer_id'];
					$product_id = $model['product_id'];
					$chip_latitude = $model['chip_latitude'];
					$chip_longitude = $model['chip_longitude'];
					$updated_at = $model['updated_at'];
					$created_at = $model['created_at'];
					
				}	
				$product = Mage::getModel('catalog/product')->load($product_id);
				//$customer = Mage::getModel('customer/customer')->load($bussiness_customer_id);
				$productDetail = array();
				//if($customer->getId() && $product->getId()){
				if($product->getId()){
					
					$productDetail['id'] = $product->getId();	
					$productDetail['name'] = $trans->translate($source, $target, $product->getName());
					$productDetail['description'] = $trans->translate($source, $target, $product->getDescription());
					$productDetail['price'] = $product->getPrice();
				    $productDetail['weight'] = $product->getWeight();
					$categoryIds = $product->getCategoryIds();
					$cat = Mage::getModel('catalog/category')->load($categoryIds[0])->getParentId();
					$category = Mage::getModel('catalog/category')->load($cat)->getName();
					$subcategory = Mage::getModel('catalog/category')->load($categoryIds[0])->getName();
					$productDetail['category'] = $trans->translate($source, $target, $category); 
					$productDetail['subcategory'] = $trans->translate($source, $target, $subcategory);
					$productDetail['latitude'] = $chip_latitude;
					$productDetail['longitude'] = $chip_longitude;
					$productDetail['updated_at'] = $updated_at;
					$productDetail['created_at'] = $created_at;
					
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