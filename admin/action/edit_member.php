<?php
include '../../database/connection.php';

if (isset($_POST['memberID'])) {
    $memberID = intval($_POST['memberID']);

    $sql = "
        SELECT 
            Members.MemberID, 
            Users.Username, 
            Users.Email, 
            Members.Gender, 
            Members.Age, 
            Members.Address, 
            Members.MembershipStatus 
        FROM Members 
        INNER JOIN Users ON Members.UserID = Users.UserID
        WHERE Members.MemberID = ?
    ";
    $stmt = $conn1->prepare($sql);
    $stmt->bind_param("i", $memberID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $memberData = $result->fetch_assoc();
        echo json_encode(['success' => $memberData]);
    } else {
        echo json_encode(['error' => 'Member not found.']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request.']);
}
?>