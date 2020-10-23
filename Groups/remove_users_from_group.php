<?php
  if (isset($_GET['ugid']) && isset($_GET['uuid'])) {
    require_once '../includes/db.inc.php';

    $groupQuery = $conn->prepare("SELECT gid FROM groups WHERE ugid=:ugid");
    $groupQuery->execute(array(
      ":ugid" => $_GET['ugid'],
    ));
    $gid = $groupQuery->fetch(PDO::FETCH_ASSOC)['gid'];

    $removeQuery = $conn->prepare("DELETE FROM user_group
                                   WHERE gid=:gid AND uid IN (
                                     SELECT uid FROM users WHERE uuid=:uuid
                                   )");
    $removeQuery->execute(array(
      ":gid" => $gid,
      ":uuid" => $_GET['uuid']
    ));
  }
  header("Location: ../my/dashboard");
?>
