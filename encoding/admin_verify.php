<?php
	require_once '../../app/Mage.php';
	Mage::app();
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$seller_id = strtolower($js['seller_id']);
	$tag_id = strtolower($js['tag_id']);
	$product_id = strtolower($js['product_id']);
	$latitude = strtolower($js['latitude']);
	$longitude = strtolower($js['longitude']);
    if(!empty($seller_id) && !empty($tag_id) && !empty($product_id) && !empty($latitude) && !empty($longitude)){
		
	   	$query = "SELECT * FROM `encode` WHERE `tag_id` = '".$tag_id."'";
		$result = $db_handle->numRows($query);
		if($result > 0){
		  /*$sql = "SELECT * FROM `encode` WHERE `tag_id` = '".$tag_id."' AND seller_id IS NULL";
		    $nRows = $db_handle->numRows($sql);
			if($nRows == 0){*/
				$productDetail = array();
				$result = $db_handle->runQuery($query);
				$productDetail['product_id'] = $result[0]['product_id'];	

				if($product_id == $productDetail['product_id']){
					
					$json = array("error" => true, "type" =>"2", "message" => "Tag already used!.");	
					
				}else{
					
					$productCollection = Mage::getModel('catalog/product')->getCollection()
											->addFieldToFilter('entity_id', $productDetail['product_id'])
											->load();
					$cnt = $productCollection->count();
					if($cnt == 1){
						
						$json = array("error" => true, "type" =>"2", "message" => "Tag already used!.");	
						
					}else{
						$productCollection = Mage::getModel('catalog/product')->getCollection()
												->addAttributeToSelect('*')
												->addFieldToFilter('entity_id', $product_id)
												->load();
						foreach($productCollection as $product){
							
							$lat  = $product->getLatitude();
							$long = $product->getLongitude();
							$pr_address = $product->getAddress();
						}
						function distance($lat1, $lon1, $lat2, $lon2, $unit) {
								  $theta = $lon1 - $lon2;
								  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
								  $dist = acos($dist);
								  $dist = rad2deg($dist);
								  $miles = $dist * 60 * 1.1515;
								  $unit = strtoupper($unit);

								  if ($unit == "M") { //Miles(M),Meters(M),Nautical Miles(N)
									return ($miles * 1609.344); // 1.609344 for Kilometers
								  } else if ($unit == "N") {
									  return ($miles * 0.8684);
									} else {
										return $miles;
									}
						}
						if(distance($latitude, $longitude,$lat,$long,"M") < 500){
												
							$query = "UPDATE `encode` SET `added_by` = 'admin', `bussiness_customer_id` = '".$seller_id."', `product_id` = '".$product_id."' , `chip_latitude` = '".$latitude."' , `chip_longitude` = '".$longitude."', `chip_address` = '".$pr_address."' WHERE  `tag_id` = '".$tag_id."'";
							$result = $db_handle->updateQuery($query);
							if($result == 1){
								$query = "SELECT `jeptag_id` FROM `encode` WHERE `tag_id` = '".$tag_id."'";
								$result = $db_handle->runQuery($query);	

								$json = array("error" => false, "message" => $result);  
							}else{
								
								$json = array("error" => true, "type" =>"2", "message" => "Database Error!");  
							}	
						}else{
							
							$json = array("error" => true, "type" =>"1", "message" => $pr_address);  
						}			
					}
						
				}
          /*}else{ 
				
				$json = array("error" => true, "type" =>"2", "message" => "Tag Encoding Incomplete!"); 
				
			}*/	 
			
		}else{

			$json = array("error" => true, "type" =>"2", "message" => "Tag Not Found!"); 
		}	

    }else{
			
		$json = array("error" => true, "type" =>"2", "message" => "SellerID, TagID, ProductID, Latitude and Longitude not blank!");
	}
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>