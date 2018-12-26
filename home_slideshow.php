<?php
	require_once '../app/Mage.php';
	require('includes/simple_html_dom.php');
	//Mage::app();
	//require_once("dbcontroller.php");
	//$db_handle = new DBController();
	//read JSon input
	//$jsondata=file_get_contents('php://input');
	//$js = json_decode($jsondata, true);
	$slider = array();
	$html = "";
	$html .= Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('block_slide1')->toHtml();
	$html .= Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('block_slide2')->toHtml();
	$html .= Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('block_slide3')->toHtml();
	$html .= Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('block_slide4')->toHtml();
	$html .= Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('block_slide5')->toHtml();
	$html = str_get_html($html);
	// creating an array of elements
    $slideshow = [];
    foreach ($html->find('div') as $slider) {  
   
        $slideshow[] = $slider->attr;
       
    }
    if($slideshow >0 ){
		
		$json = array("error" => false, "message" =>$slideshow );
		
	}else{
		
		$json = array("error" => true, "message" =>"No slider image exists!" );
	}
    
	
    /* Output header */
    header('Content-type: application/json, charset=UTF-8');
    echo json_encode($json);

?>