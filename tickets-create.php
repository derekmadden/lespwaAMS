<?php


  session_start();

  if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true){
    header("location: login.html");
    die();
  }


  require_once('getHTML.php');
  require_once('db_connect.php');
  require_once('SimpleXLSXGen.php');

  buildTable();

  function buildTable(){
    $_REQUEST['countryDropDown'] = '';
    $_REQUEST['campusDropDown'] = '';
    $_REQUEST['locationDropDown'] = '';
    $_REQUEST['sublocationDropDown'] = '';

    $country = '';
    $location = '';

    $countries = getCountries();
    $campuses = getCampuses();
    $locations = getLocations();
    $sublocations = getSubLocations();
    
    if ($countries->num_rows > 0) {

      // output data of each row
      $count = 0;

      while($row = $countries->fetch_assoc()) {
        $_REQUEST['countryDropDown'] .= '<option>'.$row["country"].'</option>';
        $count++;
        }
    }

    if ($campuses->num_rows > 0) {

      // output data of each row
      $count = 0;

      while($row = $campuses->fetch_assoc()) {
        if(strlen(trim($row["campus"])) > 0){
           $_REQUEST['campusDropDown'] .= '<option>'.$row["campus"].'</option>';
        }
        
        $count++;
        }
    }

    if ($locations->num_rows > 0) {

      // output data of each row
      $count = 0;

      while($row = $locations->fetch_assoc()) {
        if(strlen(trim($row["location"])) > 0){
          $_REQUEST['locationDropDown'] .= '<option>'.$row["location"].'</option>';
        }

        $count++;
        }
    }

    if ($sublocations->num_rows > 0) {

      // output data of each row
      $count = 0;

      while($row = $sublocations->fetch_assoc()) {
        if(strlen(trim($row["sub_location"])) > 0){
          $_REQUEST['sublocationDropDown'] .= '<option>'.$row["sub_location"].'</option>';
        }

        $count++;
        }
    }

  }


  if (!empty($_POST)){
    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";
    // exit();
    $count = 0;
    foreach ($_POST as $col => $val){
      if (!is_array($col)){
        if ($count == 0){
          echo 'col = ' . $col . '& val = ' . $val;
          $result = createTicket($col,$val);
          echo $result;
        }
        if (!empty($val)){
          // update asset
          $_REQUEST['id'] = getLastId();
          $result = updateTicket($_REQUEST['id'],$col,$val);
        }
      }
      $count++;
    }


    // if (empty($_POST['major_asset'])){
    //   $result = updateAsset($_REQUEST['id'],'major_asset','off');
    // }
    // if (empty($_POST['deferred_replacement'])){
    //   $result = updateAsset($_REQUEST['id'],'deferred_replacement','off');
    // }
  }


  while(true){
    $cur_date = date('d').date('m').date('y');
    $invoice = $cur_date;
    $customer_id = rand(00000 , 99999);
    $_REQUEST['ticket_no'] = $invoice.'-'.$customer_id;

    if (checkIfTicketNoExists($_REQUEST['ticket_no']) === false) {
        break;
    }
  }



?>


<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Lespwa AMS - New Ticket</title>

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
          <!-- <h1 class="h3 mb-2 text-gray-800">Detailed View</h1> -->
          <!-- <p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below. For more information about DataTables, please visit the <a target="_blank" href="https://datatables.net">official DataTables documentation</a>.</p> -->
        <form action="ticket-detail" method="POST">
          <input type="hidden" id="createTicket" name="createTicket" value="1">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary"><input type="text" class="form-control" id="title" name="title" placeholder="Maintenance Issue" required></h6>
            </div>
            <div class="card-body">
              <!-- The styling for this basic card example is created by using default Bootstrap utility classes. By using utility classes, the style of the card component can be easily modified with no need for any custom CSS! -->
              <!-- Asset -->

              
                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                </div>
                <hr>
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label for="country">Country</label>
                    <select class="form-control" name="country" id="country">
                      <?php echo $_REQUEST['countryDropDown'];?>
                    </select>
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="campus">Campus</label>
                    <select class="form-control" name="campus" id="campus">
                      <?php echo $_REQUEST['campusDropDown'];?>
                      <option>Other</option>
                    </select>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label for="location">Location</label>
                    <select class="form-control" name="location" id="location">
                      <?php echo $_REQUEST['locationDropDown'];?>
                      <option>Other</option>
                    </select>
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="sub_location">Sub-Location</label>
                    <select class="form-control" name="sub_location" id="sub_location">
                      <?php echo $_REQUEST['sublocationDropDown'];?>
                      <option>Other</option>
                    </select>
                  </div>
                </div>
                

                <input type="hidden" id="ticket_no" name="ticket_no" value="<?php echo $_REQUEST['ticket_no'];?>">
                <input type="hidden" id="submitter" name="submitter" value="<?php echo $_SESSION['userId'];?>">
                <input type="hidden" id="assigned_worker" name="assigned_worker" value="Unassigned">
                <input type="hidden" id="ticket_type" name="ticket_type" value="Standard">
                <input type="hidden" id="priority" name="priority" value="Unassigned">
                <input type="hidden" id="status" name="status" value="Open">
                <button type="submit" class="btn btn-primary">Add New Ticket</button>
              </div>
            </div>
        </form>


          <!-- DataTales Example -->
          
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
