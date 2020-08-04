<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<?php
  // Display Flash Messages
  function flash_message() {
    if (isset($_SESSION['ERROR'])) {
      echo '<p style="color: red" class="alert alert-danger">'.
              $_SESSION['ERROR'].
           '</p>';
    	unset($_SESSION['ERROR']);
    } elseif (isset($_SESSION['SUCCESS'])) {
    	echo '<p style="color: green" class="alert alert-success">'.
              $_SESSION['SUCCESS'].
           '</p>';
    	unset($_SESSION['SUCCESS']);
    }
  }
?>
