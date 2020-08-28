<?php
  session_start();

  if (isset($_POST)) {
    // Collection of Question and their Parameters
    $quiz_name = "";
    $question_descriptions = array();
    $question_types = array();
    $question_options = array();

    $current_question_number = 0;
    $current_option_number = 0;

    // Processing quiz data
    foreach ($_POST as $quiz_param => $quiz_value) {
      if ($quiz_param == "quiz-name")
        $quiz_name = $quiz_value;

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
            $question_options[$current_question_number] = array(null => null);
          else
            $question_options[$current_question_number][$current_option_number] = array(
              "description" => $quiz_value,
              "isanswer" => 0
            );
        }
      }

      elseif (!is_array($current_question_number) && isset($current_option_number) &&
              $quiz_param == "editable-question-".$current_question_number."-option-isanswer") {
        $question_options[$current_question_number][$current_option_number]["isanswer"] = 1;
      }
    }

    require_once "db.inc.php";

    function createQuiz($quiz_name) {
      global $conn;
      $insertQuizQuery = $conn->prepare("INSERT INTO quizzes(uqid, qname, create_time, uid)
                                         VALUES(:uqid, :qname, :create_time, :uid)");
      $insertQuizQuery->execute(array(
        ":uqid" => uniqid("Q"),
        ":qname" => $quiz_name,
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

    function createOption($question_id, $option_number, $option_description, $option_isanswer) {
      global $conn;
      $insertQuizQuery = $conn->prepare("INSERT INTO options(option_number, description, isanswer, qnid)
                                         VALUES(:option_number, :description, :isanswer, :qnid)");
      $insertQuizQuery->execute(array(
        ":option_number" => $option_number,
        ":description" => $option_description,
        ":isanswer" => $option_isanswer,
        ":qnid" => $question_id
      ));
    }

    // Loading quiz data to the database
    $current_quiz_id = null;
    $current_question_id = null;

    $current_quiz_id = createQuiz($quiz_name);
    unset($_POST);

    foreach ($question_descriptions as $question_number => $description) {
      $current_question_id = createQuestion($current_quiz_id, $question_number, $description, $question_types[$question_number]);

      foreach ($question_options[$question_number] as $option_number => $option_attributes) {
        if ($option_number == null)
          createOption($question_id, 0, null, 0);
        else
          createOption($current_question_id, $option_number, $option_attributes["description"], $option_attributes["isanswer"]);
      }
    }
  }

  header("Location: ../my/dashboard");
?>
