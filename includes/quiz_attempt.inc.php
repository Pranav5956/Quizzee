<?php
  session_start();
  require_once "db.inc.php";
  if (isset($_POST)) {
    $quiz_id = null;

    foreach ($_POST as $key => $value) {
      if ($key == "uqid") {
        $selectQuizQuery = $conn->prepare("SELECT qid
                                           FROM quizzes
                                           WHERE uqid = :uqid");
        $selectQuizQuery->execute(array(
          ":uqid" => urldecode($value)
        ));
        $quiz_id = $selectQuizQuery->fetch(PDO::FETCH_ASSOC)['qid'];


        // Check for number of attempts
        $selectAttemptsQuery = $conn->prepare("SELECT MAX(attempt_no) AS attempt_no
                                               FROM responses
                                               WHERE uid=:uid AND qid=:qid;");
        $selectAttemptsQuery->execute(array(
        ":uid" => $_SESSION['USERID'],
        ":qid" => $quiz_id
        ));
        $attempts = $selectAttemptsQuery->fetch(PDO::FETCH_ASSOC)['attempt_no'];
        $attempt_time = time();
      } else {
        $question_option = explode('-', $key);
        $question_number = $question_option[0];
        $option_number = $question_option[1];

        // Get the question id
        $selectQuestionQuery = $conn->prepare("SELECT qnid
                                               FROM questions
                                               WHERE question_number=:question_number AND qid=:qid;");
        $selectQuestionQuery->execute(array(
          ":question_number" => $question_number,
          ":qid" => $quiz_id
        ));
        $question_id = $selectQuestionQuery->fetch(PDO::FETCH_ASSOC)['qnid'];

        // Get option id
        $selectOptionQuery = $conn->prepare("SELECT `oid`, option_number, weightage
                                           FROM options
                                           WHERE option_number=:option_number AND qnid=:qnid;");
        $selectOptionQuery->execute(array(
          ":option_number" => $option_number,
          ":qnid" => $question_id
        ));
        $result = $selectOptionQuery->fetch(PDO::FETCH_ASSOC);
        $option_id = $result['oid'];
        $weightage = (($result['option_number'] == 0)? 0:$result['weightage']);

        // Record response
        $insertResponseQuery = $conn->prepare("INSERT INTO responses(attempt_no, uid, qid, qnid, `oid`, response, weightage, attempt_time)
                                               VALUES(:attempts, :uid, :qid, :qnid, :opid, :response, :weightage, :attempt_time)");
        $insertResponseQuery->execute(array(
          ":attempts" => ($attempts + 1),
          ":uid" => $_SESSION['USERID'],
          ":qid" => $quiz_id,
          ":qnid" => $question_id,
          ":opid" => $option_id,
          ":response" => (($value == "on")? null : $value),
          ":weightage" => $weightage,
          ":attempt_time" => $attempt_time
        ));
      }
    }

    header("Location: ../my/dashboard");
  }
?>
