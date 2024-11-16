<?php
include '../../database/connection.php'; // Include database connection

if (isset($_POST['action']) && $_POST['action'] == 'toggleAttendance' && isset($_POST['memberID'])) {
    $memberID = $_POST['memberID'];

    // Begin transaction to ensure atomicity
    $conn1->begin_transaction();

    try {
        // Check if the member already has an active attendance record
        $checkSql = "SELECT AttendanceID, CheckOut FROM Attendance WHERE MemberID = ? ORDER BY AttendanceID DESC LIMIT 1";
        $stmt = $conn1->prepare($checkSql);
        $stmt->bind_param("i", $memberID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $attendance = $result->fetch_assoc();
            $attendanceID = $attendance['AttendanceID'];
            $checkOut = $attendance['CheckOut'];

            if ($checkOut == '0000-00-00 00:00:00') {
                // Member is checked in, perform checkout and increment AttendanceCount
                $updateSql = "UPDATE Attendance SET CheckOut = NOW(), AttendanceCount = AttendanceCount + 1 WHERE AttendanceID = ?";
                $stmt = $conn1->prepare($updateSql);
                $stmt->bind_param("i", $attendanceID);

                if ($stmt->execute()) {
                    $conn1->commit();
                    echo 'checkedOut';
                    exit;
                } else {
                    throw new Exception("Error updating checkout.");
                }
            }
        }

        // If no active record, perform check-in
        $insertSql = "INSERT INTO Attendance (MemberID, CheckIn, CheckOut, AttendanceCount) VALUES (?, NOW(), '0000-00-00 00:00:00', 0)";
        $stmt = $conn1->prepare($insertSql);
        $stmt->bind_param("i", $memberID);

        if ($stmt->execute()) {
            $conn1->commit();
            echo 'checkedIn';
        } else {
            throw new Exception("Error inserting new attendance.");
        }
    } catch (Exception $e) {
        $conn1->rollback();
        echo 'error';
    }
}
?>