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

  <style>
    /* Custom Styles for Dark Theme */
    body {
      background-color: #121212;
      color: #fff;
      font-family: 'Roboto', sans-serif;
      margin: 0;
      padding-top: 30px;
    }

    .navbar {
      background-color: #1a1a1a;
      padding: 20px 0;
position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 9999; /* Keep navbar above other content */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
      font-weight: bold;
      font-size: 2rem;
      color: #fff;
      letter-spacing: 2px;
      text-transform: uppercase;
    }

    .navbar-brand:hover {
      color: #f39c12;
    }

    /* Landing Section */
    .landing-section {
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      background: linear-gradient(135deg, #111, #333);
      padding: 0 20px;
      border-bottom: 5px solid #f39c12;
    }

    .landing-content {
      max-width: 700px;
      margin: 0 auto;
    }

    .landing-content h1 {
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 20px;
      letter-spacing: 1.5px;
    }

    .landing-content p {
      font-size: 1.1rem;
      margin-bottom: 40px;
      line-height: 1.6;
    }

    .btn-dark-custom {
      background-color: #444;
      color: #fff;
      border-color: #444;
      font-size: 1.1rem;
      padding: 12px 30px;
      margin: 10px;
      border-radius: 30px;
      transition: all 0.3s ease;
      width: 100%;
      max-width: 300px;
    }

    .btn-dark-custom:hover {
      background-color: #f39c12;
      border-color: #f39c12;
      color: #fff;
    }

    .btn-container {
      display: flex;
      flex-direction: column;
      gap: 20px;
      align-items: center;
    }

    /* Footer */
    .footer {
      text-align: center;
      padding: 20px 0;
      background-color: #222;
      color: #bbb;
      font-size: 1rem;
      margin-top: 50px;
    }

    .footer a {
      color: #f39c12;
      text-decoration: none;
    }

    .footer a:hover {
      text-decoration: underline;
    }

    /* SweetAlert2 */
    .swal2-popup {
      background-color: #333 !important;
      color: #fff !important;
    }

    .swal2-title {
      color: #f39c12 !important;
    }

    /* Custom 20% Done Header Styles */
    .done-header {
      border: 5px solid green;
      background-color: #333;
      color: #fff;
      font-size: 2rem;
      font-weight: bold;
      text-align: center;
      padding: 20px;
      margin: 20px auto;
      width: 60%;
      border-radius: 10px;
      letter-spacing: 1px;
    }
  </style>
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
    <?php include '../includes/footer.php'; ?>
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
