<?php
  if (isset($_GET['ugid'])) {
    require_once "../includes/db.inc.php";
    $selectUsersQuery = $conn->prepare("SELECT users.uuid, users.fname, users.lname, user_group.is_admin
                                        FROM users JOIN user_group
                                        ON users.uid = user_group.uid
                                        WHERE user_group.gid IN (
                                          SELECT gid FROM groups WHERE ugid=:ugid
                                        )");
    $selectUsersQuery->execute(array(
      ":ugid" => $_GET['ugid']
    ));
    $users = $selectUsersQuery->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
  } else {
    echo '';
    return;
  }
?>
