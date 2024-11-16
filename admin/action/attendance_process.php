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
            echo json_encode(['message' => 'Member is already checked in today.']);
            $stmt->close();
            exit();
        }

        // Insert new check-in record
        $sql = "INSERT INTO Attendance (MemberID, CheckIn, AttendanceDate, AttendanceCount) 
                VALUES (?, NOW(), NOW(), 0)";
        $stmt = $conn1->prepare($sql);
        $stmt->bind_param("i", $memberId);
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Check-In successful.', 'action' => 'Check In']);
        } else {
            echo json_encode(['message' => 'Error during check-in: ' . $stmt->error]);
        }
        $stmt->close();
    } elseif ($action === 'Check Out') {
        // Update check-out time and increment attendance count
        $sql = "UPDATE Attendance 
                SET CheckOut = NOW() 
                WHERE MemberID = ? AND DATE(AttendanceDate) = CURDATE() AND CheckOut = '0000-00-00 00:00:00'";
        $stmt = $conn1->prepare($sql);
        $stmt->bind_param("i", $memberId);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            // Update AttendanceCount after check-out
            $updateCountSql = "UPDATE Attendance 
                               SET AttendanceCount = AttendanceCount + 1 
                               WHERE MemberID = ? AND DATE(AttendanceDate) = CURDATE()";
            $updateStmt = $conn1->prepare($updateCountSql);
            $updateStmt->bind_param("i", $memberId);
            if ($updateStmt->execute()) {
                echo json_encode(['message' => 'Check-Out successful.', 'action' => 'Check Out']);
            } else {
                echo json_encode(['message' => 'Error updating attendance count.']);
            }
            $updateStmt->close();
        } else {
            echo json_encode(['message' => 'Error during check-out.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['message' => 'Invalid action.']);
    }
}
$conn1->close();