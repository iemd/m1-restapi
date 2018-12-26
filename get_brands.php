<?php
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	require_once ('vendor/autoload.php');
    use \Statickidz\GoogleTranslate;
	$trans = new GoogleTranslate();
    $source = 'en';
   	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$target = strtolower($js['lang']);
	if(empty($target)){$target = 'en';}
	Mage::app();
	$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'brand');
	$opt = $attribute->getSource()->getAllOptions(false);
	$cnt = count($opt);
	if($cnt > 0){
		$i=0;
		foreach($attribute->getSource()->getAllOptions(false) as $option){
			$label = $trans->translate($source, $target,$option['label']);
			$attributeArray[$i]['value'] = $option['value'];
			$attributeArray[$i]['label'] = $label;
			$i++;			
		}
		$json = array("error" => false, "message" => $attributeArray);	
		
	}else{
		
		$json = array("error" => true, "message" => "Not Found!");	
		
	}	
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>