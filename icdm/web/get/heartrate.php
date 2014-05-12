<?php
error_reporting(E_ALL);
require('../include/sql.php');

if(!isset($_GET['patient_id'])) {
	echo "ERROR";
}
else {
	$heartrates = getHeartRateOf($_GET['patient_id']);
	echo json_encode($heartrates);
}

?>