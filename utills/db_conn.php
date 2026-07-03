<?php
$host_name = "localhost";
$username = "root";
$password = "";
$db_name = "alumniconnect";
$port = 3306;

$conn = mysqli_connect($host_name, $username, $password, $db_name, $port) or die("Database not connected");
