<?php
require 'config.php';
$mysqli = new mysqli(HOST, USERNAME, PASSWORD,DB);
if ($mysqli->connect_errno) {
    die("Failed to connent mysql: " . $mysqli->connect_error);
}
