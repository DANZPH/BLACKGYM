<?php
include '../../database/connection.php';

if (isset($_GET['MemberID'])) {
    $memberID = $_GET['MemberID'];

    // Start a transaction to delete from both tables
    $conn1->begin_transaction();

    try {
        // Delete from Members table
        $deleteMemberQuery = "DELETE FROM Members WHERE MemberID = ?";
        $stmtMember = $conn1->prepare($deleteMemberQuery);
        $stmtMember->bind_param("i", $memberID);
        $stmtMember->execute();

        // Delete from Users table (associated with the MemberID)
        $deleteUserQuery = "DELETE FROM Users WHERE UserID = (SELECT UserID FROM Members WHERE MemberID = ?)";
        $stmtUser = $conn1->prepare($deleteUserQuery);
        $stmtUser->bind_param("i", $memberID);
        $stmtUser->execute();

        // Commit the transaction
        $conn1->commit();

        // Redirect to the member list page
        header('Location: ../dashboard/view_member.php');
        exit();
    } catch (Exception $e) {
        // If there's an error, roll back the transaction
        $conn1->rollback();
        echo "Error deleting record: " . $e->getMessage();
    }
}
?>
