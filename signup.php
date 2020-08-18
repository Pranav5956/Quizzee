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

<style>
body{
  background-image: url("https://images.unsplash.com/photo-1434030216411-0b793f4b4173?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1050&q=80");
  background-repeat: no-repeat;
  background-size: cover;
}
h1{
  color: #f0ece9;
}
</style>

<div class="container my-5">
    <h1 style="text-align:left">Signup</h1>
    <div style="width: 30%;">
			<form action="includes/signup.inc.php" method="post">
				<div class="form-group">
			  <input class ="form-control" id="fname" type="text" name="first-name"
							 placeholder="First Name" maxlength="20"
							 title="Enter your First Name here"
							 value="<?php if (isset($_GET['fname']))
							 				{ echo urldecode(htmlentities($_GET['fname'])); } ?>"></div>
				<div class="form-group">
				<input class ="form-control" id="lname" type="text" name="last-name"
							 placeholder="Last Name" maxlength="20"
							 title="Enter your Last Name here"
							 value="<?php if (isset($_GET['lname']))
							 				{ echo urldecode(htmlentities($_GET['lname'])); } ?>"></div>
				<div class="form-group">
				<input class ="form-control" id="email" type="text" name="email"
							 placeholder="Email" maxlength="50"
							 title="Enter your E-mail Address here"
							 value="<?php if (isset($_GET['email']))
							 				{ echo urldecode(htmlentities($_GET['email'])); } ?>"></div>
				<div class="form-group">
				<input class ="form-control" id="pwd" type="password" name="password"
							 placeholder="Password" maxlength="20"
							 title="Enter your Password here"></div>
				<div class="form-group">
			  <input class ="form-control" id="cpwd" type="password" name="confirm-password"
							 placeholder="Confirm Password" maxlength="20"
							 title="Re-enter your Passord here"></div>
			  <div class="form-group">
				<input class="form-control btn btn-success" type="submit" name="signup-submit" value="Signup"
							 title="Click to Signup"></div>
			</form>
			<a class="btn btn-outline-dark" href=<?php echo $google_client->CreateAuthUrl(); ?> role="button" style="text-transform:none">
			<img width="20px" style="margin-bottom:3px; margin-right:5px" alt="Google sign-in" src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png" />
			Signup with Google
		</a><br>
			<br><a href="login.php">Already have an account?</a>
		</div>
</div>
