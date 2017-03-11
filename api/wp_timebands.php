<?php
header("Content-Type: application/json;charset=utf-8");
header("Access-Control-Allow-Origin: *");

include('../config.php');

echo json_encode(TIME_BANDS);

?>