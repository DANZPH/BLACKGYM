l<!-- Sidebar Toggle Button -->
<button id="toggleButton">☰</button>

<!-- Sidebar -->
<div class="sidebar">
    <div class="container">
        <!-- Admin Dashboard Links -->
        <ul class="list-unstyled">
            <li class="nav-item">
                <a class="nav-link collapsed" href="#manageMembers" data-toggle="collapse" aria-expanded="false">
                    <i class="fa fa-users"></i> Manage Members
                </a>
                <div id="manageMembers" class="collapse">
                    <ul class="list-unstyled pl-3">
                        <li><a href="view_members.php" class="nav-link"><i class="fa fa-eye"></i> View Members</a></li>
                        <li><a href="add_member.php" class="nav-link"><i class="fa fa-user-plus"></i> Add Member</a></li>
                        <li><a href="update_member.php" class="nav-link"><i class="fa fa-edit"></i> Update Members</a></li>
                    </ul>
                </div>
            </li>
            <!-- More links here... -->
        </ul>
    </div>
</div>

<!-- JavaScript for Toggling the Sidebar -->
<script>
    // Get the toggle button and sidebar
    const toggleButton = document.getElementById('toggleButton');
    const sidebar = document.querySelector('.sidebar');

    // Add click event listener to toggle button
    toggleButton.addEventListener('click', function() {
        sidebar.classList.toggle('active'); // Toggle the 'active' class
    });
</script>