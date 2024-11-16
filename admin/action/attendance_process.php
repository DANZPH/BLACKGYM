l<?php
session_start();
if (!isset($_SESSION['AdminID'])) {
    // Redirect to login page if not logged in as admin
    header('Location: ../../admin/login.php');
    exit();
}

include '../../database/connection.php'; // Include database connection

// Check if the necessary POST parameters are present
if (isset($_POST['memberId']) && isset($_POST['action'])) {
    $memberId = $_POST['memberId'];
    $action = $_POST['action'];

    // Prepare the SQL query to get the current attendance data for the member
    $sql = "SELECT * FROM Attendance WHERE MemberID = ? ORDER BY AttendanceDate DESC LIMIT 1";
    $stmt = $conn1->prepare($sql);
    $stmt->bind_param("i", $memberId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Process the check-in or check-out
    if ($result->num_rows > 0) {
        $attendance = $result->fetch_assoc();

        if ($action == 'checkin' && $attendance['CheckOut'] == '0000-00-00 00:00:00') {
            // Update Check-In time if member hasn't checked out yet
            $checkInTime = date('Y-m-d H:i:s');
            $updateSql = "UPDATE Attendance SET CheckIn = ?, AttendanceCount = AttendanceCount + 1 WHERE AttendanceID = ?";
            $updateStmt = $conn1->prepare($updateSql);
            $updateStmt->bind_param("si", $checkInTime, $attendance['AttendanceID']);
            if ($updateStmt->execute()) {
                echo json_encode(['message' => 'Check-in successful']);
            } else {
                echo json_encode(['message' => 'Error during check-in']);
            }
        } elseif ($action == 'checkout' && $attendance['CheckOut'] == '0000-00-00 00:00:00') {
            // Update Check-Out time
            $checkOutTime = date('Y-m-d H:i:s');
            $updateSql = "UPDATE Attendance SET CheckOut = ?, AttendanceCount = AttendanceCount + 1 WHERE AttendanceID = ?";
            $updateStmt = $conn1->prepare($updateSql);
            $updateStmt->bind_param("si", $checkOutTime, $attendance['AttendanceID']);
            if ($updateStmt->execute()) {
                echo json_encode(['message' => 'Check-out successful']);
            } else {
                echo json_encode(['message' => 'Error during check-out']);
            }
        } else {
            echo json_encode(['message' => 'Member already checked out or invalid action']);
        }
    } else {
        // No previous attendance record, create a new one for check-in
        if ($action == 'checkin') {
            $checkInTime = date('Y-m-d H:i:s');
            $insertSql = "INSERT INTO Attendance (MemberID, CheckIn, AttendanceDate, AttendanceCount) VALUES (?, ?, ?, 1)";
            $insertStmt = $conn1->prepare($insertSql);
            $insertStmt->bind_param("iss", $memberId, $checkInTime, $checkInTime);
            if ($insertStmt->execute()) {
                echo json_encode(['message' => 'Check-in successful']);
            } else {
                echo json_encode(['message' => 'Error during check-in']);
            }
        } else {
            echo json_encode(['message' => 'No existing check-in record found']);
        }
    }

    $stmt->close();
    $conn1->close();
} else {
    echo json_encode(['message' => 'Invalid request']);
}
?>