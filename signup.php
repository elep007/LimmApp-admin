<?php
	include("dbcon.inc");
	
	$content=trim(file_get_contents("php://input"));
	$decoded=json_decode($content,true);

	
	$firstname=trim($decoded['firstname']);
	$lastname=trim($decoded['lastname']);
	$mobile=trim($decoded['mobile']);
	$password=trim($decoded['password']);

	$returnValue=array("status"=>"fail");

	$sql = "SELECT * FROM ahmed_user WHERE mobile='$mobile'";
	$result = $conn->query($sql);
	if($result){
		if($result->num_rows>0){
			$returnValue['status']="existmobile";
		}
		else{
			
			$sql = "INSERT INTO ahmed_user (firstname,lastname,mobile,password,confirmcode) 
			VALUES ('$firstname','$lastname','$mobile','$password','off')";
			$result = $conn->query($sql);
			if($result){
				$returnValue['status']="ok";
			}
			else{
				$returnValue['status']="fail";
			}
		}
	}

	echo json_encode($returnValue);
?>