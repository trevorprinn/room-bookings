<?php
header("Content-Type: application/json;charset=utf-8");
header("Access-Control-Allow-Origin: *");

include('../bookings_db.php');
$db = new bookings_db();

$start = date('Y-m-d', getdate()['0']);
// Add about 10 years
$end = date('Y-m-d', getdate()['0'] + 315360000);
$rows = $db->get_bookings_wp($start, $end);

$data = [];
while ($row = $rows->fetch_assoc()) {
	$data[] = $row;
}

echo json_encode($data);
?>