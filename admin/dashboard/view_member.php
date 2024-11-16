<!-- Main Content -->
<div class="col-md-9 main-content">
    <h2 class="mb-4">Member List</h2>

    <!-- Card Container for the Table -->
    <div class="card">
        <div class="card-header">
            <h5>Members Information</h5>
        </div>
        <div class="card-body">
            <!-- Wrap table in a responsive div -->
            <div class="table-responsive">
                <table id="membersTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Member ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Address</th>
                            <th>Membership Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch all members and their information
                        $sql = "
                            SELECT 
                                Members.MemberID, 
                                Users.Username, 
                                Users.Email, 
                                Members.Gender, 
                                Members.Age, 
                                Members.Address, 
                                Members.MembershipStatus, 
                                Members.created_at 
                            FROM Members 
                            INNER JOIN Users ON Members.UserID = Users.UserID
                        ";
                        $result = $conn1->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$row['MemberID']}</td>
                                    <td>{$row['Username']}</td>
                                    <td>{$row['Email']}</td>
                                    <td>{$row['Gender']}</td>
                                    <td>{$row['Age']}</td>
                                    <td>{$row['Address']}</td>
                                    <td>{$row['MembershipStatus']}</td>
                                    <td>{$row['created_at']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>No members found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>