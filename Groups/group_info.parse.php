<?php
  if (isset($_GET['ugid'])) {
    session_start();
    require_once "../includes/db.inc.php";
    $selectGroupInfo = $conn->prepare("SELECT groups.ugid, groups.gname, groups.gdesc, groups.create_time, user_group.is_admin
                                       FROM groups
                                       JOIN user_group
                                       ON groups.gid = user_group.gid
                                       WHERE groups.ugid = :ugid AND user_group.uid = :uid");
    $selectGroupInfo->execute(array(":ugid" => $_GET['ugid'], ":uid" => $_SESSION['USERID']));
    $group = $selectGroupInfo->fetch(PDO::FETCH_ASSOC);

    echo json_encode($group);
  }
?>
