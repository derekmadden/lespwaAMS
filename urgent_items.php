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
      // echo $startDate;
      // exit();

    $assets = getData('','','','urgentNeeds');
    if ($assets->num_rows > 0) {

      // output data of each row
      while($row = $assets->fetch_assoc()) {
        // echo "id: " . $row["id"]. " - Name: " . $row["item_name"]. " location: " . $row["location"]. "<br>";
        $_REQUEST['tableRows'] .= '
              <tr>'.'
                <th>'. $row["item_name"] . '</th>'.'
                <th>'. $row["location"] . '</th>'.'
                <th>'. $row["campus"] . '</th>'.'
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
      }
    } else {
      echo "0 results";
      exit();
    }

  }


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

  <title>Lespwa AMS - Urgent Items</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
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
            <!-- <h1 class="h3 mb-2 text-gray-800">Charts</h1>
            <p class="mb-4">Chart.js is a third party plugin that is used to generate the charts in this theme. The charts below have been customized - for further customization options, please visit the <a target="_blank" href="https://www.chartjs.org/docs/latest/">official Chart.js documentation</a>.</p> -->
          <p id="demo"></p>
          <!-- Content Row -->
          <div class="row">
            <div class="col-xl-8 col-lg-7">

              
              <!-- Bar Chart -->
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Bar Chart</h6>
                </div>
                <div class="card-body">
                  <div class="chart-bar">
                    <canvas id="myBarChart"></canvas>
                  </div>
                  <hr>
                  <!-- Styling for the bar chart can be found in the <code>/js/demo/chart-bar-demo.js</code> file. -->
                </div>
              </div>

              <!-- Area Chart -->
              <!-- <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Area Chart</h6>
                </div>
                <div class="card-body">
                  <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                  </div>
                  <hr>
                  Styling for the area chart can be found in the <code>/js/demo/chart-area-demo.js</code> file.
                </div>
              </div> -->
            </div>

            <!-- Donut Chart -->
            <div class="col-xl-4 col-lg-5">
              <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3">
                  <h6 class="m-2 font-weight-bold text-primary">Urgent Items</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                  <div class="chart-pie pt-4">
                    <canvas id="myPieChart"></canvas>
                  </div>
                  <hr>
                  <div class="mt-4 text-center small">
                    <span class="mr-2">
                      <i class="fas fa-circle text-success"></i> Needs Replaced
                    </span>
                    <span class="mr-2">
                      <i class="fas fa-circle text-primary"></i> Needs Repaired
                    </span>
                    <!-- <span class="mr-2">
                      <i class="fas fa-circle text-info"></i> Referral
                    </span> -->
                  </div>
                  <!-- Styling for the donut chart can be found in the <code>/js/demo/chart-pie-demo.js</code> file. -->
                </div>
              </div>
            </div>


            
          </div>
        </div>
      </div>




        <div class="container-fluid">

          <!-- Page Heading -->
          <!-- <h1 class="h3 mb-2 text-gray-800">Tables</h1>
          <p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below. For more information about DataTables, please visit the <a target="_blank" href="https://datatables.net">official DataTables documentation</a>.</p> -->

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Assets: Urgent Needs</h6><br>
              <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Asset Name</th>
                      <th>Location</th>
                      <th>Campus</th>
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
          </div>
        </div>
        <!-- /.container-fluid -->

      <!-- </div> -->
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
  <script src="vendor/chart.js/Chart.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="js/demo/chart-area-demo.js"></script>
  <script src="js/demo/chart-pie-repair-replace.js"></script>
  <script src="js/demo/chart-bar-repair-replace.js"></script>


    <!-- Page level plugins -->
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="js/demo/datatables-demo.js"></script>


</body>

</html>
