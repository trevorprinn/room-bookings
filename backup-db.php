<?php
include('api/shuttle-export/dumper.php');
include('config.php');

$done = 0;
$senddata = !isset($_GET['geturl']);
try {
	$dumper = Shuttle_Dumper::create([
		'host' => DB_HOST,
		'username' => DB_USER,
		'password' => DB_PASSWORD,
		'db_name' => DB_NAME
	]);
	
	$file = 'api/temp/backup.sql.gz';
	$dumper->dump($file);
	$done = 1;
} catch(Shuttle_Exception $e) {
	header("Dump Error", true, 500);
	echo $e->getMessage();
}

if ($done == 1) {
	if ($senddata) {
		readfile($file);
	} else {
		echo "api/temp/backup.sql.gz";
	}
}
?>