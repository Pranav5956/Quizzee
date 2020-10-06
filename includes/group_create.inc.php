<?php
  session_start();
  require_once "db.inc.php";

  if (isset($_POST['create-group-submit'])) {
    if (!empty($_POST['group-name']) && !empty($_POST['group-desc'])) {
      $ugid = 'G'.md5(time());
      $gname = $_POST['group-name'];
      $gdesc = $_POST['group-desc'];
      $create_time = time();

      $groupCreateQuery = $conn->prepare("INSERT INTO groups(ugid, gname, gdesc, create_time, creator)
                                          VALUES(:ugid, :gname, :gdesc, :create_time, :creator);");
      $groupCreateQuery->execute(array(
        ":ugid" => $ugid,
        ":gname" => $gname,
        ":gdesc" => $gdesc,
        ":create_time" => $create_time,
        ":creator" => $_SESSION['USERID']
      ));
      $gid = $conn->lastInsertId();

      $userGroupCreateQuery = $conn->prepare("INSERT INTO user_group(gid, uid, is_admin)
                                              VALUES(:gid, :uid, 'Yes');");
      $userGroupCreateQuery->execute(array(
        ":gid" => $gid,
        ":uid" => $_SESSION['USERID']
      ));

      header("Location: ../my/dashboard");
    } else {
      header("Location: ../my/groups/create");
    }
  } else {
    header("Location: ../quizzee");
  }
?>
