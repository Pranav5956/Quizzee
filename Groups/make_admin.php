<?php
  if (isset($_GET['ugid']) && isset($_GET['uuid'])) {
    require_once '../includes/db.inc.php';

    $groupQuery = $conn->prepare("SELECT gid FROM groups WHERE ugid=:ugid");
    $groupQuery->execute(array(
      ":ugid" => $_GET['ugid'],
    ));
    $gid = $groupQuery->fetch(PDO::FETCH_ASSOC)['gid'];

    $removeQuery = $conn->prepare("UPDATE user_group
                                   SET is_admin='No'
                                   WHERE gid=:gid AND is_admin='Yes'");
    $removeQuery->execute(array(
      ":gid" => $gid
    ));

    $updateQuery = $conn->prepare("UPDATE user_group
                                   SET is_admin='Yes'
                                   WHERE gid=:gid AND uid IN (
                                     SELECT uid FROM users WHERE uuid=:uuid
                                   )");
    $updateQuery->execute(array(
      ":gid" => $gid,
      ":uuid" => $_GET['uuid']
    ));
  }
  header("Location: ../my/dashboard");
?>
