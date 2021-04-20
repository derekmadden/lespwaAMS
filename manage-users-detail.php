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


  $_REQUEST['buttonDisabled'] = 'disabled';
  if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'manager'){
    $_REQUEST['buttonDisabled'] = '';  
  }

  // var_dump($_SESSION);
  // exit();

  

  if (!empty($_POST)){

    if (empty($_POST['userId'])){
      exit('error here');
    } else {
      $_REQUEST['userId'] = $_POST['userId'];
    }




    $count = 0;
    foreach ($_POST as $col => $val){
      if ($_SESSION['userId'] == $_POST['userId']){
        $_SESSION[$col] = $val;
      }

      if (!is_array($col)){
        if ($count != 0 && !empty($val)){
          $result = updateUserById($_REQUEST['userId'],$col,$val);
        }
      }
      $count++;
    }
  }
  

  buildTable();





  function buildTable(){


    $_REQUEST['tableRows'] = '';
    if ($_SESSION['country'] !== 'All'){
      $_REQUEST['countryDropDown'] = '<option>'.$_SESSION['country'].'</option>'.'
                                      <option>All</option>';
    } else {
      $_REQUEST['countryDropDown'] = '<option>'.$_SESSION['country'].'</option>';
    }
    $_REQUEST['roleDropDown'] = '<option>'.$_SESSION['role'].'</option>';


    $countries = getCountries();
    if ($countries->num_rows > 0) {
      while($row = $countries->fetch_assoc()) {
        if(strlen(trim($row["country"])) > 0 && $row['country'] !== $_SESSION['country']){
          $_REQUEST['countryDropDown'] .= '<option>'.$row["country"].'</option>';
        }
      }
    }

    $roles = array("admin", "exec", "manager", "maintenance");
    foreach ($roles as $role => $value) {
      if ($value !== $_SESSION['role']){
        $_REQUEST['roleDropDown'] .= '<option>'.$value.'</option>';
      }
    }



    $user = getUserById($_REQUEST['userId']);
    if ($user->num_rows > 0) {

      // output data of each row
      $count = 0;

      while($row = $user->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["item_name"]. " location: " . $row["location"]. "<br>";
        // var_dump($row);
        // exit();

        

        $_REQUEST['userId'] = $row['id'];
        $_REQUEST['name'] = $row['first_name'] . " " . $row['last_name'];
        $_REQUEST['email'] = $row['email'];
        $_REQUEST['password'] = $row['password'];
        $_REQUEST['country'] = $row['country'];
        $_REQUEST['role'] = $row['role'];
        $_REQUEST['organization'] = $row['organization'];

      }
    } else {
      echo "0 results";
      // exit();
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

  <title>Lespwa AMS - More Info</title>

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
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary"><?php echo $_REQUEST['name'];?></h6>
            </div>
            <div class="card-body">
              <!-- The styling for this basic card example is created by using default Bootstrap utility classes. By using utility classes, the style of the card component can be easily modified with no need for any custom CSS! -->
              <!-- Asset -->
              <form action="manage-users-detail<?php echo $_REQUEST['appendPhpExtension'];?>" method="POST">
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label for="email">Email</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="<?php echo $_REQUEST['email'];?>" disabled>
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="password">Password</label>
                    <input type="text" class="form-control" id="password" name="password" placeholder="**********" disabled>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <input type="hidden" id="userId" name="userId" value="<?php echo $_REQUEST['userId'];?>">
                    <label for="country">Country</label>
                    <select class="form-control" id="country" name="country" <?php echo $_REQUEST['buttonDisabled'];?>>
                      <?php echo $_REQUEST['countryDropDown'];?>
                    </select>
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="role">Role</label>
                    <select class="form-control" name="role" id="role" <?php echo $_REQUEST['buttonDisabled'];?>>
                      <?php echo $_REQUEST['roleDropDown'];?>
                    </select>
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

              <!-- Project Card Example -->
              <!-- <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Ticket History</h6>
                </div>
                <div class="card-body">
                  {{ put the history in here }}
                </div>
              </div> -->
            </div>

            <div class="col-lg-6 mb-4">

              <!-- Illustrations -->
              <!-- <div class="card shadow mb-4"> -->
                <!-- <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Comments</h6>
                </div>
                <div class="card-body">
                  <p>The Ticket Comments will go here</p>
                </div> -->
              <!-- </div> -->
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
