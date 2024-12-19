<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body">
                        <form id="registerForm">
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" id="username" name="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" id="password" name="password" class="form-control" required value="member" readonly>
                            </div>

                            <!-- Gender, Age, and Address Fields -->
                            <div class="form-group">
                                <label for="gender">Gender:</label>
                                <select id="gender" name="gender" class="form-control" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="age">Age:</label>
                                <input type="number" id="age" name="age" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Address:</label>
                                <input type="text" id="address" name="address" class="form-control" required>
                            </div>

                            <!-- Membership Option Fields -->
                            <div class="form-group">
                                <label for="membershipType">Choose Membership Type:</label>
                                <select id="membershipType" name="membershipType" class="form-control" required>
                                    <option value="SessionPrice">Pay Per Session</option>
                                    <option value="Subscription">Subscription</option>
                                </select>
                            </div>

                            <div class="form-group" id="subscriptionOptions" style="display: none;">
                                <label for="subscriptionMonths">Choose Number of Months:</label>
                                <input type="number" id="subscriptionMonths" name="subscriptionMonths" class="form-control" min="1" max="12">
                            </div>

                            <div class="form-group" id="sessionPriceOptions" style="display: none;">
                                <label for="sessionPrice">Price per Session:</label>
                                <input type="number" id="sessionPrice" name="sessionPrice" class="form-control" value="50" min="0">
                            </div>

                            <div class="form-group">
                                <!-- Register button styled like the login button -->
                                <button type="submit" class="btn btn-primary btn-block">Register</button>
                            </div>
                        </form>
                    </div>
        </div>
    </div>
</div>
