<?php
include '../../database/connection.php'; // Include database connection

if (isset($_POST['action']) && $_POST['action'] == 'toggleAttendance' && isset($_POST['memberID'])) {
    $memberID = $_POST['memberID'];

    // Check if the member has an active attendance record
    $checkSql = "SELECT AttendanceID, CheckOut, AttendanceCount FROM Attendance WHERE MemberID = ? ORDER BY AttendanceID DESC LIMIT 1";
    $stmt = $conn1->prepare($checkSql);
    $stmt->bind_param("i", $memberID);

    if (!$stmt->execute()) {
        echo "Error: " . $conn1->error;
        exit();
    }

    $result = $stmt->get_result();
    $attendance = $result->fetch_assoc();

    if ($attendance) {
        if ($attendance['CheckOut'] == '0000-00-00 00:00:00') {
            // Member is already checked in, no further action
            echo 'alreadyCheckedIn';
        } else {
            // Member is checked out, update to check-in and increment AttendanceCount
            $updateSql = "UPDATE Attendance 
                          SET CheckIn = NOW(), 
                              CheckOut = '0000-00-00 00:00:00',
                              AttendanceCount = AttendanceCount + 1 
                          WHERE AttendanceID = ?";
            $stmt = $conn1->prepare($updateSql);
            $stmt->bind_param("i", $attendance['AttendanceID']);

            if ($stmt->execute()) {
                echo 'checkedIn'; // Success: Member checked in
            } else {
                echo "Error: " . $conn1->error;
            }
        }
    } else {
        // No existing record, create a new one
        $insertSql = "INSERT INTO Attendance (MemberID, CheckIn, CheckOut, AttendanceCount) 
                      VALUES (?, NOW(), '0000-00-00 00:00:00', 1)";
        $stmt = $conn1->prepare($insertSql);
        $stmt->bind_param("i", $memberID);

        if ($stmt->execute()) {
            echo 'checkedIn'; // Success: Member checked in
        } else {
            echo "Error: " . $conn1->error;
        }
    }
}
?>