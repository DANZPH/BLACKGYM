<?php
include '../../database/connection.php';

// Check if MemberID is passed
if (isset($_GET['memberID'])) {
    $memberID = $_GET['memberID'];

    // Prepare the query to fetch the member's details
    $sql = "SELECT Members.MemberID, Users.Username, Users.Email, Members.Gender, 
                   Members.Age, Members.Address, Members.MembershipStatus, Members.Balance
            FROM Members 
            INNER JOIN Users ON Members.UserID = Users.UserID 
            WHERE Members.MemberID = ?";
    
    if ($stmt = $conn1->prepare($sql)) {
        $stmt->bind_param("i", $memberID);  // Bind MemberID as an integer
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();  // Fetch the member data
            echo json_encode($data);  // Return the data as a JSON response
        } else {
            echo json_encode(["error" => "Member not found"]);  // Return an error message if no member found
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => "Failed to prepare the SQL query"]);  // Handle database query preparation failure
    }
} else {
    echo json_encode(["error" => "MemberID not provided"]);  // Handle missing MemberID
}

$conn1->close();
?>
