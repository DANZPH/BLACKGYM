<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BLACKGYM</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Fonts (Roboto) -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

  <!-- SweetAlert2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.8/dist/sweetalert2.min.css" rel="stylesheet">

  <!-- Font Awesome CDN (for GitHub Icon) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="admin/dashboard/includes/styles.css">
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="#">BLACKGYM</a>
    </div>
  </nav>

  <!-- Landing Section -->
  <div class="landing-section">
    <div class="landing-content">
      <h1>Welcome to BLACKGYM</h1>
      <p>Your fitness journey starts here. Choose your login option below to proceed.</p>

      <!-- 20% Done Header (Static Design) -->
      <div class="done-header">
        30% Done
      </div>

      <!-- Login Buttons -->
      <div class="btn-container">
        <a href="javascript:void(0)" class="btn btn-dark-custom" onclick="showAlert('admin')">Admin Login</a>
        <a href="javascript:void(0)" class="btn btn-dark-custom" onclick="showAlert('member')">Member Login</a>
        <a href="javascript:void(0)" class="btn btn-dark-custom" onclick="showAlert('staff')">Staff Login</a>
      </div>
    </div>
  </div>
    <?php include 'includes/footer.php'; ?>
  <!-- Footer -->


  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.8/dist/sweetalert2.min.js"></script>

  <script>
    // Function to show SweetAlert based on login type
    function showAlert(userType) {
      let title, text, redirectURL;

      if (userType === 'admin') {
        title = 'Admin Login';
        text = 'You are attempting to login as an Admin!';
        redirectURL = 'admin/login.php'; // Redirect URL for Admin
      } else if (userType === 'member') {
        title = 'Member Login';
        text = 'You are attempting to login as a Member!';
        redirectURL = 'member/login.php'; // Redirect URL for Member
      } else if (userType === 'staff') {
        title = 'Staff Login';
        text = 'You are attempting to login as Staff!';
        redirectURL = 'stafflogin.php'; // Redirect URL for Staff
      }

      // Show SweetAlert with dynamic content
      Swal.fire({
        icon: 'info',
        title: title,
        text: text,
        confirmButtonText: 'Ok',
        confirmButtonColor: '#f39c12',
        background: '#333',
      }).then((result) => {
        // Redirect to the appropriate login page when the 'Ok' button is clicked
        if (result.isConfirmed) {
          window.location.href = redirectURL;
        }
      });
    }
  </script>

</body>
</html>