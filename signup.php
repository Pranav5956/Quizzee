<?php
	require_once "header.php";
  include_once "vendor/config.php";

	if (isset($_SESSION['USERID'])) {
		header("Location: index.php");
		return;
	}

	// Display Flash Messages
	include_once "includes/utilities.inc.php";
	flash_message();
?>

<!-- Signup Form -->
<form action="includes/signup.inc.php" method="post">
  <input id="fname" type="text" name="first-name"
				 placeholder="First Name" maxlength="20"
				 title="Enter your First Name here"
				 value="<?php if (isset($_GET['fname']))
				 				{ echo urldecode(htmlentities($_GET['fname'])); } ?>">
  <input id="lname" type="text" name="last-name"
				 placeholder="Last Name" maxlength="20"
				 title="Enter your Last Name here"
				 value="<?php if (isset($_GET['lname']))
				 				{ echo urldecode(htmlentities($_GET['lname'])); } ?>">
  <input id="email" type="text" name="email"
				 placeholder="Email" maxlength="50"
				 title="Enter your E-mail Address here"
				 value="<?php if (isset($_GET['email']))
				 				{ echo urldecode(htmlentities($_GET['email'])); } ?>">
  <input id="pwd" type="password" name="password"
				 placeholder="Password" maxlength="20"
				 title="Enter your Password here">
  <input id="cpwd" type="password" name="confirm-password"
				 placeholder="Confirm Password" maxlength="20"
				 title="Re-enter your Passord here"><br>
  <input type="submit" name="signup-submit" value="Signup"
				 title="Click to Signup">
</form><br>
OR<br>
<a href=<?php echo $google_client->CreateAuthUrl(); ?>>
  <img src="https://i.stack.imgur.com/FQMtO.png" alt="Log in with Google">
</a><br>

<a href="login.php">Already have an account?</a>
