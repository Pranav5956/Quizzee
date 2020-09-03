<link rel="stylesheet" href="quiz_edit.css">
<?php

  if (isset($_GET['uqid'])) {
    require_once "../includes/db.inc.php";
    // Get the quiz id
    $selectQuizQuery = $conn->prepare("SELECT qid, qname, type, code, create_time
                                       FROM quizzes
                                       WHERE uqid = :uqid");
    $selectQuizQuery->execute(array(
      ":uqid" => urldecode($_GET['uqid'])
    ));
    $quiz = $selectQuizQuery->fetch(PDO::FETCH_ASSOC);

    if ($quiz !== false) {
      // Select the questions
      $selectQuestionQuery = $conn->prepare("SELECT qnid, question_number, description, type
                                             FROM questions
                                             WHERE qid = :qid");
      $selectQuestionQuery->execute(array(
        ":qid" => $quiz['qid']
      ));
      $questions = $selectQuestionQuery->fetchAll(PDO::FETCH_ASSOC);

      // Get the responses
      $marklist = array();
      if (isset($_GET['attemptno'])) {
        $responsesQuery = $conn->prepare("SELECT oid, response, weightage
                                          FROM responses
                                          WHERE attempt_no=:attno AND uid=:uid AND qid=:qid");
        $responsesQuery->execute(array(
          ":attno" => $_GET['attemptno'],
          ":uid" => $_SESSION['USERID'],
          ":qid" => $quiz['qid']
        ));
        $responseList = $responsesQuery->fetchAll(PDO::FETCH_ASSOC);

        $responses = array();
        $marks = 0;
        foreach ($responseList as $key => $value) {
          $responses[$value['oid']] = (!empty($value['response'])? $value['response']:"No answer provided");
          $marks += $value['weightage'];
          $marklist[$value['oid']] = $value['weightage'];
        }
      }

      // Select the options
      $options = array();
      $feedback = array();
      foreach ($questions as $index => $attributes) {
        $selectOptionQuery = $conn->prepare("SELECT oid, option_number, description, isanswer, weightage
                                             FROM options
                                             WHERE qnid = :qnid");
        $selectOptionQuery->execute(array(
          ":qnid" => $attributes['qnid']
        ));
        $options[$attributes['question_number']] = $selectOptionQuery->fetchAll(PDO::FETCH_ASSOC);

        // Get feedback
        $selectFeedbackQuery = $conn->prepare("SELECT feedback_text
                                               FROM feedback
                                               WHERE qnid = :qnid AND uid=:uid AND attempt_no=:ano");
        $selectFeedbackQuery->execute(array(
          ":qnid" => $attributes['qnid'],
          ":uid" => $_SESSION['USERID'],
          ":ano" => $_GET['attemptno']
        ));
        $feedback[$attributes['question_number']] = $selectFeedbackQuery->fetch(PDO::FETCH_ASSOC);
        if ($feedback[$attributes['question_number']] === false)
          $feedback[$attributes['question_number']] = "";
        else
          $feedback[$attributes['question_number']] = $feedback[$attributes['question_number']]['feedback_text'];

        foreach ($options[$attributes['question_number']] as $oindex => $option_attributes) {
          if (isset($responses[$options[$attributes['question_number']][$oindex]["oid"]])) {
            if ($options[$attributes['question_number']][$oindex]["isanswer"]) {
              $options[$attributes['question_number']][$oindex]["condition"] = "attended-correct";  //good
            } else {
              $options[$attributes['question_number']][$oindex]["condition"] = "attended-incorrect"; //bad
            }

            if ($options[$attributes['question_number']][$oindex]["option_number"] == 0) {
              $options[$attributes['question_number']][$oindex]["condition"] = "attended-descriptive";  //pending review
            }
          } else {
            if ($options[$attributes['question_number']][$oindex]["isanswer"]) {
              $options[$attributes['question_number']][$oindex]["condition"] = "unattended-correct";  //bad
            } else {
              $options[$attributes['question_number']][$oindex]["condition"] = "unattended-incorrect"; //good
            }

            if ($options[$attributes['question_number']][$oindex]["option_number"] == 0) {
              $options[$attributes['question_number']][$oindex]["condition"] = "unattended-descriptive";  //bad
            }
          }
        }
      }
      // print_r($options);

      $marksQuery = $conn->prepare("SELECT SUM(weightage) AS tot_mark
                                    FROM options
                                    WHERE (isanswer=1 OR option_number=0) AND qnid IN (
                                      SELECT qnid
                                      FROM questions
                                      WHERE qid = :qid
                                    );");
      $marksQuery->execute(array(
        ":qid" => $quiz['qid']
      ));
      $total_mark = $marksQuery->fetch(PDO::FETCH_ASSOC)['tot_mark'];

      // Get the feedback
      $selectFeedbackQuery = $conn->prepare("SELECT qnid, feedback_text
                                             FROM feedback
                                             WHERE uid=:uid AND attempt_no=:attno AND qnid IN (
                                               SELECT qnid
                                               FROM questions
                                               WHERE qid = :qid
                                             );");
      $selectFeedbackQuery->execute(array(
        ":uid" => $_SESSION['USERID'],
        ":attno" => $_GET['attemptno'],
        ":qid" => $quiz['qid']
      ));
      $feedbacks = $selectFeedbackQuery->fetchAll(PDO::FETCH_ASSOC);
      $feedback = array();
      foreach ($feedbacks as $f_index => $f_value) {
        $feedback[$f_value['qnid']] = $f_value['feedback_text'];
      }
    }
  }
?>

<?php if (empty($_GET['attemptno'])): ?>

  <div class="row">
    <div class="col-3"></div>
    <div class=col-6>
      <?php if (isset($_GET['uqid'])): ?>
        <h1><?php echo $quiz['qname'] ?></h1>
        <h5><?php echo "Total Score: ".$total_mark; ?></h5>
        <?php if (isset($quiz['code']) && empty($_GET['attemptno'])): ?>
          <div class="quiz-code-container">
            <label for="quiz-code-display">Quiz Code: </label>
            <input id="quiz-code-display" type="text" name="quiz-code" value=<?php echo $quiz['code']; ?> min="8" max="8" readonly>
          </div>
        <?php endif; ?>
        <?php foreach ($questions as $qn_index => $question_attributes): ?>
          <div class="preview-question-container">
            <p class="preview-question-description"><?php echo htmlentities($question_attributes['question_number'].'. '.$question_attributes['description'],
                                       ENT_QUOTES, 'utf-8'); ?></p>

            <?php foreach ($options[$question_attributes['question_number']] as $op_index => $option_attributes): ?>
              <div class="preview-question-option-container">
                <?php if ($question_attributes['type'] == 'MCQ' || $question_attributes['type'] == 'TF'): ?>
                  <?php if ($option_attributes["isanswer"] == 1): ?>
                    <input type="radio" class="preview-question-option-display invisible" name=<?php echo $question_attributes['question_number']; ?>
                           id=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?> onclick="return false;" checked>
                    <label class="preview-question-option-label correct" for=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?>>
                      <?php echo htmlentities($option_attributes['description'], ENT_QUOTES, 'utf-8'); ?>
                    </label>
                  <?php else: ?>
                    <input type="radio" class="preview-question-option-display invisible" name=<?php echo $question_attributes['question_number']; ?>
                           id=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?> onclick="return false;">
                    <label class="preview-question-option-label wrong" for=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?>>
                      <?php echo htmlentities($option_attributes['description'], ENT_QUOTES, 'utf-8'); ?>
                    </label>
                  <?php endif; ?>
                  <label class="preview-question-option-label" for=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?>>
                    <?php echo htmlentities("(".$option_attributes['weightage']." marks)", ENT_QUOTES, 'utf-8'); ?>
                  </label>
                <?php elseif ($question_attributes['type'] == 'MCMQ'): ?>
                  <?php if ($option_attributes["isanswer"] == 1): ?>
                    <input type="checkbox" class="preview-question-option-display invisible" name=<?php echo $question_attributes['question_number']; ?>
                           id=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?> onclick="return false;" checked>
                    <label class="preview-question-option-label correct" for=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?>>
                      <?php echo htmlentities($option_attributes['description'], ENT_QUOTES, 'utf-8'); ?>
                    </label>
                  <?php else: ?>
                    <input type="checkbox" class="preview-question-option-display invisible" name=<?php echo $question_attributes['question_number']; ?>
                           id=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?> onclick="return false;">
                    <label class="preview-question-option-label incorrect" for=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?>>
                      <?php echo htmlentities($option_attributes['description'], ENT_QUOTES, 'utf-8'); ?>
                    </label>
                  <?php endif; ?>
                  <label class="preview-question-option-label" for=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?>>
                    <?php echo htmlentities("(".$option_attributes['weightage']." marks)", ENT_QUOTES, 'utf-8'); ?>
                  </label>
                <?php elseif ($question_attributes['type'] == 'D'): ?>
                  <textarea rows="8" cols="60" placeholder="Enter answer here" readonly></textarea>
                  <label class="preview-question-option-label">
                    <?php echo htmlentities("(".$option_attributes['weightage']." marks)", ENT_QUOTES, 'utf-8'); ?>
                  </label>
                <?php endif; ?>
              </div>

            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <div class="col-3"></div>
  </div>

<?php else: ?>
  <div class="row">
    <div class="col-3"></div>
      <div class="col-6">

        <div class="quiz-info-container">
          <?php if (!empty($_GET['uqid'])): ?>
            <h1><?php echo $quiz['qname'] ?></h1>
            <h5><?php echo "Marks: ".$marks."/".$total_mark ?></h5>
            <?php if (isset($quiz['code']) && empty($_GET['attemptno'])): ?>
              <div class="quiz-code-container">
                <label for="quiz-code-display">Quiz Code: </label>
                <input id="quiz-code-display" type="text" name="quiz-code" value=<?php echo $quiz['code']; ?> min="8" max="8" readonly>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>

        <div class="quiz-questions-container">
          <?php foreach ($questions as $q_index => $q_attr): ?>
            <div class="preview-question-container">
              <p class="preview-question-description">
                <?php echo htmlentities($q_attr['question_number'].'. '.$q_attr['description'], ENT_QUOTES, 'utf-8'); ?>
              </p>

              <?php foreach ($options[$q_attr['question_number']] as $o_index => $o_attr): ?>
                <div class="preview-question-option-container">
                  <?php if ($q_attr['type'] == 'MCQ' || $q_attr['type'] == 'TF'): ?>
                    <input type="radio" class="preview-question-option-display invisible" name=<?php echo $q_attr['question_number']; ?>
                           id=<?php echo $q_attr['question_number'].'-'.$o_attr['option_number']; ?> onclick="return false;">
                    <label class="preview-question-option-label <?php echo $o_attr['condition']; ?>"
                           for=<?php echo $q_attr['question_number'].'-'.$o_attr['option_number']; ?>>
                      <?php echo htmlentities($o_attr['description'], ENT_QUOTES, 'utf-8'); ?>
                    </label>

                    <p>
                      <?php
                        if ($o_attr['condition'] == "attended-correct")
                          echo htmlentities($o_attr['weightage']."/".$o_attr['weightage']." marks", ENT_QUOTES, 'utf-8');
                        elseif ($o_attr['condition'] == "unattended-correct")
                          echo htmlentities("0/".$o_attr['weightage']." marks", ENT_QUOTES, 'utf-8');
                      ?>
                    </p>
                  <?php elseif ($q_attr['type'] == 'MCMQ'): ?>
                    <input type="checkbox" class="preview-question-option-display invisible" name=<?php echo $q_attr['question_number']; ?>
                           id=<?php echo $q_attr['question_number'].'-'.$o_attr['option_number']; ?> onclick="return false;">
                    <label class="preview-question-option-label <?php echo $o_attr['condition']; ?>"
                           for=<?php echo $q_attr['question_number'].'-'.$o_attr['option_number']; ?>>
                      <?php echo htmlentities($o_attr['description'], ENT_QUOTES, 'utf-8'); ?>
                    </label>

                    <p>
                      <?php
                        if ($o_attr['condition'] == "attended-correct")
                          echo htmlentities($o_attr['weightage']."/".$o_attr['weightage']." marks", ENT_QUOTES, 'utf-8');
                        elseif ($o_attr['condition'] == "unattended-correct")
                          echo htmlentities("0/".$o_attr['weightage']." marks", ENT_QUOTES, 'utf-8');
                      ?>
                    </p>
                  <?php elseif ($q_attr['type'] == 'D'): ?>
                    <textarea rows="8" cols="60" placeholder="Enter answer here" readonly><?php echo isset($responses[$o_attr['oid']])? $responses[$o_attr['oid']]:"No answer provided" ?></textarea>
                    <p>
                      <?php
                        if (isset($marklist[$o_attr['oid']]))
                          echo htmlentities($marklist[$o_attr['oid']]."/".$o_attr['weightage']." marks", ENT_QUOTES, 'utf-8');
                        else
                          echo htmlentities("0/".$o_attr['weightage']." marks", ENT_QUOTES, 'utf-8');
                      ?>
                    </p>
                  <?php endif ?>
                </div>
              <?php endforeach; ?>

              <p>
                <?php if (!empty($feedback[$q_attr['qnid']])): ?>
                  <?php echo "Remarks: ".$feedback[$q_attr['qnid']] ?>
                <?php elseif ($q_attr['type'] == 'D'): ?>
                  <?php echo "Your response has been sent for evaluation and your marks will be updated once the evaluation is completed."; ?>
                <?php endif; ?>
              </p>
            </div>
          <?php endforeach; ?>
        </div>

      </div>
    <div class="col-3"></div>
  </div>
<?php endif; ?>

<script type="text/javascript">
  $("#quiz-code-display")
  .click(function() {
    $(this).select();
    // $(this).setSelectionRange(0, 99999); /*For mobile devices*/
    document.execCommand("copy");
  });
</script>
