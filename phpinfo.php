<?php
echo 'Current PHP timezone: ' . date_default_timezone_get();


// Set the timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Example usage of date and time functions
echo date('Y-m-d H:i:s');  // Outputs the current date and time in Manila timezone
?>
