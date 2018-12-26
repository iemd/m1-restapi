<?php
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	Mage::app();
	//read JSon input
	$jsondata = file_get_contents('php://input');
	$brand_ids = json_decode($jsondata, true);
	if(count($brand_ids)>0){
     	$products = Mage::getModel('catalog/product')->getCollection()
						   ->addFieldToSelect('name','price')
                           ->addAttributeToFilter('brand', array('in' => $brand_ids));
		if($products->count()>0){
            $productDetail = array();			
			foreach($products as $product){
				$productDetail[] = $product->getData();	 
			    $json = array("error" => false, "message" => $productDetail);		
			}				   
		}else{
				   
			$json = array("error" => true, "message" => "No product found!");    
		}	   
	}else{
			
		$json = array("error" => true, "message" => "No brand selected!");
	}
	
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>