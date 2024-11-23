<?php
$host = "localhost";
$username = "if0_36048499";
$password = "LokK4Hhvygq";

$dbname1 = "if0_36048499_db_user";
$dbname2 = "if0_36048499_db_paste";

$conn1 = new mysqli($host, $username, $password, $dbname1);
error_reporting(E_ALL); ini_set('display_errors', 1); if (!$conn1->connect_error) {
    die("Connection failed for first database: " . $conn1->connect_error);
}

$conn2 = new mysqli($host, $username, $password, $dbname2);

if ($conn2->connect_error) {
    die("Connection failed for second database: " . $conn2->connect_error);
}

?>
