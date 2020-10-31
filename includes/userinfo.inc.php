<?php
  require_once 'db.inc.php';

  if (isset($_GET['uuid'])) {
    $selectUsersQuery = $conn->prepare("SELECT uuid, fname, lname, email, login, profile_pic
                                        FROM users
                                        WHERE uuid = :uuid");
    $selectUsersQuery->execute([":uuid" => $_GET['uuid']]);
    $user = $selectUsersQuery->fetch(PDO::FETCH_ASSOC);

    echo json_encode($user);
  } else {
    echo '';
  }
?>
