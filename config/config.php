<?php
mysqli_report(MYSQLI_REPORT_OFF);

$db_name = 'queueless_db';
$db_host = getenv('QUEUELESS_DB_HOST') ?: '127.0.0.1';
$db_user = getenv('QUEUELESS_DB_USER') ?: 'root';
$db_pass_env = getenv('QUEUELESS_DB_PASS');
$db_port_env = getenv('QUEUELESS_DB_PORT');

$candidates = [];

if ($db_pass_env !== false || $db_port_env !== false) {
    $candidates[] = [
        'password' => $db_pass_env !== false ? $db_pass_env : '',
        'port' => $db_port_env !== false ? (int)$db_port_env : 3306
    ];
} else {
    // Common local defaults: MAMP (root/root) and XAMPP (root/no password).
    $candidates = [
        ['password' => 'root', 'port' => 3306],
        ['password' => 'root', 'port' => 8889],
        ['password' => '', 'port' => 3306]
    ];
}

$conn = null;
$serverFound = false;

foreach ($candidates as $candidate) {
    $test = @new mysqli(
        $db_host,
        $db_user,
        $candidate['password'],
        '',
        $candidate['port']
    );

    if ($test->connect_error) {
        continue;
    }

    $serverFound = true;

    if ($test->select_db($db_name)) {
        $conn = $test;
        break;
    }

    $test->close();
}

if (!$conn) {
    if ($serverFound) {
        die("Database '{$db_name}' was not found. Import database/queueless.sql in phpMyAdmin first.");
    }

    die('Database connection failed. Start MySQL in MAMP/XAMPP and check config/config.php.');
}

$conn->set_charset('utf8mb4');
$conn->query("SET time_zone = '+06:00'");
?>
