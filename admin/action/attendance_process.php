l<?php
include '../../database/connection.php'; // Include database connection

if (isset($_POST['action']) && $_POST['action'] == 'toggleAttendance' && isset($_POST['memberID'])) {
    $memberID = $_POST['memberID'];

    // Check if the member already has an active attendance (checked-in and no checkout)
    $checkSql = "SELECT * FROM Attendance WHERE MemberID = ? AND CheckOut = '0000-00-00 00:00:00' ORDER BY AttendanceDate DESC LIMIT 1";
    $stmt = $conn1->prepare($checkSql);
    $stmt->bind_param("i", $memberID);
    $stmt->execute();
    $result = $stmt->get_result();
    $attendance = $result->fetch_assoc();

    if ($attendance) {
        // Member is checked in, so we check them out
        // Increment AttendanceCount only when checking out
        $updateSql = "UPDATE Attendance SET CheckOut = NOW(), AttendanceCount = AttendanceCount + 1 WHERE AttendanceID = ?";
        $stmt = $conn1->prepare($updateSql);
        $stmt->bind_param("i", $attendance['AttendanceID']);
        $stmt->execute();
        echo 'checkedOut'; // Return this to change button text
    } else {
        // Member is not checked in, so we check them in
        // Insert attendance record without incrementing AttendanceCount
        $insertSql = "INSERT INTO Attendance (MemberID, CheckIn, AttendanceCount) VALUES (?, NOW(), 0)";
        $stmt = $conn1->prepare($insertSql);
        $stmt->bind_param("i", $memberID);
        $stmt->execute();
        echo 'checkedIn'; // Return this to change button text
    }
}
?>