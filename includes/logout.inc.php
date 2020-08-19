<?php
  if (isset($_POST['logout-submit'])) {
    // Logout from google account if signed in
    require_once "../vendor/config.php";
    google_logout();

    // Clean the session storage
    session_start();

    // Remove the cookies if they exist
    setcookie("USERID", '', time()-1, "/");
    session_destroy();
  }
  header("Location: /OnlineQuizManagement/Quizee");
?>
