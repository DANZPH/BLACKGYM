<!-- header.php -->
<?php
session_start();
?>

<header class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
      
          <button class="btn d-md-none" type="button" data-toggle="collapse" data-target="#sidebar" aria-expanded="false" aria-controls="sidebar">
        <span class="navbar-toggler-icon">↓</span>
    </button>
        <a class="navbar-brand" href="index.php">Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Hello, <?php echo $_SESSION['username']; ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../action/logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</header>