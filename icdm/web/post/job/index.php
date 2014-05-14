<?php
error_reporting(E_ALL);
require('../../include/sql.php');
if (!isset($_POST['method'])) {
	echo "ERROR: patient_id or method parameters are missing.";
}
else {
	$method = $_POST['method'];

	if ($method == "new") {
		$results = newJob(3, 'DEF', 'Heart rate is too low');
		echo json_encode($results);
	}
	elseif ($method == "update") {
		if (!isset($_POST['job_id'])) {
			echo "ERROR: job_id is missing.";
		}
		else {
			$job_id = $_POST['job_id'];
			$status = NULL;
			$description = NULL;
			$completed = NULL;

			if (isset($_POST['status'])) {
				$status = $_POST['status'];
			}

			if (isset($_POST['description'])) {
				$description = $_POST['description'];
			}

			if (isset($_POST['completed'])) {
				$completed = $_POST['status'];
			}

			updateJob($job_id, $status, $description, $completed);
		}
	}
}

?>