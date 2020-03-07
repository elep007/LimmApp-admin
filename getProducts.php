<?php
	include("dbcon.inc");

	$sql = "SELECT * FROM products order by id asc";
	$products = $conn->query($sql);

	$product_data=array();
	while ($row=$products->fetch_array()) {
		$sql = "SELECT * FROM options where product_id=".$row['id'];
		$options = $conn->query($sql);
		$product = array();
		$product['options']=$options->fetch_all(MYSQLI_ASSOC);
		$product['name']=$row['name'];
		$product['id']=$row['id'];
		array_push($product_data, $product);
	}

	echo json_encode($product_data);
?>