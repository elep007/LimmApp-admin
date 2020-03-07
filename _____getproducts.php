<?php
	include("dbcon.inc");
	
	$content=trim(file_get_contents("php://input"));
	$decoded=json_decode($content,true);

	$returnValue=array("status"=>"noproduct");
	$products=array();


	$sql = "SELECT * FROM limm_product WHERE 1";
	$result = mysql_query($sql,$conn);
	if($result){
		if (mysql_num_rows($result)>0) {
			while($row = mysql_fetch_array($result)){
				$temp=array();
				$temp['id']=$row['id'];
				$temp['asin']=$row['asin'];
				$temp['name']=$row['name'];
				$temp['image']=$row['image'];
				$temp['price']=$row['price'];
				$temp['originalprice']=$row['originalprice'];
				$temp['rating']=$row['rating'];
				$temp['reviews']=$row['reviews'];
				$temp['answers']=$row['answers'];
				$temp['description']=$row['description'];
				$temp['ebook']=$row['ebook'];
				$temp['manual']=$row['manual'];
				$temp['video']=$row['video'];
				$temp['amazon']=$row['amazon'];
				array_push($products,$temp);
			}
			$returnValue["status"]="ok";
			$returnValue["products"]=$products;		}
		else{
			$returnValue["status"]="noproduct";
		}
	}
	else{
		$returnValue["status"]="connectionerror";
	}	
	echo json_encode($returnValue);
?>