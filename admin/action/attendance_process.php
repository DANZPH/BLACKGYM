<?php
include '../../database/connection.php'; // Include database connection

if (isset($_POST['action']) && $_POST['action'] == 'toggleAttendance' && isset($_POST['memberID'])) {
    $memberID = $_POST['memberID'];

    // Check for an existing attendance record for the member
    $checkSql = "SELECT AttendanceID, CheckOut FROM Attendance WHERE MemberID = ? ORDER BY AttendanceID DESC LIMIT 1";
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
            // Member is currently checked in; perform a checkout
            $updateSql = "UPDATE Attendance 
                          SET CheckOut = NOW() 
                          WHERE AttendanceID = ?";
            $stmt = $conn1->prepare($updateSql);
            $stmt->bind_param("i", $attendance['AttendanceID']);

            if ($stmt->execute()) {
                echo 'checkedOut'; // Success: Member checked out
            } else {
                echo "Error: " . $conn1->error;
            }
        } else {
            // Member is checked out; perform a new check-in and increment AttendanceCount
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
        // No existing record, create a new one with AttendanceCount = 1
        $insertSql = "INSERT INTO Attendance (MemberID, CheckIn, CheckOut, AttendanceCount) 
                      VALUES (?, NOW(), '0000-00-00 00:00:00', 1)";
        $stmt = $conn1->prepare($insertSql);
        $stmt->bind_param("i", $memberID);

        if ($stmt->execute()) {
            echo 'checkedIn'; // Success: Member checked in with initial count
        } else {
            echo "Error: " . $conn1->error;
        }
    }
}
?>
