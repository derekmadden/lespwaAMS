<?php


  session_start();

  if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true){
    header("location: login.html");
    die();
  }


  require_once('getHTML.php');
  require_once('db_connect.php');
  require_once('SimpleXLSXGen.php');

  if ($_SESSION['live'] == 1){
    $_REQUEST['appendPhpExtension'] = '';
  } else {
    $_REQUEST['appendPhpExtension'] = '.php';
  }
  
  

  buildTable();




  function buildTable(){
    $_REQUEST['tableRows'] = '';
    if ($_SESSION['country'] == 'All'){
      $_REQUEST['countryDropDown'] = '<option value="" selected disabled>Country</option>';
      $_REQUEST['countryDropDown'] .= '<option>All</option>';
    } else {
      $_REQUEST['countryDropDown'] = '<option value="" selected disabled>'.$_SESSION['country'].'</option>';
    }
    $_REQUEST['locationDropDown'] = '<option value="" selected disabled>Location</option>';
    $_REQUEST['locationDropDown'] .= '<option>All</option>';
    $_REQUEST['conditionDropDown'] = '<option value="" selected disabled>Condition</option>';
    $_REQUEST['conditionDropDown'] .= '<option>All</option>';
      // echo $startDate;
      // exit();

    $country = '';
    $location = '';
    $condition = '';


    if ($_SESSION['country'] == 'All'){

      if (!empty($_POST['country']) || !empty($_POST['location']) || !empty($_POST['condition'])){
        if (!empty($_POST['country'])){
          if ($_POST['country'] != 'All') {
            $country = $_POST['country'];
          }
        }

        if (!empty($_POST['location'])){
          if ($_POST['location'] != 'All') {
            $location = $_POST['location'];
          }
        }

        if (!empty($_POST['condition'])){
          if ($_POST['condition'] != 'All') {
            $condition = $_POST['condition'];
          }
        }
      }
    } else {

      $country = $_SESSION['country'];
      $priority = '';
      $status = '';

      if (!empty($_POST['location'])){
          if ($_POST['location'] != 'All') {
            $location = $_POST['location'];
          }
        }

        if (!empty($_POST['condition'])){
          if ($_POST['condition'] != 'All') {
            $condition = $_POST['condition'];
          }
        }
    }

    $assets = getData($country,$location,$condition,'');

    $items = array();
    $items[] = array (
            'id' => 'id',
            'item_name' => 'Item Name',
            'country' => 'Country',
            'campus' => 'Campus',
            'location' => 'Location',
            'sub_location' => 'Sub Location',
            'major_asset' => 'Major Asset',
            'asset_type' => 'Asset Type',
            'category' => 'Category',
            'purchase_date' => 'Purchase Date',
            'purchase_cost' => 'Purchase Cost',
            'replacement_cost' => 'Replacement Cost',
            'useful_life' => 'Useful Life',
            'current_condition' => 'Current Condition',
            'deferred_replacement' => 'deferred_replacement',
            'known_issues' => 'Known Issues',
            'other_comments' => 'Other Comments'
          );

    
    if ($assets->num_rows > 0) {

      // output data of each row
      $count = 0;
      $countries = array();
      $locations = array();
      $conditions = array();

      



      while($row = $assets->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["item_name"]. " location: " . $row["location"]. "<br>";
        // var_dump($row);
        // exit();

        if ($count == 0){
          array_push($countries, $row["country"]);
          $_REQUEST['countryDropDown'] .= '<option>'.$row["country"].'</option>';

          array_push($locations, $row["location"]);
          $_REQUEST['locationDropDown'] .= '<option>'.$row["location"].'</option>';

          array_push($conditions, $row["current_condition"]);
          $_REQUEST['conditionDropDown'] .= '<option>'.$row["current_condition"].'</option>';
          
        } else {
          if (in_array($row["country"], $countries, true)) {
              // echo 'in array, skipping';
          } else {
              array_push($countries, $row["country"]);
              $_REQUEST['countryDropDown'] .= '<option>'.$row["country"].'</option>';
          }

          if (in_array($row["location"], $locations, true)) {
              // echo 'in array, skipping';
          } else {
              array_push($locations, $row["location"]);
              $_REQUEST['locationDropDown'] .= '<option>'.$row["location"].'</option>';
          }

          if (in_array($row["current_condition"], $conditions, true)) {
              // echo 'in array, skipping';
          } else {
              array_push($conditions, $row["current_condition"]);
              $_REQUEST['conditionDropDown'] .= '<option>'.$row["current_condition"].'</option>';
          }
        }





        $items[] = array (
            'id' => $row['id'],
            'item_name' => $row['item_name'],
            'country' => $row['country'],
            'campus' => $row['campus'],
            'location' => $row['location'],
            'sub_location' => $row['sub_location'],
            'major_asset' => $row['major_asset'],
            'asset_type' => $row['asset_type'],
            'category' => $row['category'],
            'purchase_date' => $row['purchase_date'],
            'purchase_cost' => $row['purchase_cost'],
            'replacement_cost' => $row['replacement_cost'],
            'useful_life' => $row['useful_life'],
            'current_condition' => $row['current_condition'],
            'deferred_replacement' => $row['deferred_replacement'],
            'known_issues' => $row['known_issues'],
            'other_comments' => $row['other_comments']
          );
        

        $_REQUEST['tableRows'] .= '
              <tr>'.'
                <th><a href="detail'.$_REQUEST['appendPhpExtension'].'?id='.$row["id"].'">'. $row["item_name"] . '</a></th>'.'
                <th>'. $row["country"] . '</th>'.'
                <th>'. $row["campus"] . '</th>'.'
                <th>'. $row["location"] . '</th>'.'
                <th>'. $row["category"] . '</th>';

                  //check if Asset Needs Repaired
                if(strpos($row['current_condition'], 'Needs Repaired') !== false){
                    $_REQUEST['tableRows'] .= '<th style="color: red;"">'. $row['current_condition'] . ' </th>';
                
                  //check if Asset Needs Replaced
                } else if(strpos($row['current_condition'], 'Needs Replaced') !== false){
                    $_REQUEST['tableRows'] .= '<th style="color: red;"">'. $row['current_condition'] . ' </th>';
                
                } else {
                  $_REQUEST['tableRows'] .= '<th>'. $row['current_condition'] . '</th>';
                }

          $_REQUEST['tableRows'] .= '
                <th>'. $row["useful_life"] . '</th>'.'
              </tr>';

        $count++;
      }
    } else {
      echo "0 results";
      // exit();
    }

    $xlsx = SimpleXLSXGen::fromArray( $items );
    $xlsx->saveAs('items.xlsx');

  }


  



    // echo "<textarea rows=80 cols=100>";
    // print_r($_REQUEST['countryDropDown'] . "\n");
    // print_r($_REQUEST['locationDropDown'] . "\n");
    // print_r($_REQUEST['conditionDropDown'] . "\n");
    // echo "</textarea>";
    // exit;

?>


<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Lespwa AMS - Assets</title>

  <!-- Custom fonts for this template -->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">

  <!-- Custom styles for this page -->
  <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <?php $_SESSION['sidebar']=getSidebarHTML();echo $_SESSION['sidebar'];?>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <?php $_SESSION['header']=getHeaderHTML();echo $_SESSION['header'];?>

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <!-- <h1 class="h3 mb-2 text-gray-800">Tables</h1>
          <p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below. For more information about DataTables, please visit the <a target="_blank" href="https://datatables.net">official DataTables documentation</a>.</p> -->



          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">All Assets</h6><br>
                <a href="items.xlsx" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a><hr>
              <div>
              Sort by:
              <form action="assets<?php echo $_REQUEST['appendPhpExtension'];?>" method="POST">
                <div class="form-row">
                  <div class="form-group col-2">
                    <select class="form-control" name="country" id="exampleFormControlSelect1">
                      <?php echo $_REQUEST['countryDropDown'];?>
                    </select>
                  </div>
                  <!-- <div class="form-group col-2">
                    <select class="form-control"  name="location" id="exampleFormControlSelect2">
                      <?php echo $_REQUEST['locationDropDown'];?>
                    </select>
                  </div> -->
                  <div class="form-group col-2">
                    <select class="form-control"  name="condition" id="exampleFormControlSelect3">
                      <?php echo $_REQUEST['conditionDropDown'];?>
                    </select>
                  </div>
                  <input class="form-group d-none d-sm-inline-block btn btn-md btn-primary shadow-sm" type="submit" value="Get Selected Values">
                  <!-- <a href="#" class="form-group d-none d-sm-inline-block btn btn-md btn-primary shadow-sm">  Go  </a> -->
                </div>
              </form>
            </div>
          </div>

          <form action="detail<?php echo $_REQUEST['appendPhpExtension'];?>" method="POST">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Asset Name</th>
                      <th>Country</th>
                      <th>Campus</th>
                      <th>Location</th>
                      <th>Category</th>
                      <th>Condition</th>
                      <th>Useful Life</th>
                    </tr>
                  </thead>
                  <tbody style="font-size: .85rem;">
                      <?php echo $_REQUEST['tableRows'];?>
                  </tbody>
                </table>
              </div>
            </div>
          </form>


          </div>
        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <?php $_SESSION['footer']=getFooterHTML();echo $_SESSION['footer'];?>

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <?php $_SESSION['logoutModule']=getLogoutHTML();echo $_SESSION['logoutModule'];?>

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

  <!-- Page level plugins -->
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="js/demo/datatables-demo.js"></script>

  <script src="js/auth.js"></script>

</body>

</html>
