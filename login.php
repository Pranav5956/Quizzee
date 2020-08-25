<?php
  require_once "header.php";
  include_once "vendor/config.php";
  if (isset($_SESSION['USERID'])) {
    header("Location: my/dashboard");
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
h1,label{
  color: #f0ece9;
}
</style>

<div class="container my-5">
    <h1 style="text-align:left">Login</h1>
    <div style="width: 30%;">
      <form action="includes/login.inc.php" method="post">
        <div class="form-group"><input class="form-control" type="text" name="email" placeholder="Email" title="Enter your E-mail Address here" class="form-control"></div>
        <div class="form-group"><input class="form-control" type="password" name="password" placeholder="Password" title="Enter your Password here" class="form-control"></div>
        <div class="form-group"><input class="form-control btn btn-primary" type="submit" name="login-submit" value="Login" title="Click to Login"></div>
        <div class="form-check">
          <input type="checkbox" id="remember-me" name="remember-me" checked="true" class="form-check-input">
          <label for="remember-me" class="form-check-label">Remember Me</label>
        </div>
      </form>
      <br>
      <a class="btn btn-outline-dark" href=<?php echo $google_client->CreateAuthUrl(); ?> role="button" style="text-transform:none">
      <img width="20px" style="margin-bottom:3px; margin-right:5px" alt="Google sign-in" src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png" />
      Login with Google
      </a>
      <br><br>
      <a href="signup">Don't have an account?</a>
  </div>
</div>

<!-- Alternate google login button -->
<!-- <a href=><img src="https://i.stack.imgur.com/FQMtO.png" width="100%" alt="Log in with Google"></a> -->
