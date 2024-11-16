<?php
include '../../database/connection.php'; // Include database connection

if (isset($_POST['action']) && $_POST['action'] == 'toggleAttendance' && isset($_POST['memberID'])) {
    $memberID = $_POST['memberID'];

    // Check the current attendance status
    $checkSql = "SELECT * FROM Attendance WHERE MemberID = ? ORDER BY AttendanceDate DESC LIMIT 1";
    $stmt = $conn1->prepare($checkSql);
    $stmt->bind_param("i", $memberID);
    $stmt->execute();
    $result = $stmt->get_result();
    $attendance = $result->fetch_assoc();

    if ($attendance && empty($attendance['CheckOut'])) {
        // Member is checked in, so we check them out
        $updateSql = "UPDATE Attendance SET CheckOut = NOW(), AttendanceCount = AttendanceCount + 1 WHERE AttendanceID = ?";
        $stmt = $conn1->prepare($updateSql);
        $stmt->bind_param("i", $attendance['AttendanceID']);
        $stmt->execute();
        echo 'checkedOut';
    } else {
        // Member is not checked in, so we check them in
        $insertSql = "INSERT INTO Attendance (MemberID, CheckIn, AttendanceCount) VALUES (?, NOW(), 1)";
        $stmt = $conn1->prepare($insertSql);
        $stmt->bind_param("i", $memberID);
        $stmt->execute();
        echo 'checkedIn';
    }
}
?>