<?php
error_reporting(E_ALL);
require('../include/sql.php');

if(!isset($_GET['method'])) {
	echo "ERROR: missing method";
}
else {
	if ($_GET['method'] == 'patient'){
		if (!isset($_GET['patient_id'])) {
			echo "ERROR: missing patient_id";			
		}
		else {
			$results = getLatestJobs($_GET['patient_id'], 10);
			echo json_encode($results);
		}
	}
	else if ($_GET['method'] == 'doctor') {
		if (!isset($_GET['doctor_id'])) {
			echo "ERROR: missing doctor_id";			
		}
		else {
			echo 0;
		}
	}
	
}

?>