<?php


  session_start();

  if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true){
    header("location: login.html");
    die();
  }


  require_once('getHTML.php');
  require_once('db_connect.php');
  require_once('SimpleXLSXGen.php');

  // var_dump($_SESSION);
  // exit();


  

  buildTable();

  function buildTable(){
    $_REQUEST['tableRows'] = '';

    if ($_SESSION['country'] == 'All'){
      $_REQUEST['countryDropDown'] = '<option value="" selected disabled>Country</option>';
      $_REQUEST['countryDropDown'] .= '<option>All</option>';
    } else {
      $_REQUEST['countryDropDown'] = '<option value="" selected disabled>'.$_SESSION['country'].'</option>';
    }
    $_REQUEST['priorityDropDown'] = '<option value="" selected disabled>Priority</option>';
    $_REQUEST['priorityDropDown'] .= '<option>All</option>';
    $_REQUEST['statusDropDown'] = '<option value="" selected disabled>Status</option>';
    $_REQUEST['statusDropDown'] .= '<option>All</option>';

      // echo $startDate;
      // exit();

    if ($_SESSION['role'] == 'admin'){
      $country = '';
      $priority = '';
      $status = '';

    } elseif ($_SESSION['country'] == 'All' && $_SESSION['role'] != 'maintenance'){
      $country = '';
      $priority = '';
      $status = '';


      if (!empty($_POST['country']) || !empty($_POST['priority']) || !empty($_POST['status'])){
        if (!empty($_POST['country'])){
          if ($_POST['country'] != 'All') {
            $country = $_POST['country'];
          }
        }

        if (!empty($_POST['priority'])){
          if ($_POST['priority'] != 'All') {
            $priority = $_POST['priority'];
          }
        }

        if (!empty($_POST['status'])){
          if ($_POST['status'] != 'All') {
            $status = $_POST['status'];
          }
        }
      }
    } else {
      $country = $_SESSION['country'];
      $priority = '';
      $status = '';


      if (!empty($_POST['priority']) || !empty($_POST['status'])){

        if (!empty($_POST['priority'])){
          if ($_POST['priority'] != 'All') {
            $priority = $_POST['priority'];
          }
        }

        if (!empty($_POST['status'])){
          if ($_POST['status'] != 'All') {
            $status = $_POST['status'];
          }
        }
      }
    }

    

    // echo $country . "<br>";
    // echo $priority . "<br>";
    // echo $status . "<br>";
    // exit();


    // if ($_SESSION['role'] == 'maintenance'){
    //   $tickets = getTicketDataByAssignedWorker($_SESSION['userId']);
    // } else {
      $tickets = getTicketData($country,$priority,$status,'');
    // }

    if ($tickets->num_rows > 0) {

      // output data of each row
      $count = 0;
      $countries = array();
      $priorities = array();
      $statuses = array();

      



      while($row = $tickets->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["item_name"]. " location: " . $row["location"]. "<br>";

        if ($count == 0){
          array_push($countries, $row["country"]);
          $_REQUEST['countryDropDown'] .= '<option>'.$row["country"].'</option>';

          array_push($priorities, $row["priority"]);
          $_REQUEST['priorityDropDown'] .= '<option>'.$row["priority"].'</option>';

          array_push($statuses, $row["status"]);
          $_REQUEST['statusDropDown'] .= '<option>'.$row["status"].'</option>';
          
        } else {
          if (in_array($row["country"], $countries, true)) {
              // echo 'in array, skipping';
          } else {
              array_push($countries, $row["country"]);
              $_REQUEST['countryDropDown'] .= '<option>'.$row["country"].'</option>';
          }

          if (in_array($row["priority"], $priorities, true)) {
              // echo 'in array, skipping';
          } else {
              array_push($priorities, $row["priority"]);
              $_REQUEST['priorityDropDown'] .= '<option>'.$row["priority"].'</option>';
          }

          if (in_array($row["status"], $statuses, true)) {
              // echo 'in array, skipping';
          } else {
              array_push($statuses, $row["status"]);
              $_REQUEST['statusDropDown'] .= '<option>'.$row["status"].'</option>';
          }
        }



        $items[] = array (
            'id' => $row['id'],
            'ticket_no' => $row['ticket_no'],
            'title' => $row['title'],
            'description' => $row['description'],
            'country' => $row['country'],
            'campus' => $row['campus'],
            'location' => $row['location'],
            'sub_location' => $row['sub_location'],
            'project' => $row['project'],
            'submitter' => $row['submitter'],
            'assigned_worker' => $row['assigned_worker'],
            'priority' => $row['priority'],
            'status' => $row['status'],
            'ticket_type' => $row['ticket_type'],
            'created' => $row['created'],
            'updated' => $row['updated']
          );

        if (strcmp($row['assigned_worker'], 'Unassigned') !== 0) {
          $assigned_worker = getUserById($row['assigned_worker']);
          if ($assigned_worker->num_rows > 0) {
            while($row2 = $assigned_worker->fetch_assoc()) {
              $_REQUEST['assigned_worker'] = $row2['first_name'] . " " . $row2['last_name'];
            }
          }
        } else {
          $_REQUEST['assigned_worker'] = $row['assigned_worker'];
        }
        

        $_REQUEST['tableRows'] .= '
              <tr>'.'
                <th>'. $row["ticket_no"] . '</th>'.'
                <th>'. $row["title"] . '</th>'.'
                <th>'. $row["country"] . '</th>'.'
                <th>'. $row["campus"] . '</th>'.'
                <th>'. $row["priority"] . '</th>'.'
                <th>'. $_REQUEST["assigned_worker"] . '</th>'.'
                <th>'. $row["status"] . '</th>'.'
                <th><a href="ticket-detail?ticket_no='.$row["ticket_no"].'">More Info</a></th>';

        $count++;
      }
    }

    // $xlsx = SimpleXLSXGen::fromArray( $items );
    // $xlsx->saveAs('items.xlsx');

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

  <title>Lespwa AMS - Ticket Dashboard</title>

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
              <h6 class="m-0 font-weight-bold text-primary">Maintenance Ticket Dashboard</h6><br>
                <!-- <a href="items.xlsx" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a><hr> -->
              <div>
              <form action="tickets" method="POST">
                <div class="form-row">
                  <div class="form-group col-2">
                    <select class="form-control" name="country" id="exampleFormControlSelect1">
                      <?php echo $_REQUEST['countryDropDown'];?>
                    </select>
                  </div>
                  <div class="form-group col-2">
                    <select class="form-control"  name="priority" id="exampleFormControlSelect2">
                      <?php echo $_REQUEST['priorityDropDown'];?>
                    </select>
                  </div>
                  <div class="form-group col-2">
                    <select class="form-control"  name="status" id="exampleFormControlSelect3">
                      <?php echo $_REQUEST['statusDropDown'];?>
                    </select>
                  </div>
                  <div class="form-group col-2">
                    <input class="form-group d-none d-sm-inline-block btn btn-md btn-primary shadow-sm" type="submit" value="Get Selected Values">
                  </div>
                  <!-- <a href="#" class="form-group d-none d-sm-inline-block btn btn-md btn-primary shadow-sm">  Go  </a> -->
                </div>
              </form>
            </div>
          </div>

          <form action="detail" method="POST">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Ticket #</th>
                      <th>Ticket Name</th>
                      <th>Country</th>
                      <th>Campus</th>
                      <th>Priority</th>
                      <th>Assigned</th>
                      <th>Status</th>
                      <th></th>
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
