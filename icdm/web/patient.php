<?php
require_once('include/common.php');
require_once('include/sql.php');
startValidSession();
$patient_id = $_GET['id'];
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

    <!-- Core Scripts - Include with every page -->
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/highcharts.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>

    <!-- SB Admin Scripts - Include with every page -->
    <script src="js/sb-admin.js"></script>

    <script type="text/javascript">
    $(function () {

        // Current HR START

        var currentHRUrl = <?php echo "'get/heartrate.php?type=current&patient_id=",$patient_id, "'"; ?>

        setInterval(function(){
            $.getJSON(currentHRUrl, function(data) {
                var newHR = data[0].heartrate;
                $('#current-hr').html(newHR + " bpm");
            })
        }, 1000);

        // Current HR END

        // Graph JS START
        var chartUrl = <?php echo "'get/heartrate.php?type=graph&patient_id=",$patient_id, "'"; ?>

        Highcharts.setOptions({
            global: {
                useUTC: false    
            }
            
        })

        var options = {
            title: {
                text: 'Heart Rate (24 hours)'
            },
            chart: {
                renderTo: 'heartrate-graph',
                type: 'line'
            },
            xAxis: {
                type: 'datetime',
                title: {
                    text: 'Time'
                }
            },
            plotOptions: {
                line: {
                    animation: false
                }
            },
            yAxis: {
                title: {
                    text: 'BPM'
                }
            },
            legend: {
                enabled: false
            },
            credits: {
                enabled: false
            },
            series: [{}]
        };

        $.getJSON(chartUrl, function(data) {

            var dataList = [];

            data.forEach(function(entry) {
                heartrateInstance = [new Date(entry.time).getTime(), parseFloat(entry.heartrate)];
                dataList.push(heartrateInstance);
            })        

        

            options.series[0].data = dataList;
            var chart = new Highcharts.Chart(options);
        });
        
        setInterval(function(){
            var dataList = [];
            $.getJSON(chartUrl, function(data) {

                if (new Date(data[data.length-1].time).getTime() != options.series[0].data[options.series[0].data.length-1][0]) {
                    data.forEach(function(entry) {
                        heartrateInstance = [new Date(entry.time).getTime(), parseFloat(entry.heartrate)];
                        dataList.push(heartrateInstance);
                    })   

                    options.series[0].data = dataList;
                    var chart = new Highcharts.Chart(options);
                }
            })
        }, 500);
        // Graph JS END

    });
        </script>

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
                                	$doctor_id = $_SESSION['doctor_id'][0]['id'];
                                	$patients = getPatientsOf($doctor_id);
                                	foreach($patients as $patient) {
                                        echo '<li>';
                                        echo '<a href=patient.php?id=', $patient[id],'>';
                                        if ($patient[id] == $patient_id) {
                                            echo '<strong>';
                                            echo $patient[name], ' ', $patient[surname];
                                            echo '</strong>';
                                        }
                                        else {
                                            echo $patient[name], ' ', $patient[surname];    
                                        }
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
                    <h3 class="page-header">
                        <?php
                        $patientDetails = getPatientDetails($patient_id);
                        echo $patientDetails[0]['name'], ' ', $patientDetails[0]['surname'];
                        ?>
                    </h3>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-rss fa-fw"></i> Activity Log
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table class="table">
                                <tr>
                                    <th>Time</th>
                                    <th>Activity</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                </tr>
                                <tr>
                                    <td>some date</td>
                                    <td>Emergency</td>
                                    <td>In Progress</td>
                                    <td>Failed to defribilate.</td>
                                </tr>
                                <tr>
                                    <td>some date</td>
                                    <td>Defribillation</td>
                                    <td>Failed</td>
                                    <td>Heart rate was too low. Failed to adjust heartrate.</td>
                                </tr>
                                <tr>
                                    <td>some date</td>
                                    <td>Defribillation</td>
                                    <td>Complete</td>
                                    <td>Heart rate was too high.</td>
                                </tr>
                            </table>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    
                </div>
                <!-- /.col-lg-8 -->
                <div class="col-lg-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-heart fa-fw"></i> Current Heart Rate
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <span>
                                <h1 id="current-hr">
                                    <?php
                                    $currentHR = getCurrentHeartRateOf($patient_id);
                                    echo $currentHR[0]['heartrate'];
                                    ?>
                                 bpm</h1> 
                            </span>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-4 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Heart Rate Graph
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div id="heartrate-graph" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    
                </div>
                <!-- /.col-lg-8 -->
                <div class="col-lg-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-book fa-fw"></i> Statistics
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table class="table">
                                <tr>
                                    <td>Average Heartrate (5 mins) </td>
                                    <td> 66.6 </td>
                                </tr>
                                <tr>
                                    <td>Average Heartrate (15 mins) </td>
                                    <td> 66.6 </td>
                                </tr>
                                <tr>
                                    <td>Average Heartrate (30 mins) </td>
                                    <td> 66.6 </td>
                                </tr>
                                <tr>
                                    <td>Average Heartrate (1 hour) </td>
                                    <td> 66.6 </td>
                                </tr>
                                <tr>
                                    <td>Average Heartrate (1.5 hours) </td>
                                    <td> 66.6 </td>
                                </tr>
                                <tr>
                                    <td>Average Heartrate (24 hours) </td>
                                    <td> 66.6 </td>
                                </tr>
                                <tr>
                                    <td>Number of Defibrillations</td>
                                    <td> 4 </td>
                                </tr>
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

    

</body>

</html>
