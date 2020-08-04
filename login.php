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

<!-- Login Form -->
<form action="includes/login.inc.php" method="post">
  <input type="text" name="email" placeholder="Email"
         title="Enter your E-mail Address here" class="form-control">
  <input type="password" name="password" placeholder="Password"
         title="Enter your Password here" class="form-control"><br>
  <div class="form-check">
    <input type="checkbox" id="remember-me" name="remember-me" checked="true" class="form-check-input">
    <label for="remember-me" class="form-check-label">Remember Me?</label><br>
  </div>

  <input type="submit" name="login-submit" value="Login"
         title="Click to Login" class="btn btn-primary">
</form><br>
OR<br>
<a href=<?php echo $google_client->CreateAuthUrl(); ?>>
  <img src="https://img.icons8.com/color/16/000000/google-logo.png" alt="Log in with Google">Log In with Google
</a><br>

<a href="signup.php">Don't have an account?</a>
