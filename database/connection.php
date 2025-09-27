<?php
$host = "localhost";
$username = "root";
$password = "";

$dbname1 = "blackgym";

$conn1 = new mysqli($host, $username, $password, $dbname1);

if ($conn1->connect_error) {
     die("Connection failed for first database: " . $conn1->connect_error);
     }
?>
        
