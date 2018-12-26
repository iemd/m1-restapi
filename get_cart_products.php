<?php
	require_once '../app/Mage.php';
	Mage::app();
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$session_id = strtolower($js['session_id']);
	$cart_id = $js['cart_id'];
	if(!empty($session_id) && !empty($cart_id)){
		
		require_once 'includes/soap_config.php';
		
	    $proxy = new SoapClient('http://'.$config["hostname"].'/api/v2_soap/?wsdl', array('trace'=>1));
		try{
			$result = $proxy->shoppingCartProductList($session_id, $cart_id,'1');
			if(count($result)>0){
				$productIds = array();
				foreach($result as $model){
					
					$productIds[] = $model->product_id;
				}	
                $products = Mage::getModel('catalog/product')->getCollection()
						   ->addAttributeToSelect('*')
                           ->addAttributeToFilter('entity_id', array('in' => $productIds));
			    if($products->count()>0){
					$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($products);
					$res = array();
					$i=0;
					foreach($products as $product){
						$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
						$res[$i]['product_id'] = $product->getId();
						$res[$i]['sku'] = $product->getSku();
						$res[$i]['name'] = $product->getName();
						$res[$i]['qty'] = round($stock->getQty(), 0);
						$res[$i]['price'] = round($product->getPrice(), 0);
						$res[$i]['image_url'] = $product->getImageUrl();
						$i++;
					}	
                }
				
               $json = array("error" => false, "message" => $res);
			   
			} else {
				
				 $json = array("error" => false, "message" => "Not found!");
				 
			}	
		
		} catch (Exception $e){
			 //$response['error_code'] = $e->getCode();
			 //$response['message'] = $e->getMessage();
			 $json = array("error" => true, "message" => $e->getMessage());
		}         
    }else{
			
		$json = array("error" => true, "message" => "SessionID and CartID not blank!");
	}
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>