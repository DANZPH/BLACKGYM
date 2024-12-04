<?php
include '../../database/connection.php';

if (isset($_POST['memberID'])) {
    $memberID = $_POST['memberID'];
    
    $sql = "SELECT * FROM Members WHERE MemberID = ?";
    $stmt = $conn1->prepare($sql);
    $stmt->bind_param("i", $memberID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['success' => $row]);
    } else {
        echo json_encode(['error' => 'Member not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
