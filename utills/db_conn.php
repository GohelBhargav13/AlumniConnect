<?php
// loading the php environment variables from .env file
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$db_host = getenv('DB_HOST');
$db_user = getenv('USER');
$db_password = getenv('PASSWORD');
$db_url = getenv('DATABASE_URL');

$is_live = (
    $_SERVER["SERVER_NAME"] == "localhost" ||
    $_SERVER["SERVER_ADDR"] == "127.0.0.1"
);
// or check for your custom domain if you connect one

if ($is_live) {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "alumniconnect";
} else {
    $host = $db_host;
    $user = $db_user;
    $pass = $db_password;
    $db   = $db_url;
}

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
