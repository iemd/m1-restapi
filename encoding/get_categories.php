<?php
	require_once '../app/Mage.php';
	//Mage::app();
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	//$seller_id = strtolower($js['seller_id']);
	$rootcatId= Mage::app()->getStore()->getRootCategoryId();
	$categories = Mage::getModel('catalog/category')->getCollection()
				->addAttributeToSelect('*')
				->addAttributeToFilter('parent_id', $rootcatId)
				->setOrder('position')
				->addAttributeToFilter('include_in_menu', 1) //this is needed if you want only the categories in the menu
				->addAttributeToFilter('is_active', 1);

		$cat = array();
		$i=0;
		foreach($categories as $category) {
			//$cat = Mage::getModel('catalog/category')->load($category->getId());
			$cat[]= $category->getData();
			$cat[$i]["image_url"] = $category->getImageUrl();
			$i++;
		}

    $json = $cat; 
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>