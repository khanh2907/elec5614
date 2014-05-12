<?php
error_reporting(E_ALL);

print_r(getHeartRateOf(1));

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

function postHeartRate($patientId, $heartRate) {
	$db = connect();
	try {
		$stmt = $db->prepare('INSERT INTO heartrate(patient_id, heartrate) VALUES (:patient_id, :heartrate)');
		$stmt->bindValue(':heartrate', $heartRate, PDO::PARAM_STR);
		$stmt->bindValue(':patient_id', $patientId, PDO::PARAM_INT);
		$stmt->execute();
        $stmt->closeCursor();        
    } catch (PDOException $e) { 
        print "Error checking login: " . $e->getMessage(); 
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
        print "Error checking login: " . $e->getMessage(); 
    }
    return $result;
}

function getDoctors(){
    $db = connect();
    try {
        $stmt = $db->prepare('SELECT * FROM doctor');
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $e) { 
        print "Error checking login: " . $e->getMessage(); 
    }
    return $result;
}

?>