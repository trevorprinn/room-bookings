<?php

include('bookings_db.php');

$db = new bookings_db();

$action = $_GET['action'];

$postdata = json_decode(file_get_contents('php://input'), true);

if ($action == 'add') {
	$id = $db->add_room($postdata['Name'], $postdata['Color'], $postdata['ColorProv']);
	$room['Id_Room'] = $id;
	$room['Name'] = $postdata['Name'];
	$room['Color'] = $postdata['Color'];
	$room['ColorProv'] = $postdata['ColorProv'];
	$facs = $db->get_roomfacilities($id);
	while ($fac = $facs->fetch_assoc()) {
		$facilities[] = $fac;
	}
	$room['facilities'] = $facilities;
	echo json_encode($room);
} else if ($action == 'update') {
	$db->rename_room($postdata['id'], $postdata['Name'], $postdata['Color'], $postdata['ColorProv']);
} else if ($action == 'delete') {
	$db->delete_room($postdata['id']);
} else if ($action == 'updatefac') {
	$roomid = $postdata['roomid'];
	$facid = $postdata['facid'];
	if ($postdata['available'] == 'true') {
		$db->add_room_facility($roomid, $facid);
	} else {
		$db->remove_room_facility($roomid, $facid);
	}
}
$db->close();

?>


