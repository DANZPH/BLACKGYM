<?php
require 'config.php';

$conn1 = new mysqli($host, $username, $password, $dbname1);
if ($conn1->connect_error) {
    die("Connection failed for first database: " . $conn1->connect_error);
}

$conn2 = new mysqli($host, $username, $password, $dbname2);
if ($conn2->connect_error) {
    die("Connection failed for second database: " . $conn2->connect_error);
}
?>