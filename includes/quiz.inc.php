<?php
  session_start();

  if (isset($_POST)) {
    // Collection of Question and their Parameters
    $quiz_name = "";
    $quiz_code = null;
    $question_descriptions = array();
    $question_types = array();
    $question_options = array();

    $current_question_number = 0;
    $current_option_number = 0;

    // Processing quiz data
    foreach ($_POST as $quiz_param => $quiz_value) {
      if ($quiz_param == "quiz-name")
        $quiz_name = $quiz_value;

      else if ($quiz_param == "quiz-type")
        $quiz_type = $quiz_value;

      else if ($quiz_param == "quiz-code")
        $quiz_code = $quiz_value;

      elseif (preg_match("/^editable-question-(\d+)-description$/", $quiz_param, $question_number) === 1) {
        $current_question_number = $question_number[1];
        $question_descriptions[$current_question_number] = $quiz_value;
      }

      elseif (preg_match("/^editable-question-\d+-type$/", $quiz_param) === 1) {
        $current_question_type = $quiz_value;
        if (isset($current_question_number)) {
          $question_types[$current_question_number] = $current_question_type;

          if ($current_question_type != "D")
            $question_options[$current_question_number] = array();
        }
      }

      elseif (preg_match("/^editable-question-(\d+)-option-(\d+)$/", $quiz_param, $attributes) === 1) {
        $question_number = $attributes[1];
        $current_option_number = $attributes[2];

        if (isset($current_question_type) && isset($current_question_number) && $current_question_number == $question_number) {
          if ($current_question_type == "D")
            $question_options[$current_question_number][1] = array("mark" => 0);
          else
            $question_options[$current_question_number][$current_option_number] = array(
              "description" => $quiz_value,
              "isanswer" => 0,
              "mark" => 0
            );
        }
      }

      elseif (!is_array($current_question_number) && isset($current_option_number) &&
              preg_match("/.*isanswer$/", $quiz_param) === 1) {
        $question_options[$current_question_number][$current_option_number]["isanswer"] = 1;
      }

      elseif (!is_array($current_question_number) && isset($current_option_number) &&
              preg_match("/.*mark$/", $quiz_param) === 1) {
        $question_options[$current_question_number][$current_option_number]["mark"] = $quiz_value;
      }
    }

    require_once "db.inc.php";
    if (isset($_GET['uqid'])) {
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
                                              SELECT qnid FROM questions WHERE qid = (
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

      $deleteQuestionsQuery = $conn->prepare("DELETE FROM quiz_group WHERE qid IN (
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

    function createQuiz($quiz_name, $quiz_type, $quiz_code) {
      global $conn;
      $insertQuizQuery = $conn->prepare("INSERT INTO quizzes(uqid, qname, type, code, create_time, uid)
                                         VALUES(:uqid, :qname, :type, :code, :create_time, :uid)");

      $insertQuizQuery->execute(array(
        ":uqid" => 'Q'.hash('crc32', $quiz_name.$quiz_type.time()),
        ":qname" => $quiz_name,
        ":type" => $quiz_type,
        ":code" => $quiz_code,
        ":create_time" => time(),
        ":uid" => $_SESSION['USERID']
      ));
      return $conn->lastInsertId();
    }

    function createQuestion($quiz_id, $question_number, $question_description, $question_type) {
      global $conn;
      $insertQuizQuery = $conn->prepare("INSERT INTO questions(question_number, description, type, qid)
                                         VALUES(:question_number, :description, :type, :qid)");
      $insertQuizQuery->execute(array(
        ":question_number" => $question_number,
        ":description" => $question_description,
        ":type" => $question_type,
        ":qid" => $quiz_id
      ));
      return $conn->lastInsertId();
    }

    function createOption($question_id, $option_number, $option_description, $option_isanswer, $option_mark) {
      global $conn;
      $insertQuizQuery = $conn->prepare("INSERT INTO options(option_number, description, isanswer, weightage, qnid)
                                         VALUES(:option_number, :description, :isanswer, :weightage, :qnid)");
      $insertQuizQuery->execute(array(
        ":option_number" => $option_number,
        ":description" => $option_description,
        ":isanswer" => $option_isanswer,
        ":weightage" => $option_mark,
        ":qnid" => $question_id
      ));
    }

    // Loading quiz data to the database
    $current_quiz_id = null;
    $current_question_id = null;

    $current_quiz_id = createQuiz($quiz_name, $quiz_type, $quiz_code);

    if (isset($_POST['groups'])) {
      foreach ($_POST['groups'] as $index => $ugid) {
        $gidQuery = $conn->prepare("SELECT gid FROM groups WHERE ugid=:ugid");
        $gidQuery->execute(array(":ugid" => $ugid));
        $gid = $gidQuery->fetch(PDO::FETCH_ASSOC)['gid'];

        $insertQuizQuery = $conn->prepare("INSERT INTO quiz_group(gid, qid)
                                           VALUES(:gid, :qid)");
        $insertQuizQuery->execute(array(
          ":gid" => $gid,
          ":qid" => $current_quiz_id
        ));
      }
    }

    foreach ($question_descriptions as $question_number => $description) {
      $current_question_id = createQuestion($current_quiz_id, $question_number, $description, $question_types[$question_number]);
      // printf($current_question_id);

      foreach ($question_options[$question_number] as $option_number => $option_attributes) {
        if ($question_types[$question_number] == "D") {
          createOption($current_question_id, 0, null, 0, $option_attributes['mark']);
        }
        else {
          createOption($current_question_id, $option_number, $option_attributes["description"], $option_attributes["isanswer"],
                       $option_attributes["mark"]);
        }
      }
    }
  }
  header("Location: ../my/dashboard");
?>
