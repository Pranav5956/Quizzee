<?php
  if (isset($_POST)) {
    require_once '../includes/db.inc.php';

    $groupQuery = $conn->prepare("SELECT gid FROM groups WHERE ugid=:ugid");
    $groupQuery->execute(array(
      ":ugid" => $_GET['ugid'],
    ));
    $gid = $groupQuery->fetch(PDO::FETCH_ASSOC)['gid'];

    foreach ($_POST['users'] as $index => $uuid) {
      $userQuery = $conn->prepare("SELECT uid FROM users WHERE uuid=:uuid");
      $userQuery->execute(array(
        ":uuid" => $uuid,
      ));
      $uid = $userQuery->fetch(PDO::FETCH_ASSOC)['uid'];

      $userGroupCreateQuery = $conn->prepare("INSERT INTO user_group(gid, uid, is_admin)
                                              VALUES(:gid, :uid, 'No');");
      $userGroupCreateQuery->execute(array(
        ":gid" => $gid,
        ":uid" => $uid
      ));
    }
    header('Location: ../my/dashboard');
  } else {
    header('Location: ../my/dashboard');
  }
?>
