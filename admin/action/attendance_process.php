<?php
include '../../database/connection.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $memberId = $_POST['memberId'];
    $action = $_POST['action'];

    if ($action === 'Check In') {
        // Check if the member is already checked in
        $sqlCheck = "SELECT * FROM Attendance WHERE MemberID = ? AND CheckOut = '0000-00-00 00:00:00'";
        $stmtCheck = $conn1->prepare($sqlCheck);
        $stmtCheck->bind_param("i", $memberId);
        $stmtCheck->execute();
        $stmtCheck->store_result();

        if ($stmtCheck->num_rows > 0) {
            echo json_encode(['message' => 'Already checked in.']);
            $stmtCheck->close();
            exit();
        }

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
        // Update the check-out record
        $sql = "UPDATE Attendance SET CheckOut = NOW() WHERE MemberID = ? AND CheckOut = '0000-00-00 00:00:00'";
        $stmt = $conn1->prepare($sql);
        $stmt->bind_param("i", $memberId);
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Check-Out successful.']);
        } else {
            echo json_encode(['message' => 'Error during check-out.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['message' => 'Invalid action.']);
    }
} else {
    echo json_encode(['message' => 'Invalid request method.']);
}
?>