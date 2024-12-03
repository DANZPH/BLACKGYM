<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body">
                <form id="registerForm">
                    <!-- Username -->
                    <div class="form-group">
                        <label for="username">Full name</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Optional">
                    </div>
                    <!-- Password -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <!-- Gender -->
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" class="form-control">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <!-- Age -->
                    <div class="form-group">
                        <label for="age">Age</label>
                        <input type="number" id="age" name="age" class="form-control" required>
                    </div>
                    <!-- Address -->
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" class="form-control" rows="2"></textarea>
                    </div>
                    <!-- Membership Type -->
                    <div class="form-group">
                        <label for="membershipType">Membership Type</label>
                        <select id="membershipType" name="membershipType" class="form-control" required>
                            <option value="SessionPrice">Pay Per Session</option>
                            <option value="Subscription">Subscription</option>
                        </select>
                    </div>
                    <!-- Subscription Options -->
                    <div class="form-group" id="subscriptionOptions" style="display: none;">
                        <label for="subscriptionMonths">Number of Months</label>
                        <input type="number" id="subscriptionMonths" name="subscriptionMonths" class="form-control" min="1" max="12">
                    </div>
                    <!-- Session Price -->
                    <div class="form-group" id="sessionPriceOptions" style="display: none;">
                        <label for="sessionPrice">Price per Session</label>
                        <input type="number" id="sessionPrice" name="sessionPrice" class="form-control" value="50" min="0">
                    </div>
                    <!-- Submit Button -->
                    <div class="form-group">
                        <button type="submit" class="btn btn-success w-100">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>