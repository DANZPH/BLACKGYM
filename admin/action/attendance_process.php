<?php
include '../../database/connection.php'; // Include database connection

if (isset($_POST['action']) && $_POST['action'] == 'toggleAttendance' && isset($_POST['memberID'])) {
    $memberID = $_POST['memberID'];

    // Check if the member already has an active attendance record (not checked out)
    $checkSql = "SELECT AttendanceID FROM Attendance WHERE MemberID = ? AND CheckOut = '0000-00-00 00:00:00'";
    $stmt = $conn1->prepare($checkSql);
    $stmt->bind_param("i", $memberID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Member is already checked in, so we check them out and increment attendance count
        $attendance = $result->fetch_assoc();
        $attendanceID = $attendance['AttendanceID'];

        $updateSql = "UPDATE Attendance SET CheckOut = NOW(), AttendanceCount = AttendanceCount + 1 WHERE AttendanceID = ?";
        $stmt = $conn1->prepare($updateSql);
        $stmt->bind_param("i", $attendanceID);

        if ($stmt->execute()) {
            echo 'checkedOut'; // Indicate successful checkout
        } else {
            echo 'error'; // Indicate an error
        }
    } else {
        // No active record found, so create a new attendance record
        $insertSql = "INSERT INTO Attendance (MemberID, CheckIn, CheckOut, AttendanceCount) VALUES (?, NOW(), '0000-00-00 00:00:00', 0)";
        $stmt = $conn1->prepare($insertSql);
        $stmt->bind_param("i", $memberID);

        if ($stmt->execute()) {
            echo 'checkedIn'; // Indicate successful check-in
        } else {
            echo 'error'; // Indicate an error
        }
    }
}
?>