<?php
	require_once '../../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	Mage::app();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$tag_id = $js['tag_id'];
	if(!empty($tag_id)){		
		$query = "SELECT * FROM `encode` WHERE `tag_id` = '".$tag_id."'";
		$result = $db_handle->numRows($query);
		    if($result == 1){
				$result = $db_handle->runQuery($query);
				$bussiness_customer_id = "";
				$product_id = "";
				
				foreach($result as $model){

					$bussiness_customer_id = $model['bussiness_customer_id'];
					$product_id = $model['product_id'];
					
				}	
				$product = Mage::getModel('catalog/product')->load($product_id);
				$customer = Mage::getModel('customer/customer')->load($bussiness_customer_id);
				
				if($customer->getId() && $product->getId()){
					$productId = $product->getId();	
					/**
					 * Getting reviews collection object
					 */
					$productId = $product->getId();
					$reviews = Mage::getModel('review/review')
									->getResourceCollection()
									->addStoreFilter(Mage::app()->getStore()->getId())
									->addEntityFilter('product', $productId)
									->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
									->setDateOrder()
									->addRateVotes();
					/**
					 * Getting average of ratings/reviews
					 */
					$avg = 0;
					$star = 0;
					$ratings = array();
					if(count($reviews) > 0){
						$productReviews = array();
						$customer_name = '';
						$rating_sum = 0;
						foreach($reviews->getItems() as $review){
							$customer_id = $review->getCustomerId();
							$customer = Mage::getModel('customer/customer')->load($customer_id);
							$customer_name = $customer->getFirstname().' '.$customer->getLastname();
                            $prod_review = $review->getData();	
							foreach($review->getRatingVotes() as $vote){
								$ratings[] = $vote->getPercent();
							}
						 $avg = array_sum($ratings)/count($ratings);
						 $star = $avg*(0.05);
						 $rating_sum += $star;	
						 $prod_review['customer_name'] = $customer_name;
						 $prod_review['rating'] = $star;
						 $productReviews[] = $prod_review;	 
						}                      						
					}
					$review_count = array('review_count' => count($reviews));
					$avg_rating = array('average_rating' => $rating_sum/count($reviews));
					//array_unshift($productReviews,$review_count);	
					//array_unshift($productReviews,$avg_rating);
					
					$json = array("error" => false, "average_rating" =>$avg_rating, "review_count" =>$review_count, "message" => $productReviews);	
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