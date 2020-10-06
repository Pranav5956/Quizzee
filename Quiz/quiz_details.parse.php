<?php
  session_start();
  require_once "../includes/db.inc.php";

  if (isset($_POST['uqid'])) {
    $selectQuizQuery = $conn->prepare("SELECT qname, type, code, create_time
                                       FROM quizzes
                                       WHERE uqid = :uqid");
    $selectQuizQuery->execute(array(
      ":uqid" => $_POST['uqid']
    ));
    $quizzes = $selectQuizQuery->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($quizzes);
  }
?>
