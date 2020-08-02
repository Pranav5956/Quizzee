<?php
  session_start();
  include "db.inc.php";

  if (isset($_POST['login-submit'])) {
    // Login processing
    if (empty($_POST['email']) || empty($_POST['password'])) {
      $_SESSION['error'] = "Fill all fields";
      header("Location: ../login.php");
      return;
    } else {
      $login_query = $conn->prepare("SELECT * FROM Users
                                     WHERE email=:email AND pwd=:pwd AND login=:login");
      $login_query->execute(array(
        ':email' => $_POST['email'],
        ':pwd' => hash('sha256', $salt.$_POST['password']),
        ':login' => 'LOGIN'
      ));
      $result = $login_query->fetch(PDO::FETCH_ASSOC);

      if ($result == false) {
        $_SESSION['error'] = "Wrong email or password";
        header("Location: ../login.php");
        return;
      } else {
        // Create a cookie for the logged in user if he wants to be remembered
        if ($_POST['remember-me']) {
          setcookie("UserInfo[userId]", $result['uid'], time() + (30*86400), "/");
          setcookie("UserInfo[name]", $result['fname']." ".$result['lname'], time() + (30*86400), "/");
        }

        // Store the user in the session
        $_SESSION['userId'] = $result['uid'];
        $_SESSION['name'] = $result['fname']." ".$result['lname'];
        $_SESSION['success'] = "Successfully Logged in!";
        header("Location: ../index.php");
        return;
      }
    }
  } elseif (isset($_GET['code'])) {
    // Google authentication
    require_once "../vendor/config.php";
    google_login($conn);
  } else {
    $_SESSION['error'] = "Please Log in to continue";
    header("Location: ../index.php");
    return;
  }
?>
