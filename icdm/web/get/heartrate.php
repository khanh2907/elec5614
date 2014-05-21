<?php
error_reporting(E_ALL);
require('../include/sql.php');

if(!isset($_GET['patient_id']) || !isset($_GET['type'])) {
	echo "ERROR";
}
else {
	if ($_GET['type'] == 'graph'){
		$heartrates = getHeartRateHours($_GET['patient_id'], 1);
		echo json_encode($heartrates);
	}
	else if ($_GET['type'] == 'current') {
		$currentRate = getCurrentHeartRateOf($_GET['patient_id']);
		echo json_encode($currentRate);
	}
	
}

?>