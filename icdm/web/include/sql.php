<?php
error_reporting(E_ALL);

function connect($file = 'config.ini') {
	// read database seetings from config file
    if ( !$settings = parse_ini_file($file, TRUE) ) 
        throw new exception('Unable to open ' . $file);
    
    // parse contents of config.ini
    $dns = $settings['database']['driver'] . ':' .
            'host=' . $settings['database']['host'] .
            ((!empty($settings['database']['port'])) ? (';port=' . $settings['database']['port']) : '') .
            ';dbname=' . $settings['database']['schema'];
    $user= $settings['db_user']['username'];
    $pw  = $settings['db_user']['password'];

	// create new database connection
    try {
        $dbh=new PDO($dns, $user, $pw);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        print "Error Connecting to Database: " . $e->getMessage() . "<br/>";
        die();
    }
    return $dbh;
}

function checkLogin($name,$pass) {
    $db = connect();
    try {
        $stmt = $db->prepare('SELECT (password = MD5(:pass)) FROM doctor WHERE username = :name');
        $stmt->bindValue(':name', $name, PDO::PARAM_INT);
        $stmt->bindValue(':pass', $pass, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        $stmt->closeCursor();
    } catch (PDOException $e) { 
        print "Error checking login: " . $e->getMessage(); 
        return FALSE;
    }
    return ($result == 1);
}

function getDoctorId($name) {
    $db = connect();
    try {
        $stmt = $db->prepare('SELECT id FROM doctor WHERE username = :name');
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        $stmt->closeCursor();
    } catch (PDOException $e) { 
        print "Error getting doctor id: " . $e->getMessage(); 
    }
    return $result;
}

function getDoctors() {
    $db = connect();
    try {
        $stmt = $db->prepare('SELECT * FROM doctor');
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $e) { 
        print "Error getting doctors: " . $e->getMessage(); 
    }
    return $result;
}

function getPatients() {
	$db = connect();
    try {
        $stmt = $db->prepare('SELECT * FROM patient');
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $e) { 
        print "Error getting doctors: " . $e->getMessage(); 
    }
    return $result;
}

function getPatientDetails($patiendId) {
    $db = connect();
    try {
        $stmt = $db->prepare('SELECT name, surname FROM patient WHERE id = :patient_id');
        $stmt->bindValue(':patient_id', $patiendId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $e) { 
        print "Error getting patients: " . $e->getMessage(); 
    }
    return $result;
}

function getPatientsOf($doctorId) {
    $db = connect();
    try {
        $stmt = $db->prepare('SELECT name, surname, id FROM patient WHERE doctor_id = :doctor_id');
        $stmt->bindValue(':doctor_id', $doctorId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $e) { 
        print "Error getting patients: " . $e->getMessage(); 
    }
    return $result;
}

function postHeartRate($patientId, $heartRate) {
    $db = connect();
    try {
        $stmt = $db->prepare('INSERT INTO heartrate(patient_id, heartrate) VALUES (:patient_id, :heartrate)');
        $stmt->bindValue(':heartrate', $heartRate, PDO::PARAM_STR);
        $stmt->bindValue(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();        
    } catch (PDOException $e) { 
        print "Error recording heart rate: " . $e->getMessage(); 
    }
}

function getHeartRateOf($patientId) {
    $db = connect();
    try {
        $stmt = $db->prepare('SELECT heartrate, time FROM heartrate WHERE patient_id = :patient_id');
        $stmt->bindValue(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $e) { 
        print "Error getting heart rate: " . $e->getMessage(); 
    }
    return $result;
}

function getHeartRateHours($patientId, $hours) {
    $db = connect();
    try {
        $stmt = $db->prepare('SELECT heartrate, time FROM heartrate WHERE patient_id = :patient_id AND time >= now() - INTERVAL :hours HOUR');
        $stmt->bindValue(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->bindValue(':hours', $hours, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $e) { 
        print "Error getting heart rate: " . $e->getMessage(); 
    }
    return $result;
}

function getHeartRateMinutes($patientId, $minutes) {
    $db = connect();
    try {
        $stmt = $db->prepare('SELECT heartrate, time FROM heartrate WHERE patient_id = :patient_id AND time >= now() - INTERVAL :minutes MINUTE');
        $stmt->bindValue(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->bindValue(':minutes', $minutes, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $e) { 
        print "Error getting heart rate: " . $e->getMessage(); 
    }
    return $result;
}

function getAverageHeartRateByMinutes($id, $minutes) {
    $hrList = getHeartRateMinutes($id, $minutes);

    if (sizeof($hrList) == 0) {
        return '-';
    }

    $total = 0;
    foreach ($hrList as $hr) {
        $total += $hr['heartrate'];
    }

    return round($total / sizeof($hrList), 2);
}

function getAverageHeartRateByHours($id, $hours) {
    $hrList = getHeartRateHours($id, $hours);

    if (sizeof($hrList) == 0) {
        return '-';
    }

    $total = 0;
    foreach ($hrList as $hr) {
        $total += $hr['heartrate'];
    }

    return round($total / sizeof($hrList), 2);
}

function getCurrentHeartRateOf($patientId) {
    $db = connect();
    try {
        $stmt = $db->prepare('SELECT heartrate FROM heartrate WHERE patient_id = :patient_id ORDER BY time DESC LIMIT 1' );
        $stmt->bindValue(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $e) { 
        print "Error getting heart rate: " . $e->getMessage(); 
    }
    return $result;
    
}

function newJob($patient_id, $type, $description) {
    $db = connect();
    try {
        $stmt = $db->prepare('INSERT INTO job(patient_id, type, status, start_time, description) 
            VALUES (:patient_id, :type, "STARTED", CURRENT_TIMESTAMP, :description)');
        $stmt->bindValue(':type', $type, PDO::PARAM_STR);
        $stmt->bindValue(':patient_id', $patient_id, PDO::PARAM_INT);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();        

        $stmt2 = $db->prepare('SELECT MAX(id) FROM job WHERE patient_id= :patient_id LIMIT 1');
        $stmt2->bindValue(':patient_id', $patient_id, PDO::PARAM_INT);
        $stmt2->execute();
        $result = $stmt2->fetchAll();
        $stmt2->closeCursor();   
    } catch (PDOException $e) { 
        print "Error creating new job: " . $e->getMessage(); 
    }
    return $result;
}

function updateJob($job_id, $status, $description, $completed){
    $db = connect();
    if ($status != NULL) {
        try {
            $stmt = $db->prepare('UPDATE job SET status=:status WHERE id=:job_id');
            $stmt->bindValue(':job_id', $job_id, PDO::PARAM_INT);
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
        } catch (PDOException $e) { 
            print "Error updating job: " . $e->getMessage(); 
        }
    }
    elseif ($description != NULL) {
        try {
            $stmt = $db->prepare('UPDATE job SET description=CONCAT(description, :description) WHERE id=:job_id');
            $stmt->bindValue(':job_id', $job_id, PDO::PARAM_INT);
            $stmt->bindValue(':description', $description, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
        } catch (PDOException $e) { 
            print "Error updating job: " . $e->getMessage(); 
        }
    }
    elseif ($completed) {
        try {
            $stmt = $db->prepare("UPDATE job SET end_time=CURRENT_TIMESTAMP, status='COMPLETE' WHERE id=:job_id");
            $stmt->bindValue(':job_id', $job_id, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
        } catch (PDOException $e) { 
            print "Error updating job: " . $e->getMessage(); 
        }
    }
}

function getLatestJobs($patient_id, $limit) {
    $db = connect();
    try {
        $stmt = $db->prepare('SELECT * FROM job WHERE patient_id = :patient_id ORDER BY start_time DESC LIMIT :limit_num');
        $stmt->bindValue(':patient_id', $patient_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit_num', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $e) { 
        print "Error getting latest jobs: " . $e->getMessage(); 
    }
    return $result;
}

function getDashboardJobs($doctor_id) {
    $db = connect();
    // SELECT * FROM patient p RIGHT JOIN job b ON (p.id = b.patient_id) WHERE doctor_id = 1;
    try {
        $stmt = $db->prepare('SELECT * FROM patient p RIGHT JOIN job b ON (p.id = b.patient_id) WHERE doctor_id = :doctor_id;');
        $stmt->bindValue(':doctor_id', $doctor_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $e) { 
        print "Error getting dashboard jobs: " . $e->getMessage(); 
    }
    return $result;
}

?>