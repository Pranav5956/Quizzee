<base href="/Quizzee/Quiz/">
<?php
  require_once "../header.php";
  require_once '../includes/db.inc.php';

  if ($_GET['action'] != 'create') {
    if ($_GET['uqid'] != "") {
      $selectQuizQuery = $conn->prepare("SELECT uid
                                         FROM quizzes
                                         WHERE uqid = :uqid");
      $selectQuizQuery->execute(array(
        ":uqid" => urldecode($_GET['uqid'])
      ));
      $user_id = $selectQuizQuery->fetch(PDO::FETCH_ASSOC)['uid'];
    }
  }

  if ($_GET['action'] == "export") {
    require_once "quiz.export.php";
  }

  if ($_GET['action'] == "edit") {
    if ($user_id != $_SESSION['USERID']) {
      header("Location: ../../dashboard");
      return;
    }
    require_once "quiz.create.php";
  } else if ($_GET['action'] == "create") {
    require_once "quiz.create.php";
  } elseif ($_GET['action'] == "view") {
    if ($user_id != $_SESSION['USERID']) {
      if ($_GET['attemptno'] == "") {
        header("Location: ../../../dashboard");
        return;
      }
    }
    require_once "quiz.view.php";
  } elseif ($_GET['action'] == "delete") {
    $deleteGroupQuizQuery = $conn->prepare("DELETE FROM quiz_group WHERE qid IN (
                                            SELECT qid FROM quizzes WHERE uqid=:uqid
                                          );");
    $deleteGroupQuizQuery->execute(array(
      ":uqid" => $_GET['uqid']
    ));

    $deleteResponsesQuery = $conn->prepare("DELETE FROM responses WHERE qid IN (
                                            SELECT qid FROM quizzes WHERE uqid=:uqid
                                          );");
    $deleteResponsesQuery->execute(array(
      ":uqid" => $_GET['uqid']
    ));

    $deleteFeedbackQuery = $conn->prepare("DELETE FROM feedback WHERE qnid IN (
                                            SELECT qnid FROM questions WHERE qid = (
                                              SELECT qid FROM quizzes WHERE uqid=:uqid)
                                          );");
    $deleteFeedbackQuery->execute(array(
      ":uqid" => $_GET['uqid']
    ));

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

    header("Location: ../../dashboard");
  } elseif ($_GET['action'] == "authenticate") {
      require_once "quiz.authenticate.php";
  } elseif ($_GET['action'] == "attempt") {
      require_once "quiz.attempt.php";
  } elseif ($_GET['action'] == "responses") {
      require_once "quiz.responses.php";
  }  elseif ($_GET['action'] == "export") {
      require_once "quiz.export.php";
  }
?>
<link rel="stylesheet" href="quiz_style.css">
