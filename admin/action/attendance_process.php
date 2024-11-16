<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    echo json_encode(['message' => 'Unauthorized access.']);
    exit();
}

include '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberId = intval($_POST['memberId']);
    $action = $_POST['action'];

    if ($action === 'Check In') {
        // Insert check-in record
        $sql = "INSERT INTO Attendance (MemberID, CheckIn, AttendanceDate) VALUES (?, NOW(), NOW())";
        $stmt = $conn1->prepare($sql);
        $stmt->bind_param("i", $memberId);
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Check-In successful.']);
        } else {
            echo json_encode(['message' => 'Error during check-in.']);
        }
        $stmt->close();
    } elseif ($action === 'Check Out') {
        // Update check-out time
        $sql = "UPDATE Attendance SET CheckOut = NOW() WHERE MemberID = ? AND DATE(AttendanceDate) = CURDATE() AND CheckOut = '0000-00-00 00:00:00'";
        $stmt = $conn1->prepare($sql);
        $stmt->bind_param("i", $memberId);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(['message' => 'Check-Out successful.']);
        } else {
            echo json_encode(['message' => 'Error during check-out.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['message' => 'Invalid action.']);
    }
}
$conn1->close();