<?php
	require_once '../app/Mage.php';
	//require_once("dbcontroller.php");
	//$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$customer_id = strtolower($js['customer_id']);
	Mage::app();
	if(!empty($customer_id)){		

				$orderCollection = Mage::getResourceModel('sales/order_collection')
								->addFieldToSelect('*')
								->addFieldToFilter('customer_id',$customer_id)
								->setOrder('created_at', 'desc');   
				$customerOrders = array();
				$shipAddress = array();
			    if($orderCollection){	
                    $i=0;				
					foreach($orderCollection as $order){
						$customerOrders[$i]['increment_id']  = $order->getIncrementId();
						$customerOrders[$i]['status']  =  $order->getStatus();
						$customerOrders[$i]['coupon_code']  =  $order->getCouponCode();
						$customerOrders[$i]['total_item_count']  = $order->getTotalItemCount();
						$customerOrders[$i]['total_qty_ordered']  = floor($order->getTotalQtyOrdered());
						$customerOrders[$i]['shipping_method']  = $order->getShippingMethod();
						$customerOrders[$i]['shipping_amount']  = floor($order->getShippingAmount());
						$shippingAddress = Mage::getModel('sales/order_address')->load($order->getShippingAddressId());
						$shipAddress['firstname'] = $shippingAddress->getFirstname();
						$shipAddress['lastname'] = $shippingAddress->getLastname();
						$shipAddress['email'] = $shippingAddress->getEmail();
						$shipAddress['city'] = $shippingAddress->getCity();
						$shipAddress['street'] = $shippingAddress->getStreet();
						$shipAddress['country'] = $shippingAddress->getCountry();
						$shipAddress['telephone'] = $shippingAddress->getTelephone();
						$customerOrders[$i]['shipping_address']  = $shipAddress;
						$customerOrders[$i]['subtotal']  = floor($order->getSubtotal());
						$customerOrders[$i]['grand_total']  = floor($order->getGrandTotal());
						$customerOrders[$i]['created_at']  = $order->getCreatedAt();
					    $items=array();
						$j=0;
					    foreach($order->getAllItems() as $item){
					        $product = Mage::getModel('catalog/product')->load($item->getProductId());
			               	$image_url = $product->getImageUrl();
							/*$images = array();
							$k=0;
							foreach($gallery_images as $g_image) {
								$images[$k] = $g_image['url'];
							$k++;	
							}*/
							$items[$j]['id'] = $item->getProductId();
							$items[$j]['name'] = $item->getName();
							$items[$j]['sku']  =  $item->getSku();
							$items[$j]['price']  =  floor($item->getPrice());
							$items[$j]['ordered_qty'] = floor($item->getQtyOrdered());
							$items[$j]['image_url'] = $image_url;
						$j++;
					   }	
					   $customerOrders[$i]['items'] = $items;
					   $i++;
					}	
					
					$json = array("error" => false, "message" => $customerOrders);
					
				}else{
					
					$json = array("error" => true, "message" => "Not Found!");
					
				}		
                		
	}else{
				
		    $json = array("error" => true, "message" => "Customer ID not blank!");
	}
	
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>