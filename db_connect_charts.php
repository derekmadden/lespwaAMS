<?php

	session_start();

	$_SESSION['live'] = 1;
	$ip_server = $_SERVER['SERVER_ADDR'];

	if($ip_server == '127.0.0.1'){
		$_SESSION['live'] = 0;
	}


	if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    }

    // var_dump($_REQUEST);

    if (isset($_POST["chartType"])){
	  $_SESSION['chartType'] = $_POST["chartType"];
	} else {
	  $_SESSION['chartType'] = null;
	}

	if (isset($_POST["dataType"])){
	  $_SESSION['dataType'] = $_POST["dataType"];
	} else {
	  $_SESSION['dataType'] = null;
	}

	if (isset($_POST["dataNeeds"])){
	  $_SESSION['dataNeeds'] = $_POST["dataNeeds"];
	} else {
	  $_SESSION['dataNeeds'] = null;
	}

    

	if(!empty($_SESSION['chartType'])){
		buildChartData($_SESSION['chartType']);
	} else {
		getAreaChartData();
	}




	function buildChartData($chartType){

		if(strpos($chartType, 'pie') !== false){
			echo getPieChartData();
		} elseif(strpos($chartType, 'bar') !== false){
			echo getBarChartData();
		} elseif(strpos($chartType, 'area') !== false){
			echo getAreaChartData();
		} else {
			echo 'error';
		}
	}


	function getPieChartData(){
		$_REQUEST['Repairs_count'] = '';
		$_REQUEST['Replacements_count'] = '';



		$pieData = array();

		if(!empty($_SESSION['dataNeeds'])){
			$chartData = array();
			$pieces = explode("_", $_SESSION['dataNeeds']);

			for ($x = 0; $x < sizeof($pieces); $x++) {
				$value = $pieces[$x]."_".$_SESSION['dataType'];
				// echo $value;
				if (strpos($pieces[$x], 'Replacements') !== false){
					// echo 'made it to replacements';
					// $sql = "SELECT SUM(replacement_cost) as ".$value." FROM assets where current_condition = 'Needs Replaced'";
					$sql = "SELECT COUNT(*)  ".$value." FROM assets where current_condition = 'Needs Replaced'";
				} else if (strpos($pieces[$x], 'Repairs') !== false){
					// echo 'made it to repairs';
					// $sql = "SELECT SUM(replacement_cost) as ".$value." FROM assets where current_condition = 'Needs Repaired'";
					$sql = "SELECT COUNT(*) as ".$value." FROM assets where current_condition = 'Needs Repaired'";
				}

				// echo $sql;

				$data = getMySQLData($sql);
				 while($row = mysqli_fetch_array($data)){
					$_REQUEST[$value] = $value;
					$arrayData = array($_REQUEST[$value] => $row[$value]);
					array_push($pieData, $arrayData);
				}
				
			}

			return json_encode($pieData);

		} else {
			echo 'no dataNeeds';
		}
	}



	function getBarChartData(){


		// dataNeeds=item_name-replacement_cost



		$barData = array();
		$chartData = array();

		if(!empty($_SESSION['dataNeeds'])){
			if (strpos($_SESSION['dataNeeds'], '-') !== false){
				$pieces = explode("-", $_SESSION['dataNeeds']);
				// $sql = "SELECT " .$pieces[0]. ",". $pieces[1] . " FROM ".$_REQUEST['country']."_assets where current_condition = 'Needs Replaced'";
				$sql = "SELECT " .$pieces[0]. ",". $pieces[1] . " FROM assets where current_condition = 'Needs Replaced'";
			}

			$data = getMySQLData($sql);
		 	while($row = mysqli_fetch_array($data)){
				$arrayData = array($row[$pieces[0]] => $row[$pieces[1]]);
				array_push($barData, $arrayData);
			}

			return json_encode($barData);

		} else {
			echo 'no dataNeeds';
		}
	}


	function getAreaChartData(){


		$yearData = array();
		$nextYear = 0;
		$thirdYear = 0;
		$fourthYear = 0;
		$fifthYear = 0;


		$sql = "SELECT * from assets";

		$yearOne = array();
		$yearTwo = array();
		$yearThree = array();
		$yearFour = array();
		$yearFive = array();

		$data = getMySQLData($sql);
	 	while($row = mysqli_fetch_array($data)){
	 		
	 		$usefulMonths = $row['useful_life']*12;
	 		$monthsToEOL = calculateEOL($row['useful_life'],$row['purchase_date']);


	 		$currYear = date('Y');
	 		$twoYearsOut = $currYear + 1;
	 		$threeYearsOut = $currYear+2;
	 		$fourYearsOut = $currYear+3;
	 		$fiveYearsOut = $currYear+4;

	 		$monthsLeftinYear = calculateMonthsLeftThisYear($twoYearsOut);
			$nextYear = $monthsLeftinYear+12;
			$thirdYear = $monthsLeftinYear+24;
			$fourthYear = $monthsLeftinYear+36;
			$fifthYear = $monthsLeftinYear+48;





			$month = 12;
			$addedMonths = $monthsLeftinYear+12;


		  	if ($row['purchase_cost'] > 500 && $monthsToEOL <= $monthsLeftinYear){
		  		//2021
			  	$arrayData = array( "id" => $row["id"], "item_name" => $row["item_name"],  "country" => $row["country"], "location" => $row["location"], "sub_location" => $row["sub_location"], "purchase_date" => $row["purchase_date"],  "purchase_cost" => $row["purchase_cost"],  "current_condition" => $row["current_condition"],  "useful_life" => $row["useful_life"], "monthsToEOL" => $monthsToEOL, "year" => $currYear);
				array_push($yearData, $arrayData);

				$yearOneData = array( "id" => $row["id"], "item_name" => $row["item_name"],  "country" => $row["country"], "location" => $row["location"], "sub_location" => $row["sub_location"], "purchase_date" => $row["purchase_date"],  "purchase_cost" => $row["purchase_cost"],  "current_condition" => $row["current_condition"],  "useful_life" => $row["useful_life"], "monthsToEOL" => $monthsToEOL, "year" => $currYear);
				array_push($yearOne, $yearOneData);


			} elseif ($row['purchase_cost'] > 500 && $monthsToEOL <= $nextYear && $monthsToEOL > $monthsLeftinYear){
				//2022
				$arrayData = array( "id" => $row["id"], "item_name" => $row["item_name"],  "country" => $row["country"], "location" => $row["location"], "sub_location" => $row["sub_location"], "purchase_date" => $row["purchase_date"],  "purchase_cost" => $row["purchase_cost"],  "current_condition" => $row["current_condition"],  "useful_life" => $row["useful_life"], "monthsToEOL" => $monthsToEOL, "year" => $twoYearsOut);
				array_push($yearData, $arrayData);

				$yearTwoData = array( "id" => $row["id"], "item_name" => $row["item_name"],  "country" => $row["country"], "location" => $row["location"], "sub_location" => $row["sub_location"], "purchase_date" => $row["purchase_date"],  "purchase_cost" => $row["purchase_cost"],  "current_condition" => $row["current_condition"],  "useful_life" => $row["useful_life"], "monthsToEOL" => $monthsToEOL, "year" => $twoYearsOut);
				array_push($yearTwo, $yearTwoData);


			} elseif ($row['purchase_cost'] > 500 && $monthsToEOL <= $thirdYear && $monthsToEOL > $nextYear){
				//2023
				$arrayData = array( "id" => $row["id"], "item_name" => $row["item_name"],  "country" => $row["country"], "location" => $row["location"], "sub_location" => $row["sub_location"], "purchase_date" => $row["purchase_date"],  "purchase_cost" => $row["purchase_cost"],  "current_condition" => $row["current_condition"],  "useful_life" => $row["useful_life"], "monthsToEOL" => $monthsToEOL, "year" => $threeYearsOut);
				array_push($yearData, $arrayData);

				$yearThreeData = array( "id" => $row["id"], "item_name" => $row["item_name"],  "country" => $row["country"], "location" => $row["location"], "sub_location" => $row["sub_location"], "purchase_date" => $row["purchase_date"],  "purchase_cost" => $row["purchase_cost"],  "current_condition" => $row["current_condition"],  "useful_life" => $row["useful_life"], "monthsToEOL" => $monthsToEOL, "year" => $threeYearsOut);
				array_push($yearThree, $yearThreeData);

			} elseif ($row['purchase_cost'] > 500 && $monthsToEOL <= $fourthYear && $monthsToEOL > $thirdYear){
				//2024
				$arrayData = array( "id" => $row["id"], "item_name" => $row["item_name"],  "country" => $row["country"], "location" => $row["location"], "sub_location" => $row["sub_location"], "purchase_date" => $row["purchase_date"],  "purchase_cost" => $row["purchase_cost"],  "current_condition" => $row["current_condition"],  "useful_life" => $row["useful_life"], "monthsToEOL" => $monthsToEOL, "year" => $fourYearsOut);
				array_push($yearData, $arrayData);

				$yearFourData = array( "id" => $row["id"], "item_name" => $row["item_name"],  "country" => $row["country"], "location" => $row["location"], "sub_location" => $row["sub_location"], "purchase_date" => $row["purchase_date"],  "purchase_cost" => $row["purchase_cost"],  "current_condition" => $row["current_condition"],  "useful_life" => $row["useful_life"], "monthsToEOL" => $monthsToEOL, "year" => $fourYearsOut);
				array_push($yearFour, $yearFourData);

			} elseif ($row['purchase_cost'] > 500 && $monthsToEOL <= $fifthYear && $monthsToEOL > $fourthYear){
				//2025
				$arrayData = array( "id" => $row["id"], "item_name" => $row["item_name"],  "country" => $row["country"], "location" => $row["location"], "sub_location" => $row["sub_location"], "purchase_date" => $row["purchase_date"],  "purchase_cost" => $row["purchase_cost"],  "current_condition" => $row["current_condition"],  "useful_life" => $row["useful_life"], "monthsToEOL" => $monthsToEOL, "year" => $fiveYearsOut);
				array_push($yearData, $arrayData);

				$yearFiveData = array( "id" => $row["id"], "item_name" => $row["item_name"],  "country" => $row["country"], "location" => $row["location"], "sub_location" => $row["sub_location"], "purchase_date" => $row["purchase_date"],  "purchase_cost" => $row["purchase_cost"],  "current_condition" => $row["current_condition"],  "useful_life" => $row["useful_life"], "monthsToEOL" => $monthsToEOL, "year" => $fiveYearsOut);
				array_push($yearFive, $yearFiveData);
			} else {
				//skip
			}
		}

		foreach ($yearData as $key => $row)
		{
		    $year[$key] = $row['year'];
		}
		array_multisort($year, SORT_ASC, $yearData);

		// print_r($yearOne);
		// print_r(json_encode($yearOneData));
		// exit();

		
		$yearOneSum = array_sum(array_column($yearOne, "purchase_cost"));
		$yearTwoSum = array_sum(array_column($yearTwo, "purchase_cost"));
		$yearThreeSum = array_sum(array_column($yearThree, "purchase_cost"));
		$yearFourSum = array_sum(array_column($yearFour, "purchase_cost"));
		$yearFiveSum = array_sum(array_column($yearFive, "purchase_cost"));

		$chartData = array( $currYear => $yearOneSum, $twoYearsOut => $yearTwoSum, $threeYearsOut => $yearThreeSum, $fourYearsOut => $yearFourSum, $fiveYearsOut => $yearFiveSum);
		// print_r(json_encode($chartData));
		return json_encode($chartData);






		// print_r(json_encode($yearData));
		// exit();
		// return json_encode($yearData);
	}

	function calculateEOL($usefulLife,$purchaseDateFromSQL){

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


	function calculateMonthsLeftThisYear($twoYearsOut){
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




	function getMySQLData($sql){
		

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

	
	
?>