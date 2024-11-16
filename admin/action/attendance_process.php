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
        $attendanceCheck = "SELECT AttendanceID, CheckOut FROM Attendance 
                            WHERE MemberID = ? AND DATE(AttendanceDate) = CURDATE()";
        $stmt = $conn1->prepare($attendanceCheck);
        $stmt->bind_param("i", $memberId);
        $stmt->execute();
        $stmt->store_result();

        // If already checked in, return message
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($attendanceId, $checkOut);
            $stmt->fetch();

            // If the member is checked in and hasn't checked out yet, prevent multiple check-ins
            if ($checkOut == '0000-00-00 00:00:00') {
                echo json_encode(['message' => 'Member is already checked in today.']);
                $stmt->close();
                exit();
            }
        }

        // Insert new check-in record
        $sql = "INSERT INTO Attendance (MemberID, CheckIn, AttendanceDate, AttendanceCount) 
                VALUES (?, NOW(), NOW(), 0)";  // AttendanceCount starts at 0 for check-in
        $stmt = $conn1->prepare($sql);
        $stmt->bind_param("i", $memberId);
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Check-In successful.', 'status' => 'checked_in']);
        } else {
            echo json_encode(['message' => 'Error during check-in.']);
        }
        $stmt->close();
    } elseif ($action === 'Check Out') {
        // Update check-out time for today's attendance
        $sql = "UPDATE Attendance 
                SET CheckOut = NOW(), AttendanceCount = AttendanceCount + 1 
                WHERE MemberID = ? AND DATE(AttendanceDate) = CURDATE() AND CheckOut = '0000-00-00 00:00:00'";
        $stmt = $conn1->prepare($sql);
        $stmt->bind_param("i", $memberId);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(['message' => 'Check-Out successful.', 'status' => 'checked_out']);
        } else {
            echo json_encode(['message' => 'Error during check-out or no active check-in found.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['message' => 'Invalid action.']);
    }
}
$conn1->close();