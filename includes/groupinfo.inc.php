<?php
  require_once 'db.inc.php';

  if (isset($_GET['ugid'])) {
    $response = array();
    $selectUsersQuery = $conn->prepare("SELECT groups.ugid, groups.gname, groups.gdesc, groups.create_time,
                                               CONCAT(users.fname, ' ', users.lname, ' (', users.uuid, ')') as creator
                                        FROM groups JOIN users
                                        ON groups.creator = users.uid
                                        WHERE groups.ugid = :ugid");
    $selectUsersQuery->execute([":ugid" => $_GET['ugid']]);
    $group = $selectUsersQuery->fetch(PDO::FETCH_ASSOC);

    $response["ugid"] = $group['ugid'];
    $response["gname"] = $group['gname'];
    $response["gdesc"] = $group['gdesc'];
    $response["create_time"] = $group['create_time'];
    $response["creator"] = $group['creator'];

    $selectUsersQuery = $conn->prepare("SELECT CONCAT(users.fname, ' ', users.lname, ' (', users.uuid, ')') as member, user_group.is_admin
                                        FROM user_group JOIN users
                                        ON user_group.uid = users.uid
                                        WHERE gid IN (
                                          SELECT gid FROM groups WHERE ugid=:ugid
                                        )");
    $selectUsersQuery->execute([":ugid" => $_GET['ugid']]);
    $group_members = $selectUsersQuery->fetchAll(PDO::FETCH_ASSOC);

    $response["members"] = $group_members;

    echo json_encode($response);
  } else {
    echo '';
  }
?>
