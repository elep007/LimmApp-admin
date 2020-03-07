<?php
	include("dbcon.inc");
	
	$content=trim(file_get_contents("php://input"));
	$decoded=json_decode($content,true);

	$data=$decoded['data'];

	$delivery_date = $data['delivery_date'];
 	$orders=json_encode($data['order_data']);
    $price=$data["price"];
    $address=$data["delivery_address"];
    
	$sql = "INSERT INTO orders (delivery_date,orders,price,latitude,longitude,address, type) 
			VALUES ('$delivery_date','$orders','$price','".$data['latitude']."','".$data['longitude']."','$address','".$data['type']."')";
	if($conn->query($sql))
		echo json_encode(array("status"=>"ok"));
	else
		echo json_encode(array("status"=>"error"));
?>