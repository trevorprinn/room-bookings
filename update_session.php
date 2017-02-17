<?php

session_start();

if (isset($_GET['StartDate'])) $_SESSION['StartDate'] = $_GET['StartDate'];
if (isset($_GET['EndDate'])) $_SESSION['EndDate'] = $_GET['EndDate'];

if (isset($_GET['logout'])) session_destroy();

?>