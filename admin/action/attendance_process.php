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
        // Check if the member is already checked in today
        $attendanceCheck = "SELECT AttendanceCount FROM Attendance 
                            WHERE MemberID = ? AND DATE(AttendanceDate) = CURDATE()";
        $stmt = $conn1->prepare($attendanceCheck);
        $stmt->bind_param("i", $memberId);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            // Member is already checked in for today
            echo json_encode(['message' => 'Member is already checked in today.', 'status' => 'error']);
            $stmt->close();
            exit();
        }

        // Insert new check-in record
        $sql = "INSERT INTO Attendance (MemberID, CheckIn, AttendanceDate, AttendanceCount) 
                VALUES (?, NOW(), NOW(), 1)";
        $stmt = $conn1->prepare($sql);
        $stmt->bind_param("i", $memberId);
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Check-In successful.', 'status' => 'success']);
        } else {
            echo json_encode(['message' => 'Error during check-in.', 'status' => 'error']);
        }
        $stmt->close();
    } elseif ($action === 'Check Out') {
        // Update check-out time and increment AttendanceCount
        $sql = "UPDATE Attendance 
                SET CheckOut = NOW(), AttendanceCount = AttendanceCount + 1 
                WHERE MemberID = ? AND DATE(AttendanceDate) = CURDATE() AND CheckOut = '0000-00-00 00:00:00'";
        $stmt = $conn1->prepare($sql);
        $stmt->bind_param("i", $memberId);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(['message' => 'Check-Out successful.', 'status' => 'success']);
        } else {
            echo json_encode(['message' => 'Error during check-out.', 'status' => 'error']);
        }
        $stmt->close();
    } else {
        echo json_encode(['message' => 'Invalid action.', 'status' => 'error']);
    }
}
$conn1->close();