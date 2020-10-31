<?php
session_start();
  require_once 'db.inc.php';

  if (isset($_POST['token'])) {
    $selectUsersQuery = $conn->prepare("SELECT uuid, fname, lname
                                        FROM users");
    $selectUsersQuery->execute();
    $users = $selectUsersQuery->fetchAll(PDO::FETCH_ASSOC);

    $selectQuizzesQuery = $conn->prepare("SELECT uqid, qname, type
                                        FROM quizzes
                                        WHERE type <> 'G' AND uid <> :uid");
    $selectQuizzesQuery->execute([":uid" => $_SESSION['USERID']]);
    $quizzes = $selectQuizzesQuery->fetchAll(PDO::FETCH_ASSOC);

    $selectGroupsQuery = $conn->prepare("SELECT ugid, gname
                                        FROM groups");
    $selectGroupsQuery->execute();
    $groups = $selectGroupsQuery->fetchAll(PDO::FETCH_ASSOC);

    $response = array();
    foreach ($users as $key => $value) {
        $response[] = ["id" => $value['uuid'], "name" => $value['fname']." ".$value['lname']];
    }
    foreach ($quizzes as $key => $value) {
      $response[] = ["id" => $value['uqid'], "name" => $value['qname'], "type" => (($value['type'] == "O")? "Open":"Code Protected")];
    }
    foreach ($groups as $key => $value) {
      $response[] = ["id" => $value['ugid'], "name" => $value['gname']];
    }
    echo json_encode($response);
  } else {
    echo '';
  }
?>
