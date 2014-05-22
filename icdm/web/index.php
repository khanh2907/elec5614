<?php
require_once('include/common.php');
require_once('include/sql.php');
startValidSession();
?>

<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ICDM</title>

    <!-- Core CSS - Include with every page -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">

    <!-- SB Admin CSS - Include with every page -->
    <link href="css/sb-admin.css" rel="stylesheet">

</head>

<body>

    <div id="wrapper">

        <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">ICDM v1.0</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="login"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default navbar-static-side" role="navigation">
                <div class="sidebar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="."><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
                        <li class="active">
                            <a href="#"><i class="fa fa-users fa-fw"></i> Patients<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <?php
                                try {
                                    $doctor_id = $_SESSION['doctor_id'];
                                	$patients = getPatientsOf($doctor_id);
                                	foreach($patients as $patient) {
                                		echo '<li>';
                                		echo '<a href=patient.php?id=', $patient[id],'>';
                                		echo $patient[name], ' ', $patient[surname];
                                		echo '</a>';
                                		echo '</li>';
                                	}
                                }
                                catch (Exception $e) {
                                	echo 'Something went wrong here.';
                                }

                                ?>

                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                    </ul>
                    <!-- /#side-menu -->
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header">Dashboard</h3>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-rss fa-fw"></i> Lastest Activity
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table class="table">
                                <tr>
                                    <th>Patient</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Activity</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                </tr>
                                <?php 
                                $doctor_id = $_SESSION['doctor_id'];
                                $jobs = getDashboardJobs($doctor_id);
                                
                                forEach($jobs as $job) {
                                    echo "<tr>";
                                    echo "<td>";
                                    echo '<a href="';
                                    echo 'patient.php?id=', $job['patient_id'];
                                    echo '">'; 
                                        echo $job['name'], ' ', $job['surname']; 
                                    echo "</a>";
                                    echo "</td>";
                                    echo "<td>" , $job['start_time'] , "</td>";
                                    echo "<td>" , $job['end_time'] , "</td>";
                                    echo "<td>" , $job['type'] , "</td>";
                                    echo "<td>" , $job['status'] , "</td>";
                                    echo "<td>" , $job['description'] , "</td>";
                                    echo "</tr>";
                                }
                                ?>

                            </table>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    
                </div>
                <!-- /.col-lg-8 -->
                <div class="col-lg-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-heart fa-fw"></i> Average Heartrate (24 hours)
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table class="table">
                            	<!-- insert status table here -->
                            </table>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-4 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- Core Scripts - Include with every page -->
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>

    <!-- SB Admin Scripts - Include with every page -->
    <script src="js/sb-admin.js"></script>

</body>

</html>
