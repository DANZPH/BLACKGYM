<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/BLACKGYM/database/connection.php';

if (isset($_GET['StaffID'])) {
    $staffID = $_GET['StaffID'];
    
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
        
        // Delete from Staff table first (due to foreign key constraint)
        $deleteStaffSQL = "DELETE FROM Staff WHERE StaffID = ?";
        $deleteStaffStmt = $conn1->prepare($deleteStaffSQL);
        $deleteStaffStmt->bind_param("i", $staffID);
        
        if (!$deleteStaffStmt->execute()) {
            throw new Exception("Failed to delete staff record");
        }
        
        // Delete from Users table (this will cascade to other related tables)
        $deleteUserSQL = "DELETE FROM Users WHERE UserID = ?";
        $deleteUserStmt = $conn1->prepare($deleteUserSQL);
        $deleteUserStmt->bind_param("i", $userID);
        
        if (!$deleteUserStmt->execute()) {
            throw new Exception("Failed to delete user record");
        }
        
        // Commit transaction
        $conn1->commit();
        
        // Redirect back to staff page with success message
        header("Location: ../dashboard/view_staff.php?success=Staff deleted successfully");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction
        $conn1->rollback();
        
        // Redirect back with error message
        header("Location: ../dashboard/view_staff.php?error=" . urlencode($e->getMessage()));
        exit();
    }
    
} else {
    header("Location: ../dashboard/view_staff.php?error=No staff ID provided");
    exit();
}

$conn1->close();
?>