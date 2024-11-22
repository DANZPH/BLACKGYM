<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BLACKGYM - Landing Page</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(90deg, #bdc3c7, #2c3e50);
      height: 100%;
      margin: 0;
      display: flex;
      flex-direction: column;
      font-family: 'Roboto', sans-serif;
      color: #ffffff;
    }

    header {
      background-color: rgba(44, 62, 80, 0.9);
      padding: 20px;
      text-align: center;
      box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.5);
    }

    header h1 {
      font-size: 2rem;
      letter-spacing: 1px;
    }

    nav {
      display: flex;
      justify-content: center;
      padding: 10px 0;
    }

    nav a {
      text-decoration: none;
      color: #ffffff;
      margin: 0 15px;
      font-size: 1rem;
      transition: color 0.3s ease;
    }

    nav a:hover {
      color: #bdc3c7;
    }

    .container {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .buttons {
      text-align: center;
    }

    .buttons button {
      background-color: #34495e;
      border: none;
      padding: 15px 30px;
      margin: 10px;
      color: #ffffff;
      font-size: 1rem;
      cursor: pointer;
      border-radius: 5px;
      transition: background-color 0.3s ease;
    }

    .buttons button:hover {
      background-color: #2c3e50;
    }

    footer {
      background-color: rgba(44, 62, 80, 0.9);
      padding: 10px;
      text-align: center;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>
  <header>
    <h1>Welcome to BLACKGYM</h1>
    <nav>
      <a href="#admin">Admin</a>
      <a href="#member">Member</a>
      <a href="#staff">Staff</a>
    </nav>
  </header>
  <div class="container">
    <div class="buttons">
      <button onclick="location.href='#admin'">Admin</button>
      <button onclick="location.href='#member'">Member</button>
      <button onclick="location.href='#staff'">Staff</button>
    </div>
  </div>
  <footer>
    © 2024 BLACKGYM. All Rights Reserved.
  </footer>
</body>
</html>