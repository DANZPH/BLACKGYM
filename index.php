<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>BLACKGYM</title>
     <style>
         /* Basic reset and styling */
         body, h1, p {
             font-family: Arial, sans-serif;
             margin: 0;
             padding: 0;
             box-sizing: border-box;
         }

         /* Centering the content */
         .container {
             display: flex;
             justify-content: center;
             align-items: center;
             height: 100vh;
             background-color: #f4f4f4;
             text-align: center;
         }
     </style>
     <script>
         // Simple redirect without location requests
         window.onload = function() {
             // Redirect directly to admin login
             window.location.href = "admin/login.php";
         };
     </script>
</head>
<body>
     <!-- Main content container -->
     <div class="container">
         <h1>Redirecting to Login...</h1>
     </div>
</body>
</html>