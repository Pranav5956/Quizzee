<?php
  if (isset($_GET['ugid'])) {
    require_once "../includes/db.inc.php";
    $selectGroupInfo = $conn->prepare("SELECT gname, gdesc, create_time
                                       FROM groups
                                       WHERE ugid = :ugid");
    $selectGroupInfo->execute(array(":ugid" => $_GET['ugid']));
    $group = $selectGroupInfo->fetch(PDO::FETCH_ASSOC);

    echo json_encode($group);
  }
?>
