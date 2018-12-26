<?php
    ini_set('max_execution_time', 3000);  //300 seconds = 5 minutes
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$customer_id = strtolower($js['customer_id']);
	$cart = $js['cart'];
	$payment_method = strtolower($js['payment_method']);
	$shipping_method = strtolower($js['shipping_method']);
	//$logFileName = 'customer-order-log-file.log';
	if(!empty($customer_id) && !empty($cart) && !empty($payment_method) && !empty($shipping_method)){
		
        $websiteId = Mage::app()->getWebsite()->getId();
        $store = Mage::app()->getStore();
			
		/**
		 * You need to enable this method from Magento admin
		 * Other methods: tablerate_tablerate, freeshipping_freeshipping, etc.
		 */ 
		$shippingMethod = 'flatrate_flatrate';
 
		/**
		 * You need to enable this method from Magento admin
		 * Other methods: checkmo, free, banktransfer, ccsave, purchaseorder, etc.
		 */ 
		$paymentMethod = 'cashondelivery';
 
		/** 
		 * Array of your product ids and quantity
		 * array($productId => $qty)
		 * In the array below, the product ids are 374 and 375 with quantity 3 and 1 respectively
		 */ 
		//$productIds = array(374 => 3, 375 => 1); 
 
		// Initialize sales quote object
		$quote = Mage::getModel('sales/quote')->setStoreId($store->getId());
 
		// Set currency for the quote
		$quote->setCurrency($store->getBaseCurrencyCode());
 
		$customer = Mage::getModel('customer/customer')
					->setWebsiteId($websiteId)
					->load($customer_id);
		/**
		 * Setting up customer for the quote 
		 * if the customer is not already registered
		 */
		if($customer->getId()){
		   // Assign customer to quote
			$quote->assignCustomer($customer);
			 
			// Add products to quote
			foreach($cart as $productId => $qty) {
				$product = Mage::getModel('catalog/product')->load($productId);
				if($product){
					
					$quote->addProduct($product, $qty);
				}
								
				/**
				 * Varien_Object can also be passed as the second parameter in addProduct() function like below:
				 * $quote->addProduct($product, new Varien_Object(array('qty' => $qty)));
				 */ 
			}
			$billing_address = Mage::getModel('customer/address')->load($customer->default_billing);
			$shipping_address = Mage::getModel('customer/address')->load($customer->default_shipping);
			$customerBillingAddress = $billing_address->getData();
			$customerShippingAddress = $shipping_address->getData();
 
			// Add billing address to quote
			$billingAddressData = $quote->getBillingAddress()->addData($customerBillingAddress);
			 
			// Add shipping address to quote
			$shippingAddressData = $quote->getShippingAddress()->addData($customerShippingAddress);
 
			/**
			 * Billing or Shipping address for already registered customers can be fetched like below
			 * 
			 * $customerBillingAddress = $customer->getPrimaryBillingAddress();
			 * $customerShippingAddress = $customer->getPrimaryShippingAddress();
			 * 
			 * Instead of the custom address, you can add these customer address to quote as well
			 * 
			 * $billingAddressData = $quote->getBillingAddress()->addData($customerBillingAddress);
			 * $shippingAddressData = $quote->getShippingAddress()->addData($customerShippingAddress);
			 */
 
			// Collect shipping rates on quote shipping address data
			$shippingAddressData->setCollectShippingRates(true)->collectShippingRates();
 
			// Set shipping and payment method on quote shipping address data
			$shippingAddressData->setShippingMethod($shippingMethod)->setPaymentMethod($paymentMethod);
			 
			// Set payment method for the quote
			$quote->getPayment()->importData(array('method' => $paymentMethod));
			try {
			// Collect totals of the quote
			$quote->collectTotals();
		 
			// Save quote
			$quote->save();
			
			// Create Order From Quote
			$service = Mage::getModel('sales/service_quote', $quote);
			$service->submitAll();
			//$incrementId = $service->getOrder()->getRealOrderId();
            
			/*Mage::getSingleton('checkout/session')
						->setLastQuoteId($quote->getId())
						->setLastSuccessQuoteId($quote->getId())
						->clearHelperData(); */   
			
			/**
			 * For more details about saving order
			 * See saveOrder() function of app/code/core/Mage/Checkout/Onepage.php
			 */ 
			
			// Log order created message
			//Mage::log('Order created with increment id: '.$incrementId, null, $logFileName);
			$json = array("success" => true, "error" => false);
		     
			
			} catch (Mage_Core_Exception $e) {
				$json = array("success" => false, "error" => true, "error_messages" => $e->getMessage());
							
				
			} catch (Exception $e) {
				
				$json = array("success" => false, "error" => true, "error_messages" => "There was an error processing your order. Please contact us or try again later.");
			
			}	
            // Resource Clean-Up
			$quote = $customer = $service = null; 
		}else{
			
			$json = array("error" => true, "message" => "CustomerID not exists!");
			
		}
	
	}else{
			
		$json = array("error" => true, "message" => "CustomerID, Cart, Payment Method, and Shipping Method not blank!");
	}
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>