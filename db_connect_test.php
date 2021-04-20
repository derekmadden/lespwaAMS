<?php


	$_SESSION['live'] = 1;
	$ip_server = $_SERVER['SERVER_ADDR'];

	if($ip_server == '127.0.0.1'){
		$_SESSION['live'] = 0;
	}


	echo 'live = ' . $_SESSION['live'];

	echo '<textarea>';
	print_r(getAssets());
	echo '</textarea>';

	exit();


	function getAssets(){

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


			$sql = "SELECT * FROM assets where country = 'Guatemala'";

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




	function getData($country,$location,$condition,$searchTerm){
		$servername = "localhost";
		$serverusername = "root";
		$serverpass = "derek123";
		$dbname = "moh_ams";


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

			// echo 'here now';

			// echo $country;
			// echo $location;
			// echo $condition;

			


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
		$servername = "localhost";
		$serverusername = "root";
		$serverpass = "derek123";
		$dbname = "moh_ams";


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
		$servername = "localhost";
		$serverusername = "root";
		$serverpass = "derek123";
		$dbname = "moh_ams";

		$sql = "INSERT INTO assets (".$column.") VALUES ('".$assetName."')";
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

	function updateAsset($id,$update,$value){
		$servername = "localhost";
		$serverusername = "root";
		$serverpass = "derek123";
		$dbname = "moh_ams";

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


	function updateUser($firstName, $lastName, $role){
		$servername = "localhost";
		$serverusername = "root";
		$serverpass = "derek123";
		$dbname = "moh_ams";

		$sql = "update users set role = '".$role."' where first_name = '".$firstName."' and last_name = '".$lastName."'";
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
		$servername = "localhost";
		$serverusername = "root";
		$serverpass = "derek123";
		$dbname = "moh_ams";

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


	function getUsers($search){
		$servername = "localhost";
		$serverusername = "root";
		$serverpass = "derek123";
		$dbname = "moh_ams";

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
		$servername = "localhost";
		$serverusername = "root";
		$serverpass = "derek123";
		$dbname = "moh_ams";

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