<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once('../include/sql.php');

function log_out() {
    $_SESSION['user'] = '';
    $_SESSION['logged_in'] = false;
}

function log_in($name, $pass) {
    $is_valid = checkLogin($name,$pass);
    if ($is_valid) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user'] = $name;
        $_SESSION['doctor_id'] = getDoctorId($name);
    }
    return $is_valid;
}

// Start session from scratch

session_start();
log_out();

// Messages to display to user if returning to page
$message = '';

// Query string parameters to preserve across login process
$qstring = http_build_query($_GET);
if (!empty($qstring)) {
    $qstring = '?'.$qstring;
}

//
// Process login details (must be POST data) and redirect to main site if correct
//
if(!isset($_POST['user']) || !isset($_POST['pass'])) {
    // Invalid data supplied, so don't return any message (maybe log the event though)
} else if (log_in($_POST['user'], $_POST['pass'])) {
    // Success so redirect to desired page
    $target = '../'.$qstring; // Pass on query parameters
    header('Location:'.$target);
    exit;
} else {
    $message='Login details incorrect. Please try again.';
}
?>

<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Start Bootstrap - SB Admin Version 2.0 Demo</title>

    <!-- Core CSS - Include with every page -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../font-awesome/css/font-awesome.css" rel="stylesheet">

    <!-- SB Admin CSS - Include with every page -->
    <link href="../css/sb-admin.css" rel="stylesheet">

</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Sign In</h3>
                    </div>
                    <div class="panel-body">
                        <form action="<?php echo '../login/index.php',$qstring; ?>" id="loginform" method="post">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Username" name="user" type="text" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="pass" type="password" value="">
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="Remember Me">Remember Me
                                    </label>
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <button type="submit" class="btn btn-lg btn-success btn-block">Login</button>
                            </fieldset>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core Scripts - Include with every page -->
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>

    <!-- SB Admin Scripts - Include with every page -->
    <script src="js/sb-admin.js"></script>

</body>

</html>
