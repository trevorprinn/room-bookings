<?php
include('bookings_db.php');

$db = new bookings_db();
$start = $_GET['start'];
$end = $_GET['end'];
$bookings = $db->get_bookings($start, $end);

class bookingdata {
	public $title;
	public $start;
	public $url;
	public $end;
	public $backgroundColor;
	public $provisional;
	
	function __construct($booking) {
		$this->title = $booking['Title'];
		$date = $booking['Date'];
		$start = $booking['Start'];
		$this->start = $date.'T'.sprintf("%02d", $start).":00:00";
		$endTime = $start + $booking['Duration'];
		$endDate = $date;
		if ($endTime >= 24) {
			$endTime = 0;
			$endDate = new DateTime($date);
			$endDate = $endDate->modify('+1 day')->format('Y-m-d');
		} 
		$this->end = $endDate.'T'.sprintf("%02d", $endTime).':00:00';
		$this->url = 'booking.php?id='.$booking['Id'];
		$this->backgroundColor = $booking['Color'];
		$this->provisional = $booking['Provisional'] == 1;
	}	
}

$data = [];
foreach ($bookings as $booking) {
	$data[] = new bookingdata($booking);
}

echo json_encode($data);

?>