<?php
	include("dbcon.inc");
	
	$content=trim(file_get_contents("php://input"));
	$decoded=json_decode($content,true);

	$mobile=trim($decoded['mobile']);
	$password=trim($decoded['password']);
	
	$returnValue=array("status"=>"fail");

	$sql = "SELECT * FROM ahmed_user WHERE mobile='$mobile'";
	$result =  $conn->query($sql);
	if($result){
		if ($result->num_rows>0) {
			// output data of each row
			$row = $result->fetch_assoc();
			if($row["password"]==$password){
				$returnValue["status"]= "ok";
				$returnValue["confirmcode"]=$row['confirmcode'];
			}
			else{
				$returnValue["status"]="wrongpassword";
			}
		} else {
			$returnValue["status"]="nomobile";
		}				
	}				

	echo json_encode($returnValue);
    //echo '{"confirmcode": "off", "status": "ok"}';
?>