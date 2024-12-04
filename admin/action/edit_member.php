<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberID = $_POST['memberID'];
    if ($memberID) {
        $query = "SELECT * FROM Members WHERE MemberID = ?";
        $stmt = $conn1->prepare($query);
        $stmt->bind_param("i", $memberID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            echo json_encode(['success' => $data]);
        } else {
            echo json_encode(['error' => 'Member not found']);
        }
    } else {
        echo json_encode(['error' => 'Invalid Member ID']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>