<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BLACKGYM Landing Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(90deg, #bdc3c7, #2c3e50);
      height: 100%;
      margin: 0;
      display: flex;
      flex-direction: column;
      font-family: 'Roboto', sans-serif;
    }
    .navbar {
      background-color: #2c3e50;
    }
    .navbar-brand, .nav-link {
      color: #ffffff !important;
    }
    .btn-custom {
      margin: 10px;
      background-color: #34495e;
      color: #ffffff;
      border: none;
    }
    .btn-custom:hover {
      background-color: #1abc9c;
      color: #ffffff;
    }
    footer {
      background-color: #2c3e50;
      color: white;
      text-align: center;
      padding: 15px;
      position: fixed;
      width: 100%;
      bottom: 0;
    }
    .main-content {
      flex-grow: 1;
      display: flex;
      align-items: center;
      justify-content: center;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="#">BLACKGYM</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="#admin">Admin</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#member">Member</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#staff">Staff</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="main-content container text-center">
    <div>
      <h1 class="text-white mb-4">Welcome to BLACKGYM</h1>
      <div class="d-flex justify-content-center">
        <button id="admin" class="btn btn-custom">Admin</button>
        <button id="member" class="btn btn-custom">Member</button>
        <button id="staff" class="btn btn-custom">Staff</button>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <p>&copy; 2024 BLACKGYM. All rights reserved.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>