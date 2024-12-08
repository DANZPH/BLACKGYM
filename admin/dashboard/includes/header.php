<nav class="navbar navbar-expand-lg fixed-top">
    <a class="navbar-brand" href="index.php">Hello Admin, <?php echo $_SESSION['username']; ?>!</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Display current date and time -->
    <div class="ml-auto text-white">
        <span class="navbar-text">
            <?php echo date("l, F j, Y g:i A"); ?>
        </span>
    </div>

    <script src="includes/JS/sweetalert.js"></script>
    <script src="includes/JS/script.js"></script>
    <!-- JS Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</nav>