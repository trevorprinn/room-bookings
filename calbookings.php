<?php
include('bookings_db.php');

$db = new bookings_db();
$bookings = $db->get_bookings();

class bookingdata {
	public $title;
	public $start;
	public $url;
	public $end;
	public $backgroundColor;
	
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
	}	
}

$data = [];
foreach ($bookings as $booking) {
	$data[] = new bookingdata($booking);
}

echo json_encode($data);

?>