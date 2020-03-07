<?php
	include("dbcon.inc");
	
	$content=trim(file_get_contents("php://input"));
	$decoded=json_decode($content,true);

    // $to="sparkle172538@gmail.com";
    $to="support@limmgroup.com";
    
	$sender=trim($decoded['sender']);
	$sender_email=trim($decoded['sender_email']);
	$subject="From ". $sender;
	$message=trim($decoded['message']);
	
	$returnValue=array("status"=>"error");

	if(empty($to) ||empty($message)){
		
	}
	else{

        //$to      ="info@the-work-kw.com";
        //$subject = 'Contact message from the-work-kw.com';
        //$message = $message;
        $headers = 'From: '.$sender_email . "\r\n" .
            'Reply-To: $sender_email' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        
        mail($to, $subject, $message, $headers);
            
        $returnValue["status"]="ok";

	}
	echo json_encode($returnValue);
?>