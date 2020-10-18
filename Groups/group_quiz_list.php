<?php
  if (isset($_GET['ugid'])) {
    require_once "../includes/db.inc.php";
    $selectQuizzes = $conn->prepare("SELECT uqid, qname FROM quizzes WHERE qid IN (
                                          SELECT qid FROM quiz_group WHERE gid IN (
                                            SELECT gid FROM groups WHERE ugid = :ugid
                                          ))");
    $selectQuizzes->execute(array(
      ":ugid" => $_GET['ugid']
    ));
    $quizzes = $selectQuizzes->fetchAll(PDO::FETCH_ASSOC);
      // echo '<div class="card float-left card-block d-flex quizTitle">';
      // echo '<a id="'.$quiz['uqid'].'-attempt-quiz-modal-trigger" class="card-body align-items-center d-flex justify-content-center modal-trigger"
      // data-modal="attempt-quiz" data-uqid='.$quiz['uqid'].'>'.$quiz['qname'].'</a></div>';
    echo json_encode($quizzes);
  } else {
    echo json_encode('');
    return;
  }
?>
