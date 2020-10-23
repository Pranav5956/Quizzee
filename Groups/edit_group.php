<?php
  session_start();
  require_once "../includes/db.inc.php";

  if (isset($_POST['group-edit'])) {
    if (!empty($_POST['gname']) && !empty($_POST['gdesc'])) {
      $gname = $_POST['gname'];
      $gdesc = $_POST['gdesc'];

      $groupEditQuery = $conn->prepare("UPDATE groups SET gname=:gname, gdesc=:gdesc WHERE ugid=:ugid");
      $groupEditQuery->execute(array(
        ":gname" => $gname,
        ":gdesc" => $gdesc,
        ":ugid" => $_GET['ugid']
      ));
    }
  }
  header("Location: ../my/dashboard");
?>
