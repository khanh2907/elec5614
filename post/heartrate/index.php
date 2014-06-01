<?php
error_reporting(E_ALL);
require('../../include/sql.php');

if(!isset($_POST['heartrate']) || !isset($_POST['patient_id'])) {
	echo "ERROR";
}
else {
	postHeartRate($_POST['patient_id'], $_POST['heartrate']);
}

?>