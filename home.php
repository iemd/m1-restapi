<?php
	require_once '../app/Mage.php';
	require('includes/simple_html_dom.php');
	//Mage::app();
	//require_once("dbcontroller.php");
	//$db_handle = new DBController();
	require_once ('vendor/autoload.php');
    use \Statickidz\GoogleTranslate;
	$trans = new GoogleTranslate();
    $source = 'en';
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$target = strtolower($js['lang']);
	if(empty($target)){$target = 'en';}
	$homepage = array();
	//--------------Shop By Department---------------------------
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
			$catname = $category->getName();
			$catname = $trans->translate($source, $target,$catname);
			$description = $category->getDescription();
			$description = $trans->translate($source, $target,$description);
			$cat[]= $category->getData();	
			$cat[$i]["name"] = $catname;
			$cat[$i]["description"] = $description;	
			$cat[$i]["image_url"] = $category->getImageUrl();
			$i++;
		}
     if(count($cat)>0){
		 
		 $homepage['shop_by_department'] = array("error" => false, "message" => $cat);
	 }else{
		 
		 $homepage['shop_by_department'] = array("error" => true, "message" => "No Category Exists!");
	 }       
   
	//---------------------------------------------------------
	
	$html = "";
	$html .= Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('block_slide1')->toHtml();
	$html .= Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('block_slide2')->toHtml();
	$html .= Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('block_slide3')->toHtml();
	$html .= Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('block_slide4')->toHtml();
	$html .= Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('block_slide5')->toHtml();
	$html = str_get_html($html);
	// creating an array of elements
    $slideshow = [];
    foreach ($html->find('div') as $slider) {  
   
        $slideshow[] = $slider->attr;
       
    }
    if($slideshow >0 ){
		
		$homepage['slideshow'] = array("error" => false, "message" =>$slideshow );
		
	}else{
		
		$homepage['slideshow'] = array("error" => true, "message" =>"No slider image exists!" );
	}
    //----------------------------------------------------------
	$collection = Mage::getModel('catalog/product')->getCollection()
							->addAttributeToSelect('*')
							->addFieldToFilter('promotion', array('eq' => 1));
    $size = $collection->getSize();
	$productDetail = [];
	if($size>0){
        $i = 0;		
		foreach($collection as $product){
			$prodname = $product->getName();
			$prodname = $trans->translate($source, $target,$prodname);
			$description = $product->getDescription();
			$description = $trans->translate($source, $target,$description);
					
			$productDetail[] = $product->getData();
			$productDetail[$i]['name']= $prodname;
			$productDetail[$i]['description']= $description;
			$productDetail[$i]['image']= $product->getImageUrl();
						
		    $i++;			
		}            			
       $homepage['product_promotions'] = array("error" => false, "message" => $productDetail);	
		 
	}else{
		
		$homepage['product_promotions'] = array("error" => true, "message" => "No Product Found!");
		 
	}
	//---------------------------------------------------------
	 $todayStartOfDayDate  = Mage::app()->getLocale()->date()
							->setTime('00:00:00')
							->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
	$todayEndOfDayDate  = Mage::app()->getLocale()->date()
							->setTime('23:59:59')
							->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
	/** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
	$collection = Mage::getResourceModel('catalog/product_collection')
							->addAttributeToSelect('*')
							->setVisibility(Mage::getSingleton('catalog/product_visibility')
							->getVisibleInCatalogIds());    
	$collection = $collection->addMinimalPrice()
							->addFinalPrice()
							->addTaxPercents()
							->addAttributeToSelect(Mage::getSingleton('catalog/config')
							->getProductAttributes())
							->addUrlRewrite()
							->addStoreFilter()
							->addAttributeToFilter('news_from_date', array('or'=> array(0 => array('date' => true, 'to' => $todayEndOfDayDate),
																				1 => array('is' => new Zend_Db_Expr('null')))), 'left')
							->addAttributeToFilter('news_to_date', array('or'=> array(0 => array('date' => true, 'from' => $todayStartOfDayDate),
																				1 => array('is' => new Zend_Db_Expr('null')))), 'left')
							->addAttributeToFilter(array(array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
															array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))))
							->addAttributeToSort('news_from_date', 'desc');
    $size = $collection->getSize();
	$productDetail = [];
	if($size>0){
        $i = 0;		
		foreach($collection as $product){
			$prodname = $product->getName();
			$prodname = $trans->translate($source, $target,$prodname);
			$description = $product->getDescription();
			$description = $trans->translate($source, $target,$description);
					
			$productDetail[] = $product->getData(); 
			$productDetail[$i]['name']= $prodname;
			$productDetail[$i]['description']= $description;
			$productDetail[$i]['image']= $product->getImageUrl();
						
		    $i++;			
		}            			
        $homepage['new_products'] = array("error" => false, "message" => $productDetail);	
		 
	}else{
		
		$homepage['new_products'] = array("error" => true, "message" => "No Product Found!");
		 
	}
	
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($homepage);

?>