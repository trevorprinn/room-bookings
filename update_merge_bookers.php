<?php
include('bookings_db.php');

$db = new bookings_db();

try {
	$postdata = json_decode(file_get_contents('php://input'), true);
	$ids = $postdata['ids'];
	$data = $postdata['data'];
	
	$db->merge_bookers($ids, $data);
	$result['success'] = true;
	echo json_encode($result);
} catch (Exception $e) {
	$result['success'] = false;
	$result['exception'] = $e;
	echo json_encode($result);
}

?>