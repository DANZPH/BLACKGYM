<?php
include '../../database/connection.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['memberId']) && isset($_POST['action'])) {
        $memberId = $_POST['memberId'];
        $action = $_POST['action'];
        $message = '';

        if ($action === 'checkin') {
            // Check if the member is already checked in
            $sql = "SELECT * FROM Attendance WHERE MemberID = ? AND CheckOut = '0000-00-00 00:00:00' ORDER BY AttendanceDate DESC LIMIT 1";
            $stmt = $conn1->prepare($sql);
            $stmt->bind_param("i", $memberId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $message = "Member is already checked in.";
            } else {
                // Insert check-in record
                $sql = "INSERT INTO Attendance (MemberID) VALUES (?)";
                $stmt = $conn1->prepare($sql);
                $stmt->bind_param("i", $memberId);
                if ($stmt->execute()) {
                    // Increment AttendanceCount for the member
                    $sqlCount = "UPDATE Members SET AttendanceCount = AttendanceCount + 1 WHERE MemberID = ?";
                    $stmtCount = $conn1->prepare($sqlCount);
                    $stmtCount->bind_param("i", $memberId);
                    $stmtCount->execute();

                    $message = "Check-in successful!";
                } else {
                    $message = "Error during check-in.";
                }
            }
        } elseif ($action === 'checkout') {
            // Update check-out time
            $sql = "UPDATE Attendance SET CheckOut = NOW() WHERE MemberID = ? AND CheckOut = '0000-00-00 00:00:00' ORDER BY AttendanceDate DESC LIMIT 1";
            $stmt = $conn1->prepare($sql);
            $stmt->bind_param("i", $memberId);
            if ($stmt->execute()) {
                $message = "Check-out successful!";
            } else {
                $message = "Error during check-out.";
            }
        }

        echo json_encode(["message" => $message]);
    } else {
        echo json_encode(["message" => "Invalid request."]);
    }
}