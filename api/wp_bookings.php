<?php
header("Content-Type: application/json;charset=utf-8");
header("Access-Control-Allow-Origin: *");

include('../bookings_db.php');
$db = new bookings_db();

$rows = $db->get_bookings_wp();

$data = [];
while ($row = $rows->fetch_assoc()) {
	$data[] = $row;
}

echo json_encode($data);
?>