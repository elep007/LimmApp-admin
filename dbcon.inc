 <?php
	$DBHOST = "localhost";
	$DBNAME = "thewor87_auction";
	$DBUSER = "thewor87_anton";
	$DBPASS = "qwertyuiop1234567890";

	// Create connection
	$conn = new mysqli($DBHOST, $DBUSER,$DBPASS, $DBNAME);
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

?> 