<?php
	require_once "header.php";
	include_once "includes/utilities.inc.php";

	// Display Flash Messages
	//flash_message();

	// Retrieve cookie information
	if (isset($_COOKIE['USERID'])) {
		require_once "includes/db.inc.php";

		$_SESSION['USERID'] = $_COOKIE['USERID'];

		$login_query = $conn->prepare("SELECT * FROM Users
																	 WHERE uid=:uid");
		$login_query->execute(array(
			':uid' => $_SESSION['USERID']
		));
		$result = $login_query->fetch(PDO::FETCH_ASSOC);

		$_SESSION['NAME'] = $result['fname'].' '.$result['lname'];
		$_SESSION['PROFILE-PICTURE'] = $result['profile_pic'];
		$_SESSION['TYPE'] = $result['login'];
	}
?>



<style>
.dropdown-menu li{
	margin: 10px;
}
img{
	margin:10px;
}
</style>



<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	<a class="navbar-brand" href="index.php">Quizzee</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav ml-auto">
			<?php if (isset($_SESSION['USERID'])): ?>
				<div class="dropdown">
				  <button class="btn btn-dark dropdown-toggle" type="button" data-toggle="dropdown">Signed In as <?php echo htmlentities($_SESSION['NAME'], ENT_QUOTES, 'utf-8'); ?>
				  <span class="caret"></span></button>
				  <ul class="dropdown-menu bg-dark">
						<?php if (isset($_SESSION['PROFILE-PICTURE'])): ?>
							<img src=<?php echo htmlentities($_SESSION['PROFILE-PICTURE']) ?> alt="Profile Image" class="rounded-circle img-fluid" style="width: 110px; height: 110px;">
						<?php else: ?>
							<img src="https://www.gstatic.com/images/branding/product/2x/avatar_square_blue_120dp.png" alt="No Profile Image" class="rounded-circle" style="width:110px;height:110px;">
						<?php endif; ?>
						<form>
							<?php if ($_SESSION['TYPE'] == 'LOGIN'): ?>
								<li><input type="submit" name="action" value="Change Profile Picture"
											 title="Click to upload Profile Picture" class="btn btn-dark" formaction="change.php" formmethod="get"></li>
							<?php endif; ?>
							<li><input type="submit" name="logout-submit" value="Logout"
										 title="Click to Logout" class="btn btn-dark" formaction="includes/logout.inc.php" formmethod="post"></li>
						</form>
				  </ul>
				</div>

			<?php else: ?>
				<a class="nav-link btn btn-dark" href="login.php"> Login </a><br>
				<a class="nav-link btn btn-dark" href="signup.php"> Signup </a>
			<?php endif; ?>
		</ul>
	</div>
</nav>
