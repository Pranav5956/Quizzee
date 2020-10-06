<?php
  require_once "../header.php";

  if (!empty($_GET['action']) && isset($_SESSION['USERID'])) {
    if ($_GET['action'] == "create") {
      require_once "group_create.php";
    } else {
      header("Location: ../../quizzee");
    }
  } else {
    header("Location: ../../quizzee");
  }
?>
