<?php
  if (isset($_POST["attempt"])) {
    if (isset($_GET['uqid'])) {
      require_once "../includes/db.inc.php";
      // Get the entire quiz details
      $selectQuizDetailsQuery = $conn->prepare("SELECT questions.question_number, questions.description AS question_desc, questions.type,
                                                       options.option_number, options.description AS option_desc, options.isanswer
                                                FROM questions INNER JOIN options
                                                WHERE questions.qnid = options.qnid AND questions.qid IN
                                                  (SELECT qid FROM quizzes WHERE uqid = :uqid)");
      $selectQuizDetailsQuery->execute(array(
        ":uqid" => urldecode($_GET['uqid'])
      ));

      $selectQuizParameters = $conn->prepare("SELECT qname, type, create_time
                                              FROM quizzes
                                              WHERE uqid = :uqid");
      $selectQuizParameters->execute(array(
        ":uqid" => urldecode($_GET['uqid'])
      ));


      $quiz_info = $selectQuizDetailsQuery->fetchAll(PDO::FETCH_ASSOC);
      $quiz_params = $selectQuizParameters->fetch(PDO::FETCH_ASSOC);

      // $quiz_info[count($quiz_info)] = $quiz_params;
      $quiz_details = array();

      $current_question = 0;
      foreach ($quiz_info as $index => $value) {
        if ($current_question != $value['question_number']) {
          $current_question = $value['question_number'];
          $quiz_details[$current_question] = array(
            "question_number" => $current_question,
            "description" => $value['question_desc'],
            "type" => $value['type'],
            "options" => array()
          );
        }

        $quiz_details[$current_question]["options"][$value['option_number']] = array(
          "option_number" => $value['option_number'],
          "description" => $value['option_desc'],
          "isanswer" => $value['isanswer']
        );
      }
      shuffle($quiz_details);
      foreach ($quiz_details as $question_index => $attributes) {
        shuffle($quiz_details[$question_index]["options"]);
      }
      shuffle($quiz_details);
    }
  } else {
    header("Location: ../authenticate/".$_GET['uqid']);
    // print_r($_POST);
  }
?>
<!-- <link rel="stylesheet" href=""> -->
<?php if (isset($_POST['attempt'])): ?>
  <div class="quiz-attempt">
    <h1><?php echo $quiz_params['qname'] ?></h1>
    <form class="form-attempt" action="../includes/quiz_attempt.inc.php" method="post">
      <input type="text" id="uqid" name="uqid" value=<?php echo $_GET['uqid']; ?> hidden>
      <?php foreach ($quiz_details as $question_index => $question_attributes): ?>
        <div class="preview-question-container">
          <div class="preview-question-description-container">
            <p class="preview-question-description"><?php echo ($question_index + 1).". ".$question_attributes["description"] ?></p>
          </div>
          <?php foreach ($question_attributes["options"] as $option_index => $option_attributes): ?>
            <div class="preview-question-option-container">
              <?php if ($question_attributes['type'] == 'MCQ' || $question_attributes['type'] == 'TF'): ?>
                <input type="radio" class="preview-question-option-display" name=<?php echo $question_attributes['question_number']; ?>
                       id=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?>>
                <label class="preview-question-option-label" for=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?>>
                  <?php echo htmlentities($option_attributes['description'], ENT_QUOTES, 'utf-8'); ?>
                </label>
              <?php elseif ($question_attributes['type'] == 'MCMQ'): ?>
                <input type="checkbox" class="preview-question-option-display" name=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?>
                       id=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?>>
                <label class="preview-question-option-label" for=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?>>
                  <?php echo htmlentities($option_attributes['description'], ENT_QUOTES, 'utf-8'); ?>
                </label>
              <?php elseif ($question_attributes['type'] == 'D'): ?>
                <textarea rows="8" cols="60" id=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?>
                  name=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?> placeholder="Enter answer here"></textarea>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
          <?php if ($question_attributes["type"] == "MCQ" || $question_attributes["type"] == "MCMQ" || $question_attributes["type"] == "TF"): ?>
            <button type="button" name="clear" class="editable-question-button-clear">Clear Selection</button>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
      <input type="submit" name="attempt-submit" value="Submit" onclick="format();">
    </form>
  </div>
<?php endif; ?>

<script type="text/javascript">
  $(".editable-question-button-clear").click( function() {
    console.log($(this).parent().children().children("input:checked"));
    $(this).parent().children().children("input:checked").prop("checked", false);
  });

  function format() {
    $("input").each(function(index, element) {
      element.name = element.id;
    })
  }
</script>
