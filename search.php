<?php
	require_once '../app/Mage.php';
	Mage::app();
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$search_string = $js['search_string'];
	if(!empty($search_string)){	
		   $productCollection = Mage::getResourceModel('catalog/product_collection')
                  ->addAttributeToSelect('*')
                  ->addAttributeToFilter('name', array('like' => '%'.$search_string.'%'))
                  ->load();
			$productDetails = array();		  
			if($productCollection->count() > 0){
				$i = 0;
				foreach ($productCollection as $product){
					 $productDetails[]  = $product->getData();
					 $productDetails[$i]['image_url']  = $product->getImageUrl();
					 $i++;
				}			
			    $json = array("error" => true, "message" => $productDetails); 
			}else{
				
			    $json = array("error" => true, "message" => "Not Found!");  
		    }
    }else{
			
		$json = array("error" => true, "message" => "Search String not blank!");
	}
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>