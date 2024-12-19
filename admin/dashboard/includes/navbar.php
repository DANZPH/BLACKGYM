<nav>
	<i class='bx bx-menu'></i>
	<a href="#" class="nav-link">            <?php echo date("l, F j, Y g:i A"); ?></a>
	<form action="#">
		<div class="form-input">
			<button type="submit" class="search-btn"><i class='bx bx-calendar'></i></button>
		</div>
	</form>
	<input type="checkbox" id="switch-mode" hidden>
	<label for="switch-mode" class="switch-mode"></label>
	<a href="#" class="notification">
		<i class='bx bxs-bell'></i>
		<span class="num">
		  <?php echo $pendingPayments; ?>
		</span>
	</a>
	<a href="#" class="profile">
		<img src="../../img/favicon-512x512.png">
	</a>
</nav>