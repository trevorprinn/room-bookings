<?php
include('bookings_db.php');

$db = new bookings_db();

$postdata = json_decode(file_get_contents('php://input'), true);
$ids = $postdata['ids'];
$data = $postdata['data'];

$db->merge_bookers($ids, $data); 

?>