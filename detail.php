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



  //check if we are arriving here after creating an asset
  if (!empty($_POST['createAsset'])){
    $count = 0;

    foreach ($_POST as $col => $val){
      if (!is_array($col)){
        if ($count > 0){
          if ($count == 1){
            $result = createAsset($col,$val);
          } else if (!empty($val)){
            // update asset

            if ($col == 'purchase_date') {
              $formattedDate = date('M-Y', strtotime($val));
              $result = updateAsset($_REQUEST['id'],$col,$formattedDate);
            } else {
              $_REQUEST['id'] = getLastId();
              $result = updateAsset($_REQUEST['id'],$col,$val);
            }
          }
        }
      }
      $count++;
    }
  }






  if (!empty($_POST['asset_id'])){
    $_REQUEST['id'] = $_POST['asset_id'];
  } elseif (empty($_REQUEST['id'])){
    exit('error here');
  }

  
  

  buildTable();




  function buildTable(){

    $_REQUEST['tableRows'] = '';
    $_REQUEST['countryDropDown'] = '<option>All</option>';
    $_REQUEST['locationDropDown'] = '<option>All</option>';
    $_REQUEST['conditionDropDown'] = '<option>All</option>';

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

      if (!empty($_POST['condition'])){
        if ($_POST['condition'] != 'All') {
          $condition = $_POST['condition'];
        }
      }
    }


    if (!empty($_POST)){
    $count = 0;
      foreach ($_POST as $col => $val){
        if (!is_array($col)){
          if ($count != 0 && !empty($val)){
            // update asset
            $result = updateAsset($_REQUEST['id'],$col,$val);
          }
        }
        $count++;
      }


      if (empty($_POST['major_asset'])){
        $result = updateAsset($_REQUEST['id'],'major_asset','off');
      }
      if (empty($_POST['deferred_replacement'])){
        $result = updateAsset($_REQUEST['id'],'deferred_replacement','off');
      }
    }


    $asset = getDataByID($_REQUEST['id']);
    if ($asset->num_rows > 0) {

      // output data of each row
      $count = 0;

      while($row = $asset->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["item_name"]. " location: " . $row["location"]. "<br>";
        // var_dump($row);
        // exit();

        $_REQUEST['id'] = $row['id'];
        $_REQUEST['item_name'] = $row['item_name'];
        $_REQUEST['country'] = $row['country'];
        $_REQUEST['campus'] = $row['campus'];
        $_REQUEST['location'] = $row['location'];
        $_REQUEST['sub_location'] = $row['sub_location'];
        $_REQUEST['major_asset'] = $row['major_asset'];
        $_REQUEST['asset_type'] = $row['asset_type'];
        $_REQUEST['category'] = $row['category'];
        $_REQUEST['purchase_date'] = $row['purchase_date'];
        $_REQUEST['purchase_cost'] = $row['purchase_cost'];
        $_REQUEST['replacement_cost'] = $row['replacement_cost'];
        $_REQUEST['useful_life'] = $row['useful_life'];
        $_REQUEST['current_condition'] = $row['current_condition'];
        $_REQUEST['deferred_replacement'] = $row['deferred_replacement'];
        $_REQUEST['known_issues'] = $row['known_issues'];
        $_REQUEST['other_comments'] = $row['other_comments'];

        if ($_REQUEST['major_asset'] == 'on'){
          $_REQUEST['major_asset_checked'] = 'checked';
        } else {
          $_REQUEST['major_asset_checked'] = '';
        }

        if ($_REQUEST['deferred_replacement'] == 'on'){
          $_REQUEST['deferred_replacement_checked'] = 'checked';
        } else {
          $_REQUEST['deferred_replacement_checked'] = '';
        }



        $newDate = date("Y-m-d", strtotime($_REQUEST['purchase_date']));
        $_REQUEST['purchase_date'] = $newDate;


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
          <!-- <p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below. For more information about DataTables, please visit the <a target="_blank" href="https://datatables.net">official DataTables documentation</a>.</p> -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary"><?php echo $_REQUEST['item_name'];?></h6>
            </div>
            <div class="card-body">
              <!-- The styling for this basic card example is created by using default Bootstrap utility classes. By using utility classes, the style of the card component can be easily modified with no need for any custom CSS! -->
              <!-- Asset -->

              <form action="detail<?php echo $_REQUEST['appendPhpExtension'];?>" method="POST">
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <input type="hidden" id="asset_id" name="asset_id" value="<?php echo $_REQUEST['id'];?>">
                    <label for="country">Country</label>
                    <input type="text" class="form-control" id="country" name="country" placeholder="<?php echo $_REQUEST['country'];?>">
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="campus">Campus</label>
                    <input type="text" class="form-control" id="campus" name="campus" placeholder="<?php echo $_REQUEST['campus'];?>">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label for="location">Location</label>
                    <input type="text" class="form-control" id="location" name="location" placeholder="<?php echo $_REQUEST['location'];?>">
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="sub_location">Sub-Location</label>
                    <input type="text" class="form-control" id="sub_location" name="sub_location" placeholder="<?php echo $_REQUEST['sub_location'];?>">
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label for="sub_location">Asset Type</label>
                    <select class="custom-select" id="asset_type" name=asset_type>
                      <option selected><?php echo $_REQUEST['asset_type'];?></option>
                      <option value="1">Equipment</option>
                      <option value="2">Fleet</option>
                      <option value="3">Structure</option>
                      <option value="4">Infrastructure</option>
                    </select>
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="sub_location">Category</label>
                    <select class="custom-select" id="category" name=category>
                      <option selected><?php echo $_REQUEST['category'];?></option>
                      <option value="1">Appliance</option>
                      <option value="2">Boat Motor</option>
                      <option value="3">Building</option>
                      <option value="4">Construction</option>
                      <option value="5">Electrical</option>
                      <option value="6">Heavy Equipment</option>
                      <option value="7">Roofing</option>
                      <option value="8">Vehicle</option>
                      <option value="9">Vessel</option>
                    </select>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="major_asset" name="major_asset" <?php echo $_REQUEST["major_asset_checked"];?>>
                    <label class="form-check-label" for="gridCheck">Major Asset</label>
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label>Purchase Date</label>
                    <input type="text" class="form-control"placeholder="<?php echo $_REQUEST['purchase_date'];?>" disabled>                    
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="purchase_date">Update Purchase Date</label>
                    <input class="form-control" id="purchase_date" name="purchase_date" type="date" value="<?php echo date("Y-m-d");?>">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label for="purchase_cost">Purchase Cost</label>
                    <input type="text" class="form-control" id="purchase_cost" name="purchase_cost" placeholder="$ <?php echo $_REQUEST['purchase_cost'];?>">
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="replacement_cost">Replacement Cost</label>
                    <input type="text" class="form-control" id="replacement_cost" name="replacement_cost" placeholder="$ <?php echo $_REQUEST['replacement_cost'];?>">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-4">
                    <label for="useful_life">Useful Life (Years)</label>
                    <input type="text" class="form-control" id="useful_life" name="useful_life" placeholder="<?php echo $_REQUEST['useful_life'];?>">
                  </div>
                  <div class="form-group col-md-2"></div>
                  <div class="form-group col-md-4">
                    <label for="current_condition">Condition</label>
                    <input type="text" class="form-control" id="current_condition" name="current_condition" placeholder="<?php echo $_REQUEST['current_condition'];?>">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="deferred_replacement" name="deferred_replacement" <?php echo $_REQUEST["deferred_replacement_checked"];?>>
                    <label class="form-check-label" for="gridCheck">Deferred Replacement</label>
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-10">
                    <label for="known_issues">Known Issues</label>
                    <input type="text" class="form-control" id="known_issues" name="known_issues" placeholder="<?php echo $_REQUEST['known_issues'];?>">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-1"></div>
                  <div class="form-group col-md-10">
                    <label for="other_comments">Other Comments</label>
                    <textarea type="text" class="form-control" id="other_comments" name="other_comments" placeholder="<?php echo $_REQUEST['other_comments'];?>"></textarea>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
              </form>
              
            </div>
          </div>


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

</body>

</html>
