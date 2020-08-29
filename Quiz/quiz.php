<base href="/Quizzee/Quiz/">
<?php
  require_once "../header.php";

  if ($_GET['action'] == "create" || ($_GET['action'] == "edit" && isset($_GET['uqid']))) {
    require_once "quiz.create.php";
  } elseif ($_GET['action'] == "view" && isset($_GET['uqid'])) {
    require_once "quiz.view.php";
  } elseif ($_GET['action'] == "attend" && isset($_GET['uqid'])) {
    require_once "quiz.attend.php";
  } elseif ($_GET['action'] == "delete" && isset($_GET['uqid'])) {
    if (isset($_GET['uqid'])) {
      require_once '../includes/db.inc.php';
      $deleteOptionsQuery = $conn->prepare("DELETE FROM options WHERE qnid IN (
                                              SELECT qnid FROM questions WHERE qid IN (
                                                SELECT qid FROM quizzes WHERE uqid=:uqid
                                              )
                                            );");
      $deleteOptionsQuery->execute(array(
        ":uqid" => $_GET['uqid']
      ));

      $deleteQuestionsQuery = $conn->prepare("DELETE FROM questions WHERE qid IN (
                                                SELECT qid FROM quizzes WHERE uqid=:uqid
                                              );");
      $deleteQuestionsQuery->execute(array(
        ":uqid" => $_GET['uqid']
      ));

      $deleteQuizQuery = $conn->prepare("DELETE FROM quizzes WHERE uqid=:uqid;");
      $deleteQuizQuery->execute(array(
        ":uqid" => $_GET['uqid']
      ));
    }
    header("Location: ../../dashboard");
  }
?>
