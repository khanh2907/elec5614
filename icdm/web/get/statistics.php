<?php
error_reporting(E_ALL);
require_once('../include/sql.php');

if (!isset($_GET['patient_id'])) {
	echo "ERROR";
}
else {
	$patient_id = $_GET['patient_id'];

	$average = [];
	
	$average['fiveMinutes'] = getAverageHeartRateByMinutes($patient_id, 5);
	$average['fifteenMinutes'] = getAverageHeartRateByMinutes($patient_id, 15);
	$average['thirtyMinutes'] = getAverageHeartRateByMinutes($patient_id, 30);
	$average['oneHour'] = getAverageHeartRateByHours($patient_id, 1);
	$average['oneAndHalfHour'] = getAverageHeartRateByHours($patient_id, 1.5);
	$average['twentyFourHours'] = getAverageHeartRateByHours($patient_id, 24);

	echo json_encode($average);
}



?>