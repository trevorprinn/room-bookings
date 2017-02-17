<?php
include('bookings_db.php');

$db = new bookings_db();

$db->update_booker(json_decode(file_get_contents('php://input'), true));

$db->close();

?>
