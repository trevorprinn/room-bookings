<?php
include('bookings_db.php');

$db = new bookings_db();

$data = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'];

if ($action == 'update') {
	$db->update_booking($data);
} else if ($action == 'delete') {
	$db->delete_booking($data['Id_Booking']);
}

$db->close();

?>
