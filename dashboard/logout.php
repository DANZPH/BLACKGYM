<?php
session_start();

// Destroy the session
session_destroy();

// Redirect to index.php
header("Location: ../index.php");
exit();
?>