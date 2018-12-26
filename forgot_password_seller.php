<?php
	ini_set('max_execution_time', 300);
	require_once '../app/Mage.php';
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	//read JSon input
	$jsondata=file_get_contents('php://input');
	$js = json_decode($jsondata, true);
	$seller_email = strtolower($js['email']);
	Mage::app();
	if(!empty($seller_email)){
			
		$customer = Mage::getModel("customer/customer");
		$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
		$customer->loadByEmail($seller_email); //load customer by email id
		$seller_id = $customer->getId();
	    $query = "SELECT * FROM `marketplace_userdata` WHERE `mageuserid` = '".$seller_id."' ";
		$result = $db_handle->numRows($query);
		if($customer->getId() && ($result > 0)){
			$verification_code = substr(md5(uniqid(rand(), true)), 6, 6);
			require 'includes/PHPMailer/PHPMailerAutoload.php';
			$mail = new PHPMailer();
            $mail->IsSMTP();                                      // set mailer to use SMTP
			//$mail->SMTPDebug = 1;                                 // Enable verbose debug output
			$mail->Host = "tls://smtp.gmail.com";                 // specify main and backup server
			$mail->SMTPAuth = true;                               // turn on SMTP authentication
			$mail->Username = "jeptagshop@gmail.com";             // SMTP username
			$mail->Password = "jep@123!@#";                       // SMTP password
			$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
     	    $mail->Port = 587;                                    // TCP port to connect to
		    $mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
		    );
		    $mail->SetFrom('support@jeptags.com','JepTags');

			//$mail->AddAddress("josh@example.net", "Josh Adams");
			$mail->AddAddress($seller_email);   // name is optional
			$mail->AddReplyTo("donotreply@jeptags.com", "JepTags");

			$mail->WordWrap = 50;                                 // set word wrap to 50 characters
			//$mail->AddAttachment("/var/tmp/file.tar.gz");       // add attachments
			//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");  // optional name
			$mail->IsHTML(true);                                  // set email format to HTML

			$mail->Subject = "JepTgas Password Reset";
			$bodyContent = '<b>Dear JepTags User,</b></br></br>';
			$bodyContent .='<p>To reset your account password, Your verification code is: </br> <b>'.$verification_code.'</b> </p>
			<p>If you need additional assistance, or you did not make this change, please contact help@jeptags.com.<br/><br/>  
            <p>The JepTags Team</p>';
			$mail->Body = $bodyContent;
			//$mail->AltBody = "This is the body in plain text for non-HTML mail clients";
			$code = array();
            $code['verification_code'] = $verification_code;
			if(!$mail->Send())
			{
			   $json = array("error" => true, "message" => $mail->ErrorInfo);
			}else{
				$json = array("error" => false, "message" => $code);
			}		
					
		}else{
			
			$json = array("error" => true, "message" => "Seller with this email not exists!");
			
		}	
	}else{
				
		$json = array("error" => true, "message" => "Email not blank!");
	}
	
	/* Output header */
	header('Content-type: application/json, charset=UTF-8');
	echo json_encode($json);

?>