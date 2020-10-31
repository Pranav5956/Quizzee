<?php
  require_once 'db.inc.php';
  session_start();

  if (isset($_GET['uqid'])) {
    $selectQuizQuery = $conn->prepare("SELECT quizzes.uqid, quizzes.qname, quizzes.type, quizzes.create_time,
                                        users.fname, users.lname, users.uuid
                                        FROM quizzes JOIN users
                                        ON quizzes.uid = users.uid
                                        WHERE uqid = :uqid");
    $selectQuizQuery->execute([":uqid" => $_GET['uqid']]);
    $quiz = $selectQuizQuery->fetch(PDO::FETCH_ASSOC);

    echo json_encode($quiz);
  } else {
    echo '';
  }
?>
