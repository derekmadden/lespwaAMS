<?php
	

	$total = 2+5*20-6/3;
	echo $total;
	exit();

	$servername = "localhost";
	$serverusername = "root";
	$serverpass = "derek123";
	$dbname = "moh_ams";


	$ticket_no = 0;
	$sql = 'SELECT * FROM maintenance_tickets WHERE ticket_no = '.$ticket_no;
	// echo $sql;
	// exit();


	// Create connection
	$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
	// Check connection
	if ($conn->connect_error) {
	  die("Connection failed: " . $conn->connect_error);
	}

	
	$result = $conn->query($sql);
	$conn->close();


	if ($result->num_rows > 1) {
		return false;
	} else {
		return true;
	}






	echo "<textarea>";

	print_r($result);
	echo "</textarea>";
	exit();

	return $result;







	$theresponse = syncMailchimp("derekleemadden@gmail.com","Derek","Madden");

	echo "<pre>";
	print_r($theresponse);
	echo "</pre>";
	exit();
	

	function syncMailchimp($email,$firstname,$lastname) {

	    $list_id = '706ef9c949';
		$authToken = '3c1265f682ab8bfea330bb140c647dae-us1';
		// The data to send to the API

		$postData = array(
		    "email_address" => $email,
		    "status" => "subscribed",
		    "merge_fields" => array(
		    "FNAME"=> $firstname,
		    "LNAME"=> $lastname)
		);

		// Setup cURL
		$ch = curl_init('https://us1.api.mailchimp.com/3.0/lists/'.$list_id.'/members/');
		curl_setopt_array($ch, array(
		    CURLOPT_POST => TRUE,
		    CURLOPT_RETURNTRANSFER => TRUE,
		    CURLOPT_HTTPHEADER => array(
		        'Authorization: apikey '.$authToken,
		        'Content-Type: application/json'
		    ),
		    CURLOPT_POSTFIELDS => json_encode($postData)
		));
		// Send the request
		$response = curl_exec($ch);

	}

?>