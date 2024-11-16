<?php
include '../../database/connection.php'; // Include database connection

if (isset($_POST['action']) && $_POST['action'] == 'toggleAttendance' && isset($_POST['memberID'])) {
    $memberID = $_POST['memberID'];

    // Check if the member is already checked in (no CheckOut timestamp)
    $checkSql = "SELECT * FROM Attendance WHERE MemberID = ? AND CheckOut = '0000-00-00 00:00:00' LIMIT 1";
    $stmt = $conn1->prepare($checkSql);
    $stmt->bind_param("i", $memberID);
    $stmt->execute();
    $result = $stmt->get_result();
    $attendance = $result->fetch_assoc();

    if ($attendance) {
        // Member is currently checked in, so check them out
        $updateSql = "UPDATE Attendance 
                      SET CheckOut = NOW(), 
                          AttendanceCount = AttendanceCount + 1 
                      WHERE AttendanceID = ?";
        $stmt = $conn1->prepare($updateSql);
        $stmt->bind_param("i", $attendance['AttendanceID']);
        $stmt->execute();
        echo 'checkedOut'; // Return this to update button
    } else {
        // Member is not checked in, so check them in
        $insertSql = "INSERT INTO Attendance (MemberID, CheckIn, CheckOut, AttendanceCount) 
                      VALUES (?, NOW(), '0000-00-00 00:00:00', 0)";
        $stmt = $conn1->prepare($insertSql);
        $stmt->bind_param("i", $memberID);
        $stmt->execute();
        echo 'checkedIn'; // Return this to update button
    }
}
?>