<?php
header("Content-Type: application/json;charset=utf-8");
header("Access-Control-Allow-Origin: *");

include('../bookings_db.php');
$db = new bookings_db();

$rooms = $db->get_rooms();
while ($room = $rooms->fetch_assoc()) {
	$facs = $db->get_roomfacilities($room['Id_Room']);
	$facilities = null;
	while ($fac = $facs->fetch_assoc()) {
		$facilities[] = $fac;
	}
	$room['facilities'] = $facilities;
	$data[] = $room;
}

echo json_encode($data);
?>