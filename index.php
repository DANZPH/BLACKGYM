<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BLACKGYM Landing Page</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin/dashboard/includes/styles.css">
  <style>
  
body {
  background: linear-gradient(90deg, #bdc3c7, #2c3e50);
  height: 100vh; /* Full height */
  margin: 0; /* Remove default margin */
  display: flex; /* Enable flexbox layout */
  flex-direction: column; /* Stack content vertically */
  justify-content: center; /* Center content vertically */
  align-items: center; /* Center content horizontally */
  font-family: 'Roboto', sans-serif;
  color: white;
  text-align: center; /* Ensure text is centered */
}

    h1 {
      font-size: 3rem;
      margin-bottom: 20px;
    }

    p {
      font-size: 1.2rem;
      margin-bottom: 40px;
    }

    .button-container {
      display: flex;
      gap: 20px;
    }

    .btn {
      background-color: #34495e;
      color: white;
      padding: 15px 30px;
      border: none;
      border-radius: 5px;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s ease;
      text-transform: uppercase;
    }

    .btn:hover {
      background-color: #2c3e50;
    }
  </style>
</head>
<body>
  <h1>Welcome to BLACKGYM</h1>
  <p>Select your role to proceed:</p>
  <div class="button-container">
    <button class="btn" onclick="location.href='admin-login.html'">Admin</button>
    <button class="btn" onclick="location.href='member-login.html'">Member</button>
    <button class="btn" onclick="location.href='staff-login.html'">Staff</button>
  </div>
                      <?php include 'includes/footer.php'; ?>
</body>
</html>