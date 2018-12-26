<?php
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	Mage::app();
	$collection = Mage::getModel('catalog/product')->getCollection()
							->addAttributeToSelect('*')
							->addFieldToFilter('promotion', array('eq' => 1));
    $size = $collection->getSize();
	$productDetail = [];
	if($size>0){
        $i = 0;		
		foreach($collection as $product){
					
			$productDetail[] = $product->getData();
			$productDetail[$i]['image']= $product->getImageUrl();
			$productDetail[$i]['price']= round($product->getPrice(),2);
						
		    $i++;			
		}            			
        $json = array("error" => false, "message" => $productDetail);	
		 
	}else{
		
		$json = array("error" => true, "message" => "No Product Found!");
		 
	}
	
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>