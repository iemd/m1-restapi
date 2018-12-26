<?php
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$category = array();
	$seller_id = strtolower($js['seller_id']);
	//$sku = strtolower($js['sku']);
	$sku = time();
	$name = $js['name'];
	$category = strtolower($js['category']);
	$description = $js['description'];
	$weight = strtolower($js['weight']);
	$price = strtolower($js['price']);
	$qty = strtolower($js['qty']);
	//$long = strtolower($js['longitude']);
	//$lat = strtolower($js['latitude']);
	$selling_mode = strtolower($js['mode']); // Online - 237, Offline - 238
	$barcode = $js['barcode'];
	$address = $js['address'];
	$brand = $js['brand'];
	$condition = $js['condition'];
	$arr_add = explode(',', $address);
	$longitude = end($arr_add);
	$latitude = prev($arr_add);
	/*
	include('includes/geocode.php');
	$arr_add = explode(',', $address);
	array_shift($arr_add);
	$geo_address = implode(',',$arr_add);
	$latLong = getLatLong($geo_address);
    $latitude = $latLong['latitude']?$latLong['latitude']:'Not found';
    $longitude = $latLong['longitude']?$latLong['longitude']:'Not found';
	*/
    // Images 
	$images = explode(',', $js['image']) ;
	$prod_images = array();
	if($selling_mode == 238){
		
	  $visibility = 1;	
	  
	}else{
		
	  $visibility = 4;
	}	
	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
   	//$short_description = strtolower($js['short_description']);
	// $productData = json_encode(array(
            // 'type_id'           => 'simple',
            // 'attribute_set_id'  => 4,
            // 'sku'               => 'simple' . uniqid(),
            // 'weight'            => 1,
            // 'status'            => 1,
            // 'visibility'        => 4,
            // 'name'              => 'Simple Product',
            // 'description'       => 'Simple Description',
            // 'short_description' => 'Simple Short Description',
            // 'price'             => 99.95,
            // 'tax_class_id'      => 0,
    // ));	 
	$product = Mage::getModel('catalog/product');
	//    if(!$product->getIdBySku('testsku61')):
	if(!empty($seller_id) && !empty($name) && !empty($sku)){
		try{
		$product
		//  ->setStoreId(1) //you can set data in store scope
			->setWebsiteIds(array(1)) //website ID the product is assigned to, as an array
			->setAttributeSetId(4) //ID of a attribute set named 'default'
			->setTypeId('simple') //product type
			->setCreatedAt(strtotime('now')) //product creation time
		//  ->setUpdatedAt(strtotime('now')) //product update time
		
			->setSku($sku) //SKU
			->setName($name) //product name
			->setWeight($weight)
			->setStatus(1) //product status (1 - enabled, 2 - disabled)
			->setTaxClassId(2) //tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
			->setVisibility($visibility) //catalog and search visibility
			
		//	->setManufacturer(28) //manufacturer id
		//	->setColor(24)
		//	->setNewsFromDate('06/26/2014') //product set as new from
		//	->setNewsToDate('06/30/2014') //product set as new to
		//	->setCountryOfManufacture('AF') //country of manufacture (2-letter country code)
		 
			->setPrice($price) //price in form 11.22
		//	->setCost(22.33) //price in form 11.22
		//	->setSpecialPrice(00.44) //special price in form 11.22
		//	->setSpecialFromDate('06/1/2014') //special price from (MM-DD-YYYY)
		//	->setSpecialToDate('06/30/2014') //special price to (MM-DD-YYYY)
		//	->setMsrpEnabled(1) //enable MAP
		//	->setMsrpDisplayActualPriceType(1) //display actual price (1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config)
		//	->setMsrp(99.99) //Manufacturer's Suggested Retail Price
		 
		//	->setMetaTitle('test meta title 2')
		//	->setMetaKeyword('test meta keyword 2')
		//	->setMetaDescription('test meta description 2')
		 
			->setDescription($description)
		//	->setShortDescription($short_description)
		 
		//	->setMediaGallery (array('images'=>array (), 'values'=>array ())) //media gallery initialization
						
		//	->addImageToMediaGallery($prod_images, array('image','thumbnail','small_image'), true, false) //assigning image, thumb and small image to media gallery
		    ->setLongitude($longitude)
			->setLatitude($latitude)
			->setMode($selling_mode)
			->setBarcode($barcode)
			->setAddress($address)
			->setBrand($brand)
			->setCondition($condition)
			->setStockData(array(
							   'use_config_manage_stock' => 0, //'Use config settings' checkbox
							   'manage_stock'=>1, //manage stock
							   'min_sale_qty'=>1, //Minimum Qty Allowed in Shopping Cart
							   'max_sale_qty'=>2, //Maximum Qty Allowed in Shopping Cart
							   'is_in_stock' => 1, //Stock Availability
							   'qty' => $qty //qty
						   )
			)
			->setCategoryIds($category); //assign product to categories
			if($product->save()){
				
				$collection = Mage::getModel('catalog/product')->getCollection();                   
				$latestItemId = $collection->getLastItem()->getId();
				$query = "INSERT INTO `marketplace_product` (mageproductid,userid) VALUES ($latestItemId,$seller_id)";
				$result = $db_handle->insertQuery($query);
			    if($result){
					// Product Images Upload
					foreach($images as $img => $image){
						$randName = md5(uniqid(rand() * time()));
						//decode the image
						$decodedImage = base64_decode($image);
						$image_name = $name."_".$randName.".jpg";
						//upload the image
						$filepath = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product'. DS;
						$path = file_put_contents($filepath.$image_name, $decodedImage);
						$prod_images[]= $filepath.$image_name;
				    }
					$product = Mage::getModel('catalog/product');
                    $product->load($latestItemId);
				    try {
						$i=1;
						foreach($prod_images as $gallery_img)
						 /**
						 * @param directory where import image reides
						 * @param leave 'null' so that it isn't imported as thumbnail, base, or small
						 * @param false = the image is copied, not moved from the import directory to it's new location
						 * @param false = not excluded from the front end gallery
						 */
						{
							$product->setMediaGallery(array('images' => array (), 'values' => array()));
							if (file_exists ($gallery_img)){
								if($i==1)
									$product->addImageToMediaGallery($gallery_img, array ("thumbnail", "small_image", "image" ),true, false )->save();
								else
									$product->addImageToMediaGallery($gallery_img, null,true, false )->save();
							}
							$i++;
						}
                    }catch(Exception $e){
                         $json = array("error" => false, "message" => "Image upload error!");
                    }
									  
				    $json = array("error" => false, "message" => $result);
				}else{
						$json = array("error" => true, "message" => "Database Error!");
				} 
						
			}else{
				
				$json = array("error" => true, "message" => "Error found");
			}
			//endif;
						
			}catch(Exception $e){
				Mage::log($e->getMessage());
				//$msg = $e->getMessage();
				$json = array("error" => true, "message" => "SKU already exists!");
			}
	}else{
		
		$json = array("error" => true, "message" => "Seller ID/Name/SKU not blank!");
	}
		
		
		
		
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>