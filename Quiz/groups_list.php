<?php
  session_start();
  require_once "../includes/db.inc.php";

  $selectGroupInfo = $conn->prepare("SELECT groups.ugid, groups.gname, user_group.is_admin
                                     FROM groups
                                     JOIN user_group
                                     ON groups.gid = user_group.gid
                                     WHERE user_group.uid = :uid AND user_group.is_admin = 'Yes'");
  $selectGroupInfo->execute(array(":uid" => $_SESSION['USERID']));
  $groups = $selectGroupInfo->fetchAll(PDO::FETCH_ASSOC);

  foreach ($groups as $index => $group) {
    echo "<option value=".$group['ugid'].">".$group['gname']." (".$group['ugid'].")</option>";
  }
?>
