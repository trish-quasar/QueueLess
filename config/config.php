<?php
$db_host = "127.0.0.1";
$db_user = "root";
$db_pass = "root";
$db_name = "queueless_db";

mysqli_report(MYSQLI_REPORT_OFF);


$db_port = 3306;
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);


if ($conn->connect_error) {
    $db_port = 8889;
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
}

if ($conn->connect_error) {
    die("Database connection failed. " .
        "Make sure MAMP MySQL is running, the database 'queueless_db' is imported, " .
        "and the MAMP MySQL username/password are root/root. " .
        "Error: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
