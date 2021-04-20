<?php


  session_start();

  if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true){
    header("location: login.html");
    die();
  }


  require_once('getHTML.php');
  require_once('db_connect.php');
  require_once('SimpleXLSXGen.php');


  if (!empty($_POST['ticket_no'])){
    $_REQUEST['ticket_no'] = $_POST['ticket_no'];
  } elseif (empty($_REQUEST['ticket_no'])){
    exit('error here');
  }


  $_REQUEST['buttonDisabled'] = 'disabled';
  if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'manager'){
    $_REQUEST['buttonDisabled'] = '';
  }

  // var_dump($_POST);
  // exit();





  $updated = false;
  //check if we are arriving here after creating an asset
  if (!empty($_POST['createTicket']) && checkIfTicketNoExists($_REQUEST['ticket_no']) === false){
    $count = 0;
    $result = createTicket('ticket_no',$_POST['ticket_no']);
    foreach ($_POST as $col => $val){
      if (!is_array($col)){
        if ($count > 0 && $col !== 'ticket_no' && !empty($val)){
          $result = updateTicket($_POST['ticket_no'],$col,$val);
        }
      }
      $count++;
    }
    $updated = true;
  }

  

  buildTable($updated);

  function buildTable($updated){

    $_REQUEST['tableRows'] = '';
    $_REQUEST['countryDropDown'] = '<option>All</option>';
    $_REQUEST['priorityDropDown'] = '';
    $_REQUEST['workerDropdown'] = '';

    $country = '';
    $location = '';
    $condition = '';


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
    }



    if (!empty($_POST) && $updated === false){
    $count = 0;
      foreach ($_POST as $col => $val){
        if (!is_array($col)){
          if ($count != 0 && !empty($val)){
            // update asset
            $result = updateTicket($_REQUEST['ticket_no'],$col,$val);
          }
        }
        $count++;
      }
    }


    $ticket = getTicketByID($_REQUEST['ticket_no']);

    if ($ticket->num_rows > 0) {

      $count = 0;
      while($row = $ticket->fetch_assoc()) {

        $_REQUEST['id'] = $row['id'];
        $_REQUEST['ticket_no'] = $row['ticket_no'];
        $_REQUEST['title'] = $row['title'];
        $_REQUEST['description'] = $row['description'];
        $_REQUEST['country'] = $row['country'];
        $_REQUEST['campus'] = $row['campus'];
        $_REQUEST['location'] = $row['location'];
        $_REQUEST['sub_location'] = $row['sub_location'];
        $_REQUEST['project'] = $row['project'];
        $_REQUEST['submitter'] = $row['submitter'];
        $_REQUEST['priority'] = $row['priority'];
        $_REQUEST['status'] = $row['status'];
        $_REQUEST['ticket_type'] = $row['ticket_type'];
        $_REQUEST['created'] = $row['created'];
        $_REQUEST['updated'] = $row['updated'];

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

      }
    } else {
      echo "0 results";
      // exit();
    }

    $priority = array("High", "Medium", "Low");
    $_REQUEST['priorityDropDown'] .= '<option>'.$_REQUEST['priority'].'</option>';
    foreach($priority as $value){
      if ($_REQUEST['priority'] !== $value){
        $_REQUEST['priorityDropDown'] .= '<option>'.$value.'</option>';
      }
    }

    $submitter = getUserById($_REQUEST['submitter']);
    if ($submitter->num_rows > 0) {
      while($row = $submitter->fetch_assoc()) {
        $_REQUEST['submitter'] = $row['first_name'] . " " . $row['last_name'];
      }
    }

    $_REQUEST['workerDropdown'] .= "<option selected disabled>".$_REQUEST['assigned_worker']."</option>";
    $workers = getUsers('all');
    if ($workers->num_rows > 0) {
      while($row = $workers->fetch_assoc()) {
        if ($_REQUEST['assigned_worker'] !== $row['first_name'] . " " . $row['last_name']){
          $_REQUEST['workerDropdown'] .= "<option value=".$row['id'].">".$row['first_name']." ".$row['last_name']."</option>";
        }
      }
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
          <!-- <h1 class="h3 mb-2 text-gray-800">Detailed View</h1> -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <div class="form-row">
                <div class="form-group col-md-1"></div>
                <div class="form-group col-md-4">
                  <h2 class="m-0 font-weight-bold text-primary"><?php echo $_REQUEST['title'];?></h6>
                </div>
                <div class="form-group col-md-4"></div>
                <div class="form-group col-md-2">
                  <h6 class="m-0 font-weight-bold text-primary"><u>Ticket No. <?php echo $_REQUEST['ticket_no'];?></u></h6>
                </div>
              </div>
            </div>


            <div class="card-body">
              <!-- The styling for this basic card example is created by using default Bootstrap utility classes. By using utility classes, the style of the card component can be easily modified with no need for any custom CSS! -->
              <!-- Asset -->

              <form action="ticket-detail" method="POST">
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-10">
                    <div class="form-group">
                      <label for="description">Description</label>
                      <textarea class="form-control" id="description" rows="3"><?php echo $_REQUEST['description'];?></textarea>
                    </div>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <input type="hidden" id="ticket_no" name="ticket_no" value="<?php echo $_REQUEST['ticket_no'];?>">
                    <label for="country">Country</label>
                    <input type="text" class="form-control" id="country" name="country" placeholder="<?php echo $_REQUEST['country'];?>" disabled>
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="campus">Campus</label>
                    <input type="text" class="form-control" id="campus" name="campus" placeholder="<?php echo $_REQUEST['campus'];?>" disabled>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label for="location">Location</label>
                    <input type="text" class="form-control" id="location" name="location" placeholder="<?php echo $_REQUEST['location'];?>" disabled>
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="sub_location">Sub-Location</label>
                    <input type="text" class="form-control" id="sub_location" name="sub_location" placeholder="<?php echo $_REQUEST['sub_location'];?>" disabled>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label for="subitter">Submitter</label>
                    <input type="text" class="form-control" id="submitter" name="submitter" placeholder="<?php echo $_REQUEST['submitter'];?>" disabled>
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="assigned_worker">Assigned Worker</label>
                    <select class="form-control" name="assigned_worker" id="assigned_worker" <?php echo $_REQUEST['buttonDisabled'];?>>
                      <?php echo $_REQUEST['workerDropdown'];?>
                    </select>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label for="project">Project</label>
                    <input type="text" class="form-control" id="project" name="project" placeholder="<?php echo $_REQUEST['project'];?>" <?php echo $_REQUEST['buttonDisabled'];?>>
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="status">Status</label>
                    <input type="text" class="form-control" id="status" name="status" placeholder="<?php echo $_REQUEST['status'];?>" <?php echo $_REQUEST['buttonDisabled'];?>>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label for="ticket_type">Ticket Type</label>
                    <input type="text" class="form-control" id="ticket_type" name="ticket_type" placeholder="<?php echo $_REQUEST['ticket_type'];?>" <?php echo $_REQUEST['buttonDisabled'];?>>
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="priority">Priority</label>
                    <select class="form-control" name="priority" id="priority" <?php echo $_REQUEST['buttonDisabled'];?>>
                      <?php echo $_REQUEST['priorityDropDown'];?>
                    </select>
                  </div>
                </div>
                

                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label for="date_submitted">Date Submitted</label>
                    <input type="month" class="form-control" id="created" name="created" value="<?php echo $_REQUEST['created'];?>" disabled>
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="last_updated">Last Updated</label>
                    <input type="month" class="form-control" id="updated" name="updated" value="<?php echo $_REQUEST['updated'];?>" disabled>
                  </div>
                </div>




                <hr>
                
                <button type="submit" class="btn btn-primary">Update</button>
              </form>
              </div>
            </div>


            <!-- Content Column -->
            <div class="row">
              
              <div class="col-lg-6 mb-4">
                <!-- Ticket History Section -->
                <div class="card shadow mb-4">
                  <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ticket History</h6>
                  </div>
                  <div class="card-body"><p>{{ Ticket History goes here }}</p></div>
                </div>
              </div>

              <div class="col-lg-6 mb-4">
              <!-- Comment Section -->
                <div class="card shadow mb-4">
                  <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Comments</h6>
                  </div>
                  <div class="card-body"><p>{{ Ticket Comments go here }}</p></div>
                </div>
              </div>
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

</body>

</html>
