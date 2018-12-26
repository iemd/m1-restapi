<?php
	require_once '../app/Mage.php';
	Mage::app();
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	require_once ('vendor/autoload.php');
    use \Statickidz\GoogleTranslate;
	$trans = new GoogleTranslate();
    $source = 'en';
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$cat_id = strtolower($js['category_id']);
	$target = strtolower($js['lang']);
	if(empty($target)){$target = 'en';}
	if(!empty($cat_id)){
		    /* Load category by id*/
		    $cat = Mage::getModel('catalog/category')->load($cat_id);
			/*Returns comma separated ids*/
            $subcats = $cat->getChildren();
			$subcat = array();
			$i=0;
			foreach(explode(',',$subcats) as $subCatid){
				$category = Mage::getModel('catalog/category')->load($subCatid);
				if($category->getIsActive())
                {
					$subcat_name = $category->getName();
					$subcat_name = $trans->translate($source, $target,$subcat_name);
					$subcat[$i]['subcat_id'] = $category->getId();
					$subcat[$i]['name'] = $subcat_name;
					if($category->getThumbnail()){
						
						$skin_url = $category->getThumbnail();
						$subcat[$i]['image_url'] = Mage::getBaseUrl('media').'catalog/category/'.$skin_url;
						
					}else{
						
					    $subcat[$i]['image_url'] = null;
					}
		
						
				}
				$i++; 		
			}
			$result = count($subcat);
			if($result>0){
				$result = $subcat;
				$json = array("error" => false, "message" => $result);
			}
			else{
				
				$json = array("error" => true, "message" => "Not Found!");
				
			}
		
	}else{
		
		$json = array("error" => false, "message" => "Please enter category ID");
	}

    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>