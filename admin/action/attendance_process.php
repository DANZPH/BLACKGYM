<?php
include '../../database/connection.php'; // Include database connection

if (isset($_POST['action']) && $_POST['action'] == 'toggleAttendance' && isset($_POST['memberID'])) {
    $memberID = $_POST['memberID'];

    // Debugging: output the memberID received
    echo "Received MemberID: " . $memberID . "<br>";

    // Check the current attendance status
    $checkSql = "SELECT * FROM Attendance WHERE MemberID = ? AND CheckOut = '0000-00-00 00:00:00' ORDER BY AttendanceDate DESC LIMIT 1";
    $stmt = $conn1->prepare($checkSql);
    $stmt->bind_param("i", $memberID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Debugging: output the SQL query results
    if ($result->num_rows > 0) {
        echo "Member is already checked in.<br>";
        $attendance = $result->fetch_assoc();
        
        // Check-out the member
        $updateSql = "UPDATE Attendance SET CheckOut = NOW(), AttendanceCount = AttendanceCount + 1 WHERE AttendanceID = ?";
        $stmt = $conn1->prepare($updateSql);
        $stmt->bind_param("i", $attendance['AttendanceID']);
        $stmt->execute();

        // Debugging: output the success message
        echo "Member checked out. Attendance count updated.<br>";
        echo 'checkedOut';
    } else {
        echo "Member is not checked in. Checking in now.<br>";
        
        // Insert new attendance record for member
        $insertSql = "INSERT INTO Attendance (MemberID, CheckIn, AttendanceCount) VALUES (?, NOW(), 1)";
        $stmt = $conn1->prepare($insertSql);
        $stmt->bind_param("i", $memberID);
        $stmt->execute();
        
        // Debugging: output the success message
        echo "New attendance record inserted.<br>";
        echo 'checkedIn';
    }
}
?>