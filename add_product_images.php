<?php
	require_once '../app/Mage.php';
	//$seller_id = $_POST['seller_id'];
	$product_id = $_POST['product_id'];
	if(!empty($product_id)){	
		if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
			try {    
			    Mage::app();
				$uploader = new Varien_File_Uploader('image');
				$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
				$uploader->setAllowRenameFiles(false);
				$uploader->setFilesDispersion(false);
				$path = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product'. DS;
				$prodimg = $product_id.'_'.$_FILES['image']['name'];
				if($uploader->save($path, $prodimg)){
					
					Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
	                $product = Mage::getModel('catalog/product')->load($product_id);
						try{
		                    $product
							 	->setMediaGallery (array('images'=>array (), 'values'=>array ())) //media gallery initialization
		                    	->addImageToMediaGallery($path.$prodimg, array('image','thumbnail','small_image'), true, false); //assigning image, thumb and small image to media gallery
							if($product->save()){
								
								//unlink($path.$prodimg);
								
								$json = array("error" => false, "message" => "Uploaded successfully!");
								
							}else{
								
								$json = array("error" => true, "message" => "Error Found!");
								
							}	
							
						   }catch(Exception $e){
							 				
							  $json = array("error" => true, "message" => "Error Found!");
			               }				    		
					
				}
				
			} catch (Exception $e) {
				
				$json = array("error" => true, "message" => "Upload Error!");
			}
		}else{
			
			$json = array("error" => true, "message" => "Please choose an product image.");
		}

	}else{
			
		$json = array("error" => true, "message" => "Please enter Product ID.");
	}
			
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>