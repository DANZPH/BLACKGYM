<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['AdminID'])) {
    echo json_encode(['message' => 'Unauthorized access.']);
    exit();
}

include '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberId = intval($_POST['memberId']);
    $action = $_POST['action'];

    // Log the received values for debugging
    error_log("Received memberId: $memberId and action: $action");

    // Check if MemberID exists in Members table
    $checkMember = "SELECT * FROM Members WHERE MemberID = ?";
    $stmt = $conn1->prepare($checkMember);
    $stmt->bind_param("i", $memberId);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        echo json_encode(['message' => 'Member not found.']);
        $stmt->close();
        exit();
    }
    $stmt->close();

    // Action: Check In
    if ($action === 'Check In') {
        // Check if the member has already checked in today
        $attendanceCheck = "SELECT AttendanceCount FROM Attendance 
                            WHERE MemberID = ? AND DATE(AttendanceDate) = CURDATE()";
        $stmt = $conn1->prepare($attendanceCheck);
        $stmt->bind_param("i", $memberId);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Member already checked in
            echo json_encode(['message' => 'Member is already checked in today.']);
            $stmt->close();
            exit();
        }

        // Insert new Check-In record
        $sql = "INSERT INTO Attendance (MemberID, CheckIn, AttendanceDate, AttendanceCount) 
                VALUES (?, NOW(), NOW(), 1)";
        $stmt = $conn1->prepare($sql);
        $stmt->bind_param("i", $memberId);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Check-In successful.']);
        } else {
            error_log("Error during Check-In: " . $stmt->error); // Log SQL error
            echo json_encode(['message' => 'Error during check-in.']);
        }
        $stmt->close();
    } 
    // Action: Check Out
    elseif ($action === 'Check Out') {
        // Update the CheckOut time for today
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
            $updateStmt->execute();
            echo json_encode(['message' => 'Check-Out successful.']);
            $updateStmt->close();
        } else {
            error_log("Error during Check-Out: " . $stmt->error); // Log SQL error
            echo json_encode(['message' => 'Error during check-out.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['message' => 'Invalid action.']);
    }
} else {
    echo json_encode(['message' => 'Invalid request method.']);
}

$conn1->close();