<?php
$host = "sql104.infinityfree.com";
$username = "if0_36048499";
$password = "LokK4Hhvygq";

$dbname1 = "if0_36048499_db_user";

$conn1 = new mysqli($host, $username, $password, $dbname1);

if ($conn1->connect_error) {
    die("Connection failed for first database: " . $conn1->connect_error);
    }
?>
        