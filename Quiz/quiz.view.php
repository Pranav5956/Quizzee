<link rel="stylesheet" href="quiz_edit.css">
<?php
  if (isset($_GET['uqid'])) {
    require_once "../includes/db.inc.php";
    // Get the quiz id
    $selectQuizQuery = $conn->prepare("SELECT qid, qname, create_time
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

      // Select the options
      $options = array();
      foreach ($questions as $index => $attributes) {
        $selectOptionQuery = $conn->prepare("SELECT option_number, description, isanswer
                                             FROM options
                                             WHERE qnid = :qnid");
        $selectOptionQuery->execute(array(
          ":qnid" => $attributes['qnid']
        ));
        $options[$attributes['question_number']] = $selectOptionQuery->fetchAll(PDO::FETCH_ASSOC);
      }
      unset($quiz['qid']);
    }
  }
?>

<?php if (isset($_GET['uqid'])): ?>
  <h1><?php echo $quiz['qname'] ?></h1>
  <?php foreach ($questions as $qn_index => $question_attributes): ?>
    <div class="preview-question-container">
      <p class="preview-question-description"><?php echo htmlentities($question_attributes['question_number'].'. '.$question_attributes['description'],
                                 ENT_QUOTES, 'utf-8'); ?></p>

      <?php foreach ($options[$question_attributes['question_number']] as $op_index => $option_attributes): ?>
        <div class="preview-question-option-container">
          <?php if ($question_attributes['type'] == 'MCQ' || $question_attributes['type'] == 'TF'): ?>
            <input type="radio" class="preview-question-option-display" name=<?php echo $question_attributes['question_number']; ?>
                   id=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?>>
            <label class="preview-question-option-label" for=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?>>
              <?php echo htmlentities($option_attributes['description'], ENT_QUOTES, 'utf-8'); ?>
            </label>
          <?php elseif ($question_attributes['type'] == 'MCMQ'): ?>
            <input type="checkbox" class="preview-question-option-display" name=<?php echo $question_attributes['question_number']; ?>
                   id=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?>>
            <label class="preview-question-option-label" for=<?php echo $question_attributes['question_number'].'-'.$option_attributes['option_number']; ?>>
              <?php echo htmlentities($option_attributes['description'], ENT_QUOTES, 'utf-8'); ?>
            </label>
          <?php elseif ($question_attributes['type'] == 'D'): ?>
            <textarea rows="8" cols="60" placeholder="Enter answer here"></textarea>
          <?php endif; ?>
        </div>

      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
