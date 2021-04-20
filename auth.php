<?php

	// print_r($_POST);
	// exit();
	session_start();
	$_SESSION['live'] = 1;
	$ip_server = $_SERVER['SERVER_ADDR'];

	if($ip_server == '127.0.0.1'){
		$_SESSION['live'] = 0;
	}

	// login('test@test.com','test');
	// exit();
	require_once('db_connect.php');







	if (isset($_REQUEST['toDo'])){
		$checkUser = false;
		$toDo = $_REQUEST['toDo'];

		if (isset($_POST['first_name'])){
			$firstName = $_POST['first_name'];

		}
		if (isset($_POST['last_name'])){
			$lastName = $_POST['last_name'];

		}
		if (isset($_POST['email'])){
			$email = $_POST['email'];

		}
		if (isset($_POST['password'])){
			$password = $_POST['password'];
		}

		if($toDo === 'register'){
			$userExists = userExists($email);

			if ($userExists === true){
				echo 'There is already a user with that email. Try logging in.';
			} else {
				register($firstName,$lastName,$email,$password);
			}

		} else if($toDo === 'login'){
			login($email,$password);

		} else if($toDo === 'logout'){
			logout();
		} else {
			echo 'error 67679, no ToDo';
		}
	} else {
		echo 'bad';
	}


	function register($firstName,$lastName,$email,$password){

		if ($_SESSION['live'] == 1){
			$servername = "localhost";
			$serverusername = "bplzzpmn_derek";
			$serverpass = "M4dd3n123!";
			$dbname = "bplzzpmn_moh_assets";
		} else {
			$servername = "localhost";
			$serverusername = "root";
			$serverpass = "derek123";
			$dbname = "moh_ams";
		}

		// $sql = "SELECT * FROM users where email='".$email."' and password='".$password."'";


		$sql = "INSERT INTO users (first_name, last_name, email, password) VALUES ('".$firstName."', '".$lastName."', '".$email."', '".$password."')";
		// echo $sql . "<br>";

		// Create connection
		$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}

		
		$result = $conn->query($sql);
		$conn->close();

		$_SESSION['fullName'] = $firstName." ".$lastName;
		$_SESSION['loggedIn'] = true;
		echo 'true';



		syncMailchimp($email,$firstName,$lastName);

	}


	function login($email,$password){

		if ($_SESSION['live'] == 1){
			$servername = "localhost";
			$serverusername = "bplzzpmn_derek";
			$serverpass = "M4dd3n123!";
			$dbname = "bplzzpmn_moh_assets";
		} else {
			$servername = "localhost";
			$serverusername = "root";
			$serverpass = "derek123";
			$dbname = "moh_ams";
		}

		$sql = "SELECT * FROM users where email='".$email."' and password='".$password."'";
		

		// Create connection
		$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}

		// echo $sql;

		
		$result = $conn->query($sql);
		$conn->close();


		if ($result->num_rows > 0) {


			// set user session data //
			while($row = $result->fetch_assoc()) {
		        $_SESSION['fullName'] = $row['first_name']. " " .$row['last_name'];
		        $_SESSION['userId'] = $row['id'];
		        $_SESSION['role'] = $row['role'];
		        $_SESSION['country'] = $row['country'];
		    }

			$_SESSION['loggedIn'] = true;
			echo 'true';

		} else {
			echo 'false';
		}

	}


	function userExists($email){

		if ($_SESSION['live'] == 1){
			$servername = "localhost";
			$serverusername = "bplzzpmn_derek";
			$serverpass = "M4dd3n123!";
			$dbname = "bplzzpmn_moh_assets";
		} else {
			$servername = "localhost";
			$serverusername = "root";
			$serverpass = "derek123";
			$dbname = "moh_ams";
		}


		$sql = "SELECT * FROM users where email='".$email."'";

		// Create connection
		$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}

		$result = $conn->query($sql);
		$conn->close();
		
		if ($result->num_rows > 0) {
			return true;
		} else {
			return false;
		}

	}


	function logout(){
		session_destroy();
	}




	function syncMailchimp($email,$firstname,$lastname) {

	    $list_id = '706ef9c949';
		$authToken = getApiKey('mailchimp');
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






	function sendConfirmation($email){
		// the message

		//M@dden12345!


		$msg = "Hello!<br>This email is to confirm your signup with LespwaAMS.";




		// use wordwrap() if lines are longer than 70 characters
		$msg = wordwrap($msg,70);

		// send email
		mail($email,"Confirmation of AMS Registration",$msg);

		return;
	}


?>