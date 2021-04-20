<?php
	
	$_SESSION['live'] = 1;
	$ip_server = $_SERVER['SERVER_ADDR'];

	if($ip_server == '127.0.0.1'){
		$_SESSION['live'] = 0;
	}

	// echo 'live = ' . $_SESSION['live'];
	// exit();



	///////////////////////////////////////////////////////////////////
	//                   DATA BUILDING SYSTEM                        //
	///////////////////////////////////////////////////////////////////


	function getCountries(){

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

		$sql = "SELECT DISTINCT country FROM assets";

		
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

		return $result;
	}

	function getCampuses(){

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

		$sql = "SELECT DISTINCT campus FROM assets";

		
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

		return $result;
	}

	function getLocations(){

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

		$sql = "SELECT DISTINCT location FROM assets";

		
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

		return $result;
	}

	function getSubLocations(){

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

		$sql = "SELECT DISTINCT sub_location FROM assets";

		
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

		return $result;
	}


	






	///////////////////////////////////////////////////////////////////
	//                   TICKETING SYSTEM                            //
	///////////////////////////////////////////////////////////////////

	function getTicketData($country,$priority,$status,$searchTerm){
		

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


		$sql = "SELECT * FROM maintenance_tickets where ";


		if (!empty($searchTerm)){

			// not used at the moment

			
		} else {

			if (!empty($country)){

				$sql .= "country = '".$country."' ";

				if (!empty($priority)){
					$sql .= "AND priority = '".$priority."' ";

					if (!empty($status)){
						$sql .= "AND status = '".$status."' ";
					}

				} elseif (!empty($status)){
					$sql .= "AND status = '".$status."' ";
				}


			// country empty, moving to priority
			} elseif (!empty($priority)){
				$sql .= "priority = '".$priority."' ";

				if (!empty($status)){
					$sql .= "AND status = '".$status."' ";
				}

			// location empty, moving to condition
			} elseif (!empty($status)){
				$sql .= "status = '".$status."' ";
			

			// no special searches, doing default;
			} else {
				$sql = "SELECT * FROM maintenance_tickets";
			}

		}



		// Create connection
		$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}

		
		$result = $conn->query($sql);
		$conn->close();

		return $result;
	}

	function getTicketDataByAssignedWorker($userId){
		

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


		$sql = "SELECT * FROM maintenance_tickets where assigned_worker = " . $userId;


		

		// Create connection
		$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}

		
		$result = $conn->query($sql);
		$conn->close();

		return $result;
	}

	function updateUserById($id,$update,$value){

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

		$sql = "update users set ".$update."= '".$value."' where id ='".$id."'";


		// Create connection
		$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}

		
		$result = $conn->query($sql);
		$conn->close();

		return $result;

	}

	function getTicketById($id){
		

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

		$sql = "SELECT * FROM maintenance_tickets where ticket_no = '".$id."'";

		if (empty($id)){
			return 'error';
		}
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

		return $result;
	}

	function getTicketCount($priority){
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
		if (empty($priority)){
			$sql = "SELECT COUNT(*) FROM maintenance_tickets";
		} else {
			$sql = "SELECT COUNT(*) FROM maintenance_tickets where priority = '".$priority."'";
		}


		// Create connection
		$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}

		
		$result = $conn->query($sql);
		$conn->close();

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				return $row['COUNT(*)'];
			}
 		}
	}


	function createTicket($column,$ticketNo){
		

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

		$sql = "INSERT INTO maintenance_tickets (".$column.") VALUES ('".$ticketNo."')";


		// Create connection
		$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}

		
		$result = $conn->query($sql);
		$conn->close();

		return $result;

	}

	
	function updateTicket($id,$update,$value){
		

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



		$sql = "update maintenance_tickets set ".$update."= '".$value."' where ticket_no ='".$id."'";


		// Create connection
		$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}

		
		$result = $conn->query($sql);
		$conn->close();

		return $result;

	}


	function updateTicketHistory($id,$update,$value){
		

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



		$sql = "update ticket_history set ".$update."= '".$value."' where ticket_no ='".$id."'";


		// Create connection
		$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}

		
		$result = $conn->query($sql);
		$conn->close();

		return $result;

	}


	function getLastIdTickets(){
		

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

		$sql = "SELECT MAX(id) FROM maintenance_tickets";


		// Create connection
		$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}

		
		$result = $conn->query($sql);
		$conn->close();

		if ($result->num_rows > 0) {

	      // output data of each row
	      while($row = $result->fetch_assoc()) {
	        // echo "id: " . $row["id"]. " - Name: " . $row["item_name"]. " location: " . $row["location"]. "<br>";
	        	return $row["MAX(id)"];
	        }
	    }

	}

	function getLastTicketId(){
		

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

		$sql = "SELECT MAX(id) FROM maintenance_tickets";


		// Create connection
		$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}

		
		$result = $conn->query($sql);
		$conn->close();

		if ($result->num_rows > 0) {

	      // output data of each row
	      while($row = $result->fetch_assoc()) {
	        // echo "id: " . $row["id"]. " - Name: " . $row["item_name"]. " location: " . $row["location"]. "<br>";
	        	return $row["MAX(id)"];
	        }
	    }

	}

	function checkIfTicketNoExists($ticketNo){
		

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

		$sql = 'SELECT * FROM maintenance_tickets WHERE ticket_no = '.$ticketNo;

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





	///////////////////////////////////////////////////////////////////
	//                   ASSET SYSTEM                                //
	///////////////////////////////////////////////////////////////////

	function getData($country,$location,$condition,$searchTerm){
		

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

		if ($country == 'All'){
			$country = '';
		}


		$sql = "SELECT * FROM assets where ";

		if (!empty($searchTerm)){

			if (strpos($searchTerm, 'getReplaced')!== false){
				$sql = "SELECT * FROM assets where current_condition = 'Needs Replaced'";

			} elseif (strpos($searchTerm, 'getRepairs')!== false){
				$sql = "SELECT * FROM assets where current_condition = 'Needs Repaired'";

			} elseif (strpos($searchTerm, 'urgentNeeds')!== false){
				$sql = "SELECT * FROM assets where current_condition = 'Needs Repaired' or current_condition = 'Needs Replaced'";

			} elseif (strpos($searchTerm, 'pieChart')!== false){
				return getPieChartData();
			} else {
				echo 'error';
			}
		} else {


			if (!empty($country)){

				$sql .= "country = '".$country."' ";

				if (!empty($location)){
					$sql .= "AND location = '".$location."' ";

					if (!empty($condition)){
						$sql .= "AND current_condition = '".$condition."' ";
					}

				} elseif (!empty($condition)){
					$sql .= "AND current_condition = '".$condition."' ";
				}


			// country empty, moving to location
			} elseif (!empty($location)){
				$sql .= "location = '".$location."' ";

				if (!empty($condition)){
					$sql .= "AND current_condition = '".$condition."' ";
				}

			// location empty, moving to condition
			} elseif (!empty($condition)){
				$sql .= "current_condition = '".$condition."' ";
			

			// no special searches, doing default;
			} else {
				$sql = "SELECT * FROM assets";
			}


		}
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

		return $result;
	}



	function getDataById($id){
		

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

		$sql = "SELECT * FROM assets where id = ".$id;

		if (empty($id)){
			return 'error';
		}
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

		return $result;
	}

	function createAsset($column,$assetName){
		

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

		$sql = "INSERT INTO assets (".$column.") VALUES ('".$assetName."')";
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

		return $result;

	}

	function updateAsset($id,$update,$value){
		

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

		$sql = "update assets set ".$update."= '".$value."' where id ='".$id."'";
		// return $sql;
		// exit();


		// Create connection
		$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}

		
		$result = $conn->query($sql);
		$conn->close();

		return $result;

	}

	function getLastId(){
		

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

		$sql = "SELECT MAX(id) FROM assets";


		// Create connection
		$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}

		
		$result = $conn->query($sql);
		$conn->close();

		if ($result->num_rows > 0) {

	      // output data of each row
	      while($row = $result->fetch_assoc()) {
	        // echo "id: " . $row["id"]. " - Name: " . $row["item_name"]. " location: " . $row["location"]. "<br>";
	        	return $row["MAX(id)"];
	        }
	    }

	}




	///////////////////////////////////////////////////////////////////
	//                   USER DATA                                   //
	///////////////////////////////////////////////////////////////////


	function getUsers($search){
		
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

		if (strpos($search, 'all')!== false){
			$sql = "SELECT * FROM users";
		} else {
			echo 'error';
		}


		// Create connection
		$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}

		
		$result = $conn->query($sql);
		$conn->close();

		return $result;
	}

	function getUserById($id){
		
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
		

		$sql = "SELECT * FROM users where id = ".$id;
		

		// Create connection
		$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}

		
		$result = $conn->query($sql);
		$conn->close();

		return $result;
	}

	function updateUser($firstName, $lastName, $role){
		

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

		$sql = "update users set role = '".$role."' where first_name = '".$firstName."' and last_name = '".$lastName."'";


		// Create connection
		$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}

		
		$result = $conn->query($sql);
		$conn->close();

		

		if ($_SESSION['role'] !== $role){
			$_SESSION['role'] = $role;
		}


		return $result;

	}

	

	///////////////////////////////////////////////////////////////////
	//                   CALCULATORS                                 //
	///////////////////////////////////////////////////////////////////

	function calculateTheEOL($usefulLife,$purchaseDateFromSQL){

		$today = date('Y-m-d');

 		$usefulMonths = $usefulLife*12;
	    $purchaseDate = date("Y-m", strtotime($purchaseDateFromSQL));

		$ts1 = strtotime($today);
		$ts2 = strtotime($purchaseDate);

		$year1 = date('Y', $ts1);
		$year2 = date('Y', $ts2);

		$month1 = date('m', $ts1);
		$month2 = date('m', $ts2);

		$diff = (($year1 - $year2) * 12) + ($month1 - $month2);
		$monthsToEOL = $usefulMonths - $diff;
		return $monthsToEOL;
	}


	function calculateMonthsLeft($twoYearsOut){
		$today = date('Y-m-d');

 		$nextYearDate = date("Y-m", strtotime($twoYearsOut."-jan"));

		$ts1 = strtotime($nextYearDate);
		$ts2 = strtotime($today);

		$year1 = date('Y', $ts1);
		$year2 = date('Y', $ts2);

		$month1 = date('m', $ts1);
		$month2 = date('m', $ts2);

		$answer = (($year1 - $year2) * 12) + ($month1 - $month2);
		return $answer;
	}



	function getCount($searchTerm){
		

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

		$sql = "SELECT COUNT(*) from assets where current_condition = '". $searchTerm."'";


		// Create connection
		$conn = new mysqli($servername, $serverusername, $serverpass, $dbname);
		// Check connection
		if ($conn->connect_error) {
		  die("Connection failed: " . $conn->connect_error);
		}
		
		
		$result = $conn->query($sql);
		$conn->close();


	    if ($result->num_rows > 0) {

	      // output data of each row
	      while($row = $result->fetch_assoc()) {
	        // echo "id: " . $row["id"]. " - Name: " . $row["item_name"]. " location: " . $row["location"]. "<br>";
	        	return $row["COUNT(*)"];
	        }
	    }
	}
?>