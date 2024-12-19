<!-- Manage Attendance Modal -->
<div class="modal fade" id="manageAttendanceModal" tabindex="-1" role="dialog" aria-labelledby="manageAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageAttendanceModalLabel">Manage Attendance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="attendanceDate">Date</label>
                    <input type="date" id="attendanceDate" class="form-control">
                </div>
                <div class="form-group">
                    <label for="attendanceStatus">Status</label>
                    <select id="attendanceStatus" class="form-control">
                        <option value="Present">Present</option>
                        <option value="Absent">Absent</option>
                        <option value="Late">Late</option>
                        <option value="On Leave">On Leave</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="saveAttendanceBtn" class="btn btn-primary">Save Attendance</button>
            </div>
        </div>
    </div>
</div>