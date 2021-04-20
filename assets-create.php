<?php


  session_start();

  if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true){
    header("location: login.html");
    die();
  }


  require_once('getHTML.php');
  require_once('db_connect.php');
  require_once('SimpleXLSXGen.php');

  if ($_SESSION['country'] == 'All'){
    $_REQUEST['countryTextbox'] = '<input type="text" class="form-control" id="country" name="country" placeholder="Country" required>';
  } else {
    $_REQUEST['countryTextbox'] = '<input type="text" class="form-control" id="country" name="country" placeholder="'.$_SESSION['country'].'" disabled>';
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
          $result = createAsset($col,$val);
          echo $result;
        }
        if (!empty($val)){
          // update asset
          $_REQUEST['id'] = getLastId();
          $result = updateAsset($_REQUEST['id'],$col,$val);
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

// exit();


?>


<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Lespwa AMS - Create Asset</title>

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
        <form action="detail" method="POST">
          <input type="hidden" id="createAsset" name="createAsset" value="1">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary"><input type="text" class="form-control" id="item_name" name="item_name" placeholder="Asset Name" required></h6>
            </div>
            <div class="card-body">
              <!-- The styling for this basic card example is created by using default Bootstrap utility classes. By using utility classes, the style of the card component can be easily modified with no need for any custom CSS! -->
              <!-- Asset -->

              
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label for="country">Country</label>
                    <?php echo $_REQUEST['countryTextbox']; ?>
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="campus">Campus</label>
                    <input type="text" class="form-control" id="campus" name="campus" placeholder="Campus" required>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label for="location">Location</label>
                    <input type="text" class="form-control" id="location" name="location" placeholder="Location" required>
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="sub_location">Sub-Location</label>
                    <input type="text" class="form-control" id="sub_location" name="sub_location" placeholder="Sub-Location" required>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label for="sub_location">Asset Type</label>
                    <select class="custom-select" id="asset_type" name=asset_type required>
                      <option>Equipment</option>
                      <option>Fleet</option>
                      <option>Structure</option>
                      <option>Infrastructure</option>
                    </select>
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="sub_location">Category</label>
                    <select class="custom-select" id="category" name=category required>
                      <option>Appliance</option>
                      <option>Boat Motor</option>
                      <option>Building</option>
                      <option>Construction</option>
                      <option>Electrical</option>
                      <option>Heavy Equipment</option>
                      <option>Roofing</option>
                      <option>Vehicle</option>
                      <option>Vessel</option>
                    </select>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="major_asset" name="major_asset">
                    <label class="form-check-label" for="gridCheck">Major Asset</label>
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label for="purchase_date">Purchase Date</label>
                    <input class="form-control" id="purchase_date" name="purchase_date" type="date" value="<?php echo date("Y-m-d");?>">
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="purchase_cost">Purchase Cost</label>
                    <input type="text" class="form-control" id="purchase_cost" name="purchase_cost">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label for="current_condition">Condition</label>
                    <select class="custom-select" id="current_condition" name=current_condition placeholder="Current Condition" required>
                      <option value="1">New</option>
                      <option value="2">Good</option>
                      <option value="3">Fair</option>
                      <option value="4">Needs Repaired</option>
                      <option value="5">Needs Replaced</option>
                    </select>
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="replacement_cost">Replacement Cost</label>
                    <input type="text" class="form-control" id="replacement_cost" name="replacement_cost" Cost">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label for="useful_life">Useful Life (Years)</label>
                    <input type="text" class="form-control" id="useful_life" name="useful_life" placeholder="Useful Life" required>
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="known_issues">Known Issues</label>
                    <input type="text" class="form-control" id="known_issues" name="known_issues">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="deferred_replacement" name="deferred_replacement">
                    <label class="form-check-label" for="gridCheck">Deferred Replacement</label>
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-10">
                    <label for="other_comments">Other Comments</label>
                    <textarea type="text" class="form-control" id="other_comments" name="other_comments" placeholder="Other Comments"></textarea>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">Add New Asset</button>
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
