l<?php
include '../../database/connection.php'; // Include database connection

if (isset($_POST['action']) && $_POST['action'] == 'toggleAttendance' && isset($_POST['memberID'])) {
    $memberID = $_POST['memberID'];
    
    // Debug: Log received data
    error_log("Received MemberID: " . $memberID);

    // Check if the member is already checked in
    $checkSql = "SELECT * FROM Attendance WHERE MemberID = ? AND CheckOut = '0000-00-00 00:00:00' ORDER BY AttendanceDate DESC LIMIT 1";
    $stmt = $conn1->prepare($checkSql);
    $stmt->bind_param("i", $memberID);
    $stmt->execute();
    $result = $stmt->get_result();
    $attendance = $result->fetch_assoc();

    if ($attendance) {
        // Member is checked in, update CheckOut time and increment AttendanceCount
        $updateSql = "UPDATE Attendance SET CheckOut = NOW(), AttendanceCount = AttendanceCount + 1 WHERE AttendanceID = ?";
        $stmt = $conn1->prepare($updateSql);
        $stmt->bind_param("i", $attendance['AttendanceID']);
        $stmt->execute();
        
        // Debug: Log the updated attendance record
        error_log("Checked out memberID: " . $memberID);
        
        echo 'checkedOut';
    } else {
        // Member is not checked in, insert new CheckIn record
        $insertSql = "INSERT INTO Attendance (MemberID, CheckIn, AttendanceCount) VALUES (?, NOW(), 1)";
        $stmt = $conn1->prepare($insertSql);
        $stmt->bind_param("i", $memberID);
        $stmt->execute();
        
        // Debug: Log the inserted attendance record
        error_log("Checked in memberID: " . $memberID);
        
        echo 'checkedIn';
    }
}
?>