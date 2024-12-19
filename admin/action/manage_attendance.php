<?php
include '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staffID = $_POST['staffID'];
    $attendanceDate = $_POST['attendanceDate'];
    $status = $_POST['status'];

    // Insert attendance record
    $sql = "INSERT INTO StaffPresence (StaffID, Date, Status) VALUES (?, ?, ?)";
    $stmt = $conn1->prepare($sql);
    $stmt->bind_param("iss", $staffID, $attendanceDate, $status);

    if ($stmt->execute()) {
        echo "Attendance recorded successfully.";
    } else {
        echo "Failed to record attendance.";
    }
}
?>