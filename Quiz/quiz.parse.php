<?php
  if (isset($_GET['uqid'])) {
    require_once "../includes/db.inc.php";
    // Get the entire quiz details
    $selectQuizDetailsQuery = $conn->prepare("SELECT questions.question_number, questions.description AS question_desc, questions.type,
                                                     options.option_number, options.description AS option_desc, options.isanswer
                                              FROM questions INNER JOIN options
                                              WHERE questions.qnid = options.qnid AND questions.qid =
                                                (SELECT qid FROM quizzes WHERE uqid = :uqid)");
    $selectQuizDetailsQuery->execute(array(
      ":uqid" => urldecode($_GET['uqid'])
    ));

    $selectQuizParameters = $conn->prepare("SELECT qname, create_time
                                            FROM quizzes
                                            WHERE uqid = :uqid");
    $selectQuizParameters->execute(array(
      ":uqid" => urldecode($_GET['uqid'])
    ));


    $quiz_info = $selectQuizDetailsQuery->fetchAll(PDO::FETCH_ASSOC);
    $quiz_params = $selectQuizParameters->fetch(PDO::FETCH_ASSOC);

    // $quiz_info[count($quiz_info)] = $quiz_params;
    $json_encoded_quiz_details = array();
    $json_encoded_quiz_details['quiz'] = $quiz_params;

    $current_question = 0;
    foreach ($quiz_info as $index => $value) {
      if ($current_question != $value['question_number']) {
        $current_question = $value['question_number'];
        $json_encoded_quiz_details[$current_question] = array();
        $json_encoded_quiz_details[$current_question]["description"] = $value['question_desc'];
        $json_encoded_quiz_details[$current_question]["type"] = $value['type'];
        $json_encoded_quiz_details[$current_question]["options"] = array();
      }

      $json_encoded_quiz_details[$current_question]["options"][$value['option_number']] = array($value['option_desc'], $value['isanswer']);
    }

    echo json_encode($json_encoded_quiz_details);
  }
?>
