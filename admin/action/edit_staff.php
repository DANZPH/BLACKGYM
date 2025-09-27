<?php
include $_SERVER['DOCUMENT_ROOT'] . '/BLACKGYM/database/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['StaffID'])) {
    $staffID = $_GET['StaffID'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $jobTitle = $_POST['jobTitle'];
    $password = $_POST['password'];
    
    // Start transaction
    $conn1->begin_transaction();
    
    try {
        // First, get the UserID for this staff member
        $getUserSQL = "SELECT UserID FROM Staff WHERE StaffID = ?";
        $getUserStmt = $conn1->prepare($getUserSQL);
        $getUserStmt->bind_param("i", $staffID);
        $getUserStmt->execute();
        $userResult = $getUserStmt->get_result();
        
        if ($userResult->num_rows == 0) {
            throw new Exception("Staff member not found");
        }
        
        $userRow = $userResult->fetch_assoc();
        $userID = $userRow['UserID'];
        
        // Update Users table
        if (!empty($password)) {
            // Update with new password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateUserSQL = "UPDATE Users SET Username = ?, Email = ?, Password = ? WHERE UserID = ?";
            $updateUserStmt = $conn1->prepare($updateUserSQL);
            $updateUserStmt->bind_param("sssi", $username, $email, $hashedPassword, $userID);
        } else {
            // Update without changing password
            $updateUserSQL = "UPDATE Users SET Username = ?, Email = ? WHERE UserID = ?";
            $updateUserStmt = $conn1->prepare($updateUserSQL);
            $updateUserStmt->bind_param("ssi", $username, $email, $userID);
        }
        
        if (!$updateUserStmt->execute()) {
            throw new Exception("Failed to update user information");
        }
        
        // Update Staff table
        $updateStaffSQL = "UPDATE Staff SET JobTitle = ? WHERE StaffID = ?";
        $updateStaffStmt = $conn1->prepare($updateStaffSQL);
        $updateStaffStmt->bind_param("si", $jobTitle, $staffID);
        
        if (!$updateStaffStmt->execute()) {
            throw new Exception("Failed to update staff information");
        }
        
        // Commit transaction
        $conn1->commit();
        echo "Staff updated successfully.";
        
    } catch (Exception $e) {
        // Rollback transaction
        $conn1->rollback();
        echo "Error: " . $e->getMessage();
    }
    
} else {
    echo "Invalid request.";
}

$conn1->close();
?>