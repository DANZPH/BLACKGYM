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
        // Check if member is already checked in today
        $checkInCheck = "SELECT CheckIn FROM Attendance WHERE MemberID = ? AND DATE(AttendanceDate) = CURDATE()";
        $stmt = $conn1->prepare($checkInCheck);
        $stmt->bind_param("i", $memberId);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo json_encode(['message' => 'Already checked in today.']);
        } else {
            // Insert check-in record
            $sql = "INSERT INTO Attendance (MemberID, CheckIn, AttendanceDate) VALUES (?, NOW(), NOW())";
            $stmt = $conn1->prepare($sql);
            $stmt->bind_param("i", $memberId);
            if ($stmt->execute()) {
                echo json_encode(['message' => 'Check-In successful.']);
            } else {
                echo json_encode(['message' => 'Error during check-in.']);
            }
        }
        $stmt->close();
    } elseif ($action === 'Check Out') {
        // Update check-out time for today
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