<?php
	include("dbcon.inc");
	
	$content=trim(file_get_contents("php://input"));
	$decoded=json_decode($content,true);

    
	$name=trim($decoded['name']);
	$email=trim($decoded['email']);
	
	$to=$email;
	$subject="Join Limm Group";
	$message="In order to join our team, check follow link.";

	
	$returnValue=array("status"=>"error");

	if(empty($to) ||empty($message)){
		
	}
	else{

        //$to      ="info@the-work-kw.com";
        //$subject = 'Contact message from the-work-kw.com';
        //$message = $message;
        $headers = 'From: noreply@limmgroup.com' . "\r\n" .
            'Reply-To: noreply@limmgroup.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        
        mail($to, $subject, $message, $headers);
            
        $returnValue["status"]="ok";

	}
	echo json_encode($returnValue);
?>