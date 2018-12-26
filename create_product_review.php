<?php
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	Mage::app();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$customer_id = $js['customer_id'];
	$product_id = $js['product_id'];
	$rating_value = $js['rating'];
	$title = $js['title'];
	$description = $js['description'];
	if(!empty($customer_id) && !empty($product_id) && !empty($rating_value) && !empty($title) && !empty($description)){		
		
		$product = Mage::getModel('catalog/product')->load($product_id);
		$customer = Mage::getModel('customer/customer')->load($customer_id);
				
		if($customer->getId() && $product->getId()){
					
			$review = Mage::getModel('review/review');
			$review->setEntityPkValue($product->getId()); //product id
			$review->setStatusId(2);   // 1-approved,2-Pending,3-Not Approved
			$review->setTitle($title);
			$review->setDetail($description);
			$review->setEntityId(1);                                      
			$review->setStoreId(Mage::app()->getStore()->getId());                    
			$review->setCustomerId($customer->getId()); //null is for administrator
			$review->setNickname($customer->getFirstname());
			$review->setStores(array(Mage::app()->getStore()->getId())); 
            try {	
			
			   $review->save();
			    // Add review Ratting
			$_ratingOptions = array(
				1 => array(1 => 1,  2 => 2,  3 => 3,  4 => 4,  5 => 5),
				2 => array(1 => 6,  2 => 7,  3 => 8,  4 => 9,  5 => 10),
				3 => array(1 => 11, 2 => 12, 3 => 13, 4 => 14, 5 => 15),
			);


			foreach($_ratingOptions as $_ratingId => $_optionIds) {
				try {

					Mage::getModel('rating/rating')
						->setRatingId($_ratingId)
						->setReviewId($review->getId())
						->addOptionVote($_optionIds[$rating_value], $product->getId());
					 $json = array("error" => false, "message" => true);	
				} catch (Exception $e) {
					//$this->log($e->getMessage());
					$json = array("error" => false, "message" => $e->getMessage());	
				}
			}			
			}catch(Exception $e) {
				
                //die($e->getMessage());
				$json = array("error" => false, "message" => $e->getMessage());	
            }					
				
		}else{
				
			  $json = array("error" => true, "message" => "CustomerID or ProductID invalid!");	
				
		}	
		
	}else{
			
		$json = array("error" => true, "message" => "CustomerID, ProductID, Rating, Title and Description not blank!");
	}
	
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>