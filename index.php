<?php
	require_once "header.php";
	include_once "includes/utilities.inc.php";

	// Display Flash Messages
	flash_message();

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

<!-- Login information -->
<?php if (isset($_SESSION['USERID'])): ?>
	<?php if (isset($_SESSION['PROFILE-PICTURE'])): ?>
		<img src=<?php echo htmlentities($_SESSION['PROFILE-PICTURE']) ?> alt="Profile Image" class="rounded-circle"
				 style="width: 10%; height: auto;">
	<?php else: ?>
		<img src="https://www.gstatic.com/images/branding/product/2x/avatar_square_blue_120dp.png" alt="No Profile Image" class="rounded-circle">
	<?php endif; ?>
	<p>
		Welcome, <?php echo htmlentities($_SESSION['NAME'], ENT_QUOTES, 'utf-8'); ?>!
	</p>
<?php else: ?>
	<p>You are not logged in</p>
<?php endif; ?>

<!-- Display the Login form and signup button if user is not logged in -->
<?php if (isset($_SESSION['USERID'])): ?>
	<!-- Logout Button -->
	<form>
		<input type="submit" name="logout-submit" value="Logout"
					 title="Click to Logout" class="btn btn-primary" formaction="includes/logout.inc.php" formmethod="post">
		<?php if ($_SESSION['TYPE'] == 'LOGIN'): ?>
			<input type="submit" name="action" value="Upload Profile Picture"
				 		 title="Click to upload Profile Picture" class="btn btn-primary" formaction="change.php" formmethod="get">
		<?php endif; ?>
	</form>
<?php else: ?>
	<!-- Login and Signup Links -->
	<a href="login.php"> Login </a><br>
	<a href="signup.php"> Signup </a>
<?php endif; ?>
