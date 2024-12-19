<?php
include '../../database/connection.php';

if (isset($_GET['MemberID']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $memberID = $_GET['MemberID'];

    // Fetch data from the form
    $username = $_POST['editUsername'];
    $email = $_POST['editEmail'];
    $gender = $_POST['editGender'];
    $age = $_POST['editAge'];
    $address = $_POST['editAddress'];
    $membershipStatus = $_POST['editMembershipStatus'];

    // Start a transaction to update both Users and Members tables
    $conn1->begin_transaction();

    try {
        // Update Users table
        $updateUserQuery = "UPDATE Users 
                            SET Username = ?, Email = ? 
                            WHERE UserID = (
                                SELECT UserID 
                                FROM Members 
                                WHERE MemberID = ?
                            )";
        $stmtUser = $conn1->prepare($updateUserQuery);
        $stmtUser->bind_param("ssi", $username, $email, $memberID);
        $stmtUser->execute();

        // Update Members table
        $updateMemberQuery = "UPDATE Members 
                              SET Gender = ?, Age = ?, Address = ?, MembershipStatus = ? 
                              WHERE MemberID = ?";
        $stmtMember = $conn1->prepare($updateMemberQuery);
        $stmtMember->bind_param("sissi", $gender, $age, $address, $membershipStatus, $memberID);
        $stmtMember->execute();

        // Commit the transaction
        $conn1->commit();

        // Redirect to the member list page
        header('Location: ../dashboard/view_member.php');
        exit();
    } catch (Exception $e) {
        // If there's an error, roll back the transaction
        $conn1->rollback();
        echo "Error updating record: " . $e->getMessage();
    }
}
?>
