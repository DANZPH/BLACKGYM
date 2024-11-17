<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addPaymentForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPaymentModalLabel">Add Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="memberID">Select Member</label>
                        <select class="form-control" id="memberID" name="memberID" required>
                            <option value="" disabled selected>Choose a Member</option>
                            <?php
                            // Fetch members for dropdown
                            $sql = "
                                SELECT 
                                    Members.MemberID, 
                                    Users.Username 
                                FROM Members 
                                INNER JOIN Users ON Members.UserID = Users.UserID
                            ";
                            $result = $conn1->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['MemberID']}'>[{$row['MemberID']}] {$row['Username']}</option>";
                                }
                            } else {
                                echo "<option value='' disabled>No members found</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="paymentMethod">Payment Method</label>
                        <select class="form-control" id="paymentMethod" name="paymentMethod" required>
                            <option value="Cash">Cash</option>
                            <option value="Card">Card</option>
                            <option value="Online">Online</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>