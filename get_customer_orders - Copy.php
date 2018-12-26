<?php
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
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
			    if($orderCollection){					
					foreach ($orderCollection as $order) {
						$customerOrders['increment_id']  = $order->getIncrementId();
						$customerOrders['status']  =  $order->getStatus();
						$customerOrders['coupon_code']  =  $order->getCouponCode();
						$customerOrders['total_item_count']  = $order->getTotalItemCount();
						$customerOrders['total_qty_ordered']  = $order->getTotalQtyOrdered();
						$customerOrders['shipping_method']  = $order->getShippingMethod();
						$customerOrders['shipping_amount']  = $order->getShippingAmount();
						$customerOrders['subtotal']  = $order->getSubtotal();
						$customerOrders['grand_total']  = $order->getGrandTotal();
					    $items=array();
					    foreach ($order->getAllItems() as $item) {
							$items['name'] = $item->getName();
							$items['sku']  =  $item->getSku();
							$items['price']  =  $item->getPrice()
							$items['ordered_qty'] = $item->getQtyOrdered();
							$items['image_url'] = $item->getImageUrl();
						
					   }	
					}	
					$customerOrders['items'] = $items;
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