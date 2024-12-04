<?php
include '../../connection.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $memberID = $_POST['MemberID'];

    $sql = "SELECT Members.*, Users.Username, Users.Email 
            FROM Members 
            INNER JOIN Users ON Members.UserID = Users.UserID 
            WHERE Members.MemberID = ?";
    $stmt = $conn1->prepare($sql);
    $stmt->bind_param("i", $memberID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Member not found"]);
    }
}
?>