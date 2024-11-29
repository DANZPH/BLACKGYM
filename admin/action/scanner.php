<?php
session_start();
include '../../database/connection.php';  // Include database connection

header('Content-Type: application/json');

// Check if the request method is POST and QR data is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiptNumber'])) {
    $receiptNumber = $_POST['receiptNumber'];

    // Query to find the member ID associated with the ReceiptNumber
    $member_query = $conn1->prepare("SELECT MemberID FROM Payments WHERE ReceiptNumber = ?");
    $member_query->bind_param("s", $receiptNumber);
    $member_query->execute();
    $member_result = $member_query->get_result();

    if ($member_result->num_rows > 0) {
        $member = $member_result->fetch_assoc();
        $memberID = $member['MemberID'];

        // Check the latest attendance record for the MemberID
        $attendance_query = $conn1->prepare("SELECT AttendanceID, CheckInTime, CheckOutTime FROM Attendance WHERE MemberID = ? ORDER BY CheckInTime DESC LIMIT 1");
        $attendance_query->bind_param("i", $memberID);
        $attendance_query->execute();
        $attendance_result = $attendance_query->get_result();

        if ($attendance_result->num_rows > 0) {
            $attendance = $attendance_result->fetch_assoc();

            // If the last record has a CheckOutTime NULL, perform check-out
            if (is_null($attendance['CheckOutTime'])) {
                $checkout_query = $conn1->prepare("UPDATE Attendance SET CheckOutTime = NOW() WHERE AttendanceID = ?");
                $checkout_query->bind_param("i", $attendance['AttendanceID']);
                $checkout_query->execute();

                echo json_encode([
                    "status" => "success",
                    "action" => "checkout",
                    "message" => "Successfully checked out!"
                ]);
                exit();
            }
        }

        // Perform check-in if no recent attendance or already checked out
        $checkin_query = $conn1->prepare("INSERT INTO Attendance (MemberID, CheckInTime) VALUES (?, NOW())");
        $checkin_query->bind_param("i", $memberID);
        $checkin_query->execute();

        echo json_encode([
            "status" => "success",
            "action" => "checkin",
            "message" => "Successfully checked in!"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid ReceiptNumber. No member found."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request."
    ]);
}
?>
