<?php
session_start();
if (!isset($_SESSION['MemberID'])) {
    header('Location: ../login.php');
    exit();
}
include '../../database/connection.php';

// Fetch the MemberID from session
$memberID = $_SESSION['MemberID'];

// Initialize error array
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from the form
    $newUsername = trim($_POST['username']);
    $newEmail = trim($_POST['email']);
    $newAddress = trim($_POST['address']);
    $newPassword = trim($_POST['password']); // optional password field
    
    // Validate inputs
    if (empty($newUsername) || empty($newEmail)) {
        $errors[] = "Username and Email are required.";
    }
    
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    if (!empty($newPassword) && strlen($newPassword) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    
    // If no errors, proceed to update
    if (empty($errors)) {
        // Prepare SQL query to update user profile
        $updateSql = "UPDATE Users 
                      JOIN Members ON Users.UserID = Members.UserID 
                      SET Users.Username = ?, Users.Email = ?, Members.Address = ?";
        
        $params = [$newUsername, $newEmail, $newAddress];
        $types = "sss";
        
        // Check if password is set for update
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateSql .= ", Users.Password = ?";
            $params[] = $hashedPassword;
            $types .= "s";
        }
        
        $updateSql .= " WHERE Members.MemberID = ?";
        $params[] = $memberID;
        $types .= "i";
        
        // Prepare and execute the update query
        $updateStmt = $conn1->prepare($updateSql);
        $updateStmt->bind_param($types, ...$params);

        if ($updateStmt->execute()) {
            // If update is successful, redirect back with success message
            $_SESSION['success'] = 'Profile updated successfully!';
            header('Location: ../dashboard/profile.php');
            exit();
        } else {
            // If update fails, add error
            $errors[] = "Failed to update profile. Please try again.";
        }

        $updateStmt->close();
    }
}

// If there are any errors, store them in session
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: ../profile/edit_profile.php');
    exit();
}
?>
