<?php
  session_start();
  require_once 'db.inc.php';

  if (isset($_GET['ugid'])) {
    $selectUsersQuery = $conn->prepare("SELECT uuid, fname, lname
                                        FROM users
                                        WHERE uid NOT IN (
                                          SELECT uid
                                          FROM user_group
                                          WHERE gid IN (
                                            SELECT gid FROM groups WHERE ugid=:ugid
                                          )
                                        )");
    $selectUsersQuery->execute(array(
      ":ugid" => $_GET['ugid']
    ));

    $users = $selectUsersQuery->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $index => $user) {
      echo "<option value=".$user['uuid'].">".$user['fname']." ".$user['lname']."<small> ".$user['uuid']."</small></option>";
    }
  } else {
    echo '';
  }
?>
