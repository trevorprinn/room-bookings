<?php
header("Content-Type: application/json;charset=utf-8");

session_start();

if (!isset($_SESSION['StartDate'])) $_SESSION['StartDate'] = date('Y-m-d');
if (!isset($_SESSION['EndDate'])) $_SESSION['EndDate'] = date('Y-m-d', strtotime('+1 year'));

include('bookings_db.php');
$db = new bookings_db();

$type = $_GET['type'];
$id = isset($_GET['id']) ? $_GET['id'] : 0;

if ($type == "facilities") {
	$rows = $db->get_facilities();
} else if ($type == "rooms") {
	$data = get_all_room_data($db);
} else if ($type == "bookers") {
	$data = get_all_bookers($db);
} else if ($type == "booker") {
	$data = $db->get_booker($id);
} else if ($type == "bookings") {
	$now = getdate();
	$startDate = isset($_GET['start']) ? $_GET['start']
		: sprintf('%02d-%02d-%02d', $now['mday'], $now['mon'], $now['year']);
	$endDate = isset($_GET['end']) ? $_GET['end']
		: sprintf('%02d-%02d-%02d', $now['mday'], $now['mon'], $now['year'] + 1);
	$rows = $db->get_bookings($startDate, $endDate);
} else if ($type == "booking") {
	$booking = $db->get_booking($id);
	$booking['facilities'] = $db->get_booking_facilities($id);
	if ($id == 0 && isset($_GET['date'])) {
		$booking['Date'] = $_GET['date'];
	}
	if ($id == 0 && isset($_GET['time'])) {
		$booking['Start'] = $_GET['time'];
	}
	$data['booking'] = $booking;
	$data['rooms'] = get_all_room_data($db); 
	$data['bookers'] = get_all_bookers($db);
	$data['timebands'] = TIME_BANDS;
} else if ($type == "session") {
	$data = $_SESSION;
}

if (isset($rows)) {
	$data = [];
	while ($row = $rows->fetch_assoc()) {
		$data[] = $row;
	}
}
echo json_encode($data);

function get_all_room_data($db) {
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
	return $data;
}

function get_all_bookers($db) {
	$rows = $db->get_bookers();
	$data = [];
	while ($row = $rows->fetch_assoc()) {
		$data[] = $row;
	}
	return $data;
}
?>