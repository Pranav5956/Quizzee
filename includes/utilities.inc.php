<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<?php
  // Display Flash Messages
  function flash_message() {
    if (isset($_SESSION['error'])) {
      echo '<p style="color: red" class="alert alert-danger">'.
              $_SESSION['error'].
           '</p>';
    	unset($_SESSION['error']);
    } elseif (isset($_SESSION['success'])) {
    	echo '<p style="color: green" class="alert alert-success">'.
              $_SESSION['success'].
           '</p>';
    	unset($_SESSION['success']);
    }
  }
?>
