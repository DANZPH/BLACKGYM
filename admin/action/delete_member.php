<?php
include '../../database/connection.php';

if (isset($_POST['memberID'])) {
    $memberID = $_POST['memberID'];

    $sql = "DELETE FROM Members WHERE MemberID = ?";
    $stmt = $conn1->prepare($sql);
    $stmt->bind_param("i", $memberID);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => 'Member deleted successfully']);
    } else {
        echo json_encode(['error' => 'Failed to delete member']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
