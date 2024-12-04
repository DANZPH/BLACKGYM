<?php
include '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberID = $_POST['memberID'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $address = $_POST['address'];
    $membershipStatus = $_POST['membershipStatus'];

    // Update the member in the database
    $sql = "UPDATE Members 
            INNER JOIN Users ON Members.UserID = Users.UserID
            SET Users.Username = ?, Users.Email = ?, Members.Gender = ?, Members.Age = ?, Members.Address = ?, Members.MembershipStatus = ?
            WHERE Members.MemberID = ?";

    $stmt = $conn1->prepare($sql);
    $stmt->bind_param("ssssssi", $username, $email, $gender, $age, $address, $membershipStatus, $memberID);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn1->close();
}
?>
