<?php
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	require_once ('vendor/autoload.php');
    use \Statickidz\GoogleTranslate;
	$trans = new GoogleTranslate();
    $source = 'en';
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$product_id = strtolower($js['product_id']);
	$target = strtolower($js['lang']);
	if(empty($target)){$target = 'en';}
	Mage::app();
	if(!empty($product_id)){		
		
			$productDetail = array();
			$model = Mage::getModel('catalog/product');
			$product = $model->load($product_id);
			$productDetail['name'] = $trans->translate($source, $target,$product->getName());
			$categoryIds = $product->getCategoryIds();
			$subcategory = Mage::getModel('catalog/category')->load($categoryIds[0])->getParentId();
			$productDetail['category'] = "$subcategory"; 
			$productDetail['subcategory'] = $categoryIds[0];
			$productDetail['description'] = $trans->translate($source, $target,$product->getDescription());
			$productDetail['weight'] = $product->getWeight();
			$productDetail['price'] = $product->getPrice();
			$productDetail['qty'] = round($product->getStockItem()->getQty(),0);
			$gallery_images = $product->getMediaGalleryImages();
			$items = array();
			foreach($gallery_images as $g_image) {
				$items[] = $g_image['url'];
			}
			$productDetail['image'] = $items;
            			
            $json = array("error" => false, "message" => $productDetail);			
		
	}else{
				
		    $json = array("error" => true, "message" => "Product ID not blank!");
	}
	
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>