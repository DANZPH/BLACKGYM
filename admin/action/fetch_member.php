<?php
include '../../database/connection.php';

if (isset($_GET['memberID'])) {
    $memberID = $_GET['memberID'];
    
    $sql = "SELECT 
                Users.Username, 
                Users.Email, 
                Members.Gender, 
                Members.Age, 
                Members.Address, 
                Members.MembershipStatus 
            FROM Members 
            INNER JOIN Users ON Members.UserID = Users.UserID
            WHERE Members.MemberID = ?";
    
    $stmt = $conn1->prepare($sql);
    $stmt->bind_param("i", $memberID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode([]);
    }
}
?>
