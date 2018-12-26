<?php
	require_once '../app/Mage.php';
	Mage::app();
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	require_once ('vendor/autoload.php');
    use \Statickidz\GoogleTranslate;
	$trans = new GoogleTranslate();
    $source = 'en';
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$seller_id = strtolower($js['seller_id']);
	$target = strtolower($js['lang']);
	if(empty($target)){$target = 'en';}
	if(!empty($seller_id)){
		$query = "SELECT * FROM `marketplace_userdata` WHERE `mageuserid` = '".$seller_id."' ";
		$result = $db_handle->numRows($query);
		    if($result > 0){
				$query = "SELECT * FROM `marketplace_product` WHERE `userid` = '".$seller_id."' ";
				$result = $db_handle->runQuery($query);
				foreach($result as $model){
					$productIds[] = $model['mageproductid'];
					$status[] =  $model['status'];
				}	
			    $products = Mage::getModel('catalog/product')->getCollection()
						   ->addAttributeToSelect('*')
                           ->addAttributeToFilter('entity_id', array('in' => $productIds));
			   if($products->count()>0){		   
                //$products->getSelect()->order("find_in_set(entity_id,'".implode(',',$productIds)."')");
				$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($products);
				$result = array();
				$i=0;
				foreach($products as $product){
					$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
					$res[$i]['id'] = $product->getId();
					$res[$i]['sku'] = $product->getSku();
					$res[$i]['name'] = $trans->translate($source, $target,$product->getName());
					$res[$i]['qty'] = round($stock->getQty(), 0);
					$res[$i]['latitude'] = $product->getLatitude();
					$res[$i]['longitude'] = $product->getLongitude();
					$res[$i]['product_url'] = $product->getProductUrl();
					$catIds =  $product->getCategoryIds();
										
					$catCollection = Mage::getResourceModel('catalog/category_collection')
                     ->addAttributeToSelect('*')
                     ->addAttributeToFilter('entity_id', $catIds[0])
                     ->addIsActiveFilter();
					//$categories = array();
                    //$j=0;
					foreach($catCollection as $cat){
					//$categories[$j]['category_id'] = $cat->getId();
					//$categories[$j]['name'] = $cat->getName();
					//$j++;
					$cat_name = $trans->translate($source, $target,$cat->getName()); 
					}
					$res[$i]['category'] = $cat_name;
					$res[$i]['image'] = $product->getImageUrl();
					$query = "SELECT * FROM `marketplace_product` WHERE `mageproductid` = '".$product->getId()."'";
					$result = $db_handle->runQuery($query);
					foreach($result as $model){
						$res[$i]['approved'] = $model['status'];	
					}					
					$i++;
				}
                //$result= $products->getData();
 			
				$json = array("error" => false, "message" => $res);
			   }else{
				   
				   $json = array("error" => true, "message" => "Product not found!");
			   }
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