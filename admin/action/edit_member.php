<?php
include '../../database/connection.php';

// Function to fetch member details
function fetchMemberDetails($memberID, $conn) {
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
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $memberID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Function to update member details
function updateMemberDetails($memberID, $username, $email, $gender, $age, $address, $membershipStatus, $conn) {
    $sql = "UPDATE Members SET 
            Username = ?, 
            Email = ?, 
            Gender = ?, 
            Age = ?, 
            Address = ?, 
            MembershipStatus = ? 
            WHERE MemberID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisii", $username, $email, $gender, $age, $address, $membershipStatus, $memberID);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Check if it's a GET or POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle the update request
    if (isset($_POST['memberID'])) {
        // Get the form data
        $memberID = $_POST['memberID'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $gender = $_POST['gender'];
        $age = $_POST['age'];
        $address = $_POST['address'];
        $membershipStatus = $_POST['membershipStatus'];

        // Update member details
        if (updateMemberDetails($memberID, $username, $email, $gender, $age, $address, $membershipStatus, $conn)) {
            echo json_encode(['success' => 'Member updated successfully']);
        } else {
            echo json_encode(['error' => 'Failed to update member']);
        }
    } else {
        echo json_encode(['error' => 'Invalid request']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Handle the fetch request
    if (isset($_GET['memberID'])) {
        $memberID = intval($_GET['memberID']);
        
        // Fetch the member details
        $memberData = fetchMemberDetails($memberID, $conn);

        if ($memberData) {
            echo json_encode(['success' => $memberData]);
        } else {
            echo json_encode(['error' => 'Member not found.']);
        }
    } else {
        echo json_encode(['error' => 'Invalid request.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
?>