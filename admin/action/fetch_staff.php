<?php
include $_SERVER['DOCUMENT_ROOT'] . '/BLACKGYM/database/connection.php';

if (isset($_GET['staffID'])) {
    $staffID = $_GET['staffID'];
    
    $sql = "SELECT Staff.StaffID, Users.Username, Users.Email, Staff.JobTitle 
            FROM Staff 
            INNER JOIN Users ON Staff.UserID = Users.UserID 
            WHERE Staff.StaffID = ?";
    
    $stmt = $conn1->prepare($sql);
    $stmt->bind_param("i", $staffID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $staff = $result->fetch_assoc();
        echo json_encode($staff);
    } else {
        echo json_encode(['error' => 'Staff not found']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'No staff ID provided']);
}

$conn1->close();
?>