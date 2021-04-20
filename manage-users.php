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
    $_REQUEST['tableRows'] = '';
    $_REQUEST['userOptions'] = '';
    $_REQUEST['roleDropDown'] = '';
    $_REQUEST['buttonDisabled'] = 'disabled';
    if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'manager'){
      $_REQUEST['buttonDisabled'] = '';  
    }
    // exit();
    

    $_REQUEST['select-user-error'] = '';
      // echo $startDate;
      // exit();

    // var_dump($_POST);
    // exit();

    if (!empty($_POST['role'])){
      if (!empty($_POST['user'])){
        $pieces = explode(" ", $_POST['user']);
        $firstName = $pieces[0];
        $lastName = $pieces[1];
        updateUser($firstName, $lastName, $_POST['role']);
      } else {
        $_REQUEST['select-user-error'] = '<label for="exampleFormControlSelect2" style="color: red">You must select a user</label>';
      }
    }



    $users = getUsers('all');
    if ($users->num_rows > 0) {

      $count = 0;
      $roles = array();



      // output data of each row
      while($row = $users->fetch_assoc()) {


        $_REQUEST['roleDropDown'] = '<option>admin</option>'.
                                    '<option>exec</option>'.
                                    '<option>manager</option>'.
                                    '<option>maintenance</option>';





        // echo "id: " . $row["id"]. " - Name: " . $row["item_name"]. " location: " . $row["location"]. "<br>";
        $_REQUEST['tableRows'] .= '
              <tr>'.'
                <th>'. $row["first_name"] . '</th>'.'
                <th>'. $row["last_name"] . '</th>'.'
                <th>'. $row["email"] . '</th>'.'
                <th>'. $row["role"] . '</th>'.'
                <th>'. $row["organization"] . '</th>'.'
                <th>'. $row["country"] . '</th>'.'
                <th><a href="manage-users-detail?userId='.$row["id"].'">More Info</a></th>';

        $_REQUEST['userOptions'] .='<option>'.$row["first_name"].' '.$row["last_name"].'</option>';
      }
    } else {
      echo "0 results";
      exit();
    }

  }


  // <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
  //   Dropdown
  // </a>
  // <div class="dropdown-menu dropdown-menu-right animated--grow-in" aria-labelledby="navbarDropdown">
  //   <a class="dropdown-item" href="#">Action</a>
  //   <a class="dropdown-item" href="#">Another action</a>
  //   <div class="dropdown-divider"></div>
  //   <a class="dropdown-item" href="#">Something else here</a>
  // </div>


    // echo "<textarea rows=80 cols=100>";
    // print_r($_REQUEST['tableRows']);
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

  <title>Lespwa AMS - Users</title>

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
              <h6 class="m-0 font-weight-bold text-primary">Manage Team & Roles</h6>
              <hr>
              <form action="manage-users" method="POST">
                <div class="form-row">
                  <div class="col-md-1 mb-1">&nbsp;</div>
                  <div class="col-md-4 mb-3">
                    <label for="exampleFormControlSelect2">Select a User</label><br>
                    <select multiple class="form-control" name="user" id="user" <?php echo $_REQUEST['buttonDisabled'];?>>
                      <?php echo $_REQUEST['userOptions']; ?>
                    </select>
                    <?php echo $_REQUEST['select-user-error']; ?>
                  </div>
                  <div class="col-md-1 mb-1">&nbsp;</div>
                  <div class="col-md-4 mb-3">
                    <label for="validationCustom02">Select Role to Assign</label><br>
                    <select class="form-control" name="role" id="role" <?php echo $_REQUEST['buttonDisabled'];?>>
                      <?php echo $_REQUEST['roleDropDown'];?>
                    </select>
                    <br>
                    <div>
                      <button class="btn btn-primary" type="submit" <?php echo $_REQUEST['buttonDisabled'];?>>Submit form</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>First Name</th>
                      <th>Last Name</th>
                      <th>Email</th>
                      <th>Role</th>
                      <th>Organization</th>
                      <th>Country</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody style="font-size: .85rem;">
                      <?php echo $_REQUEST['tableRows'];?>
                  </tbody>
                </table>
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

  <script src="js/auth.js"></script>

</body>

</html>
