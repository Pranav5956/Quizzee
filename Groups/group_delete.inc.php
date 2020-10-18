<?php
  session_start();
  require_once "../includes/db.inc.php";

  if (isset($_POST['delete-group-submit'])) {
    if (isset($_GET['ugid'])) {
      $deleteUserGroupQuery = $conn->prepare("DELETE FROM user_group
                                              WHERE gid IN (
                                                SELECT gid
                                                FROM groups
                                                WHERE ugid = :ugid
                                              )");
      $deleteUserGroupQuery->execute(array(
        ":ugid" => $_GET['ugid']
      ));

      $deleteGroupsQuery = $conn->prepare("DELETE FROM groups
                                           WHERE ugid = :ugid");
      $deleteGroupsQuery->execute(array(
        ":ugid" => $_GET['ugid']
      ));

      header("Location: ../my/dashboard");
    } else {
      header("Location: ../my/dashboard");
    }
  } else {
    header("Location: ../../quizzee");
  }
?>
