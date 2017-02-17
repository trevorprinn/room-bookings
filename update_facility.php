<?php

include('bookings_db.php');

$db = new bookings_db();

$action = $_GET['action'];

$postdata = json_decode(file_get_contents('php://input'), true);

if ($action == 'add') {
	$newId = $db->add_facility($postdata['Name']);
	$data = ['Id_Facility'=>$newId, 'Name'=>$postdata['Name']];
	echo json_encode($data);
}
if ($action == 'update') {
	$db->rename_facility($postdata['id'], $postdata['Name']);
} 
if ($action == 'delete') {
	$db->delete_facility($postdata['id']);
}
$db->close();

?>


