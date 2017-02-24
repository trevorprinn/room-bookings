<?php
header("Content-Type: application/json;charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include('../bookings_db.php');
$db = new bookings_db();

try {
	
	function errorhandler($errno, $errstr, $errfile, $errline) {
		throw new Exception($errstr);
	}
	
	set_error_handler('errorhandler', E_ALL);
	$postdata = json_decode(file_get_contents('php://input'), true);
	
	$db->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
	
	$bookerId = $db->find_booker($postdata['BookerName'], $postdata['BookerEmail']);
	if ($bookerId == 0) {
		$bfields['Id_Booker'] = 0;
		$bfields['Name'] = $postdata['BookerName'];
		$bfields['Email'] = $postdata['BookerEmail'];
		$bookerId = $db->update_booker($bfields);
	}
	
	$f['Id_Booking'] = 0;
	$f['Id_Booker'] = $bookerId;
	$f['Id_Room'] = $postdata['Id_Room'];
	$f['Title'] = $postdata['Title'];
	$f['Date'] = $postdata['Date'];
	$f['Start'] = $postdata['Start'];
	$f['Duration'] = $postdata['Duration'];
	$f['Notes'] = isset($postdata['Notes']) ? $postdata['Notes'] : "";
	$f['Provisional'] = 1;
	$f['facilities'] = $postdata['facilities'];
	
	$db->update_booking($f, true);
	
	$db->commit();
	
	$result['success'] = true;
	echo json_encode($result);
} catch (Exception $e) {
	$result['success'] = false;
	$result['exception'] = $e->getMessage();
	echo json_encode($result);
}

?>