<?php
  if ($_POST) {
    foreach ($_POST as $key => $value) {
      if (substr($key, 0, 1) == 'o') {
        $oid = ltrim($key, 'o');
        $updateMarks = $conn->prepare("UPDATE responses
                                       SET weightage=$value
                                       WHERE uid=:uid AND attempt_no=:attno AND oid=:oid");
        $updateMarks->execute(array(
          ":uid" => ltrim($_GET['uname'], 'u'),
          ":attno" => $_GET['attemptno'],
          ":oid" => $oid
        ));
      } elseif (substr($key, 0, 1) == 'q') {
        if (!empty($value)) {
          $qnid = ltrim($key, 'q');
          $insertFeedback = $conn->prepare("INSERT INTO feedback(uid, qnid, attempt_no, feedback_text)
                                            VALUES(:uid, :qnid, :attno, :feedback_text)");
          $insertFeedback->execute(array(
            ":uid" => ltrim($_GET['uname'], 'u'),
            ":qnid" => $qnid,
            ":attno" => $_GET['attemptno'],
            ":feedback_text" => $value
          ));
        }
      }
    }
    // print_r($_POST);
    header("Location: /Quizzee/my/quizzes/responses/".$_GET['uqid']);
  }

  if (empty($_GET['uqid'])) {
    header("Location: ../my/dashboard");
    return;
  }

  require_once "../includes/db.inc.php";
  if (empty($_GET['attemptno']) && empty($_GET['uname'])) {
    $selectResponsesQuery = $conn->prepare("SELECT users.uid, users.fname, users.lname, users.email, responses.attempt_no, responses.attempt_time
                                            FROM responses JOIN users ON responses.uid=users.uid
                                            WHERE qid IN (
                                              SELECT qid FROM quizzes WHERE uqid=:uqid
                                            )");
    $selectResponsesQuery->execute(array(
      ":uqid" => urldecode($_GET['uqid'])
    ));
    $responses = $selectResponsesQuery->fetchAll(PDO::FETCH_ASSOC);
    if ($responses !== false) {
      $responses = array_unique($responses, SORT_REGULAR);
    }

  } else {
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
          ":uid" => ltrim($_GET['uname'], 'u'),
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
      foreach ($questions as $index => $attributes) {
        $selectOptionQuery = $conn->prepare("SELECT oid, option_number, description, isanswer, weightage
                                             FROM options
                                             WHERE qnid = :qnid");
        $selectOptionQuery->execute(array(
          ":qnid" => $attributes['qnid']
        ));
        $options[$attributes['question_number']] = $selectOptionQuery->fetchAll(PDO::FETCH_ASSOC);

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
    }
  }
?>
<link rel="stylesheet" href="response_authenticate.css">
<?php if (empty($_GET['attemptno']) && empty($_GET['uname'])): ?>
  <?php if (!count($responses)): ?>
    <div class="no-response"<p>There are no responses to this quiz yet. Responses will be displayed here once they start coming in.</p></div>
  <?php endif; ?>

  <?php foreach ($responses as $index => $response): ?>
    <div class="card float-left">
        <p class="card-header"><?php echo $response['fname']." ".$response['lname'] ?></p>
          <div class="card-body">
        <p><?php echo $response['email'] ?></p>
        <p><?php echo "Attempt: ".$response['attempt_no'] ?></p>
        Attempted on:
        <p><?php echo date(DATE_COOKIE, $response['attempt_time']) ?></p>
        <a href=<?php echo "../my/quizzes/responses/".$_GET['uqid']."/".$response['attempt_no']."/u".$response['uid'] ?>>
          View attempt
        </a>
        <hr>
      </div>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <link rel="stylesheet" href="quiz_style.css">
  <form class="form-acknowledge" action=<?php echo $_SERVER['REQUEST_URI'] ?> method="post">
    <div class="row">
      <div class="col-3"></div>
        <div class="col-6 main">
          <div class="quiz-info-container">
            <?php if (!empty($_GET['uqid'])): ?>
              <h1 class="quiz-header"><?php echo $quiz['qname'] ?></h1>
              <h5 class="quiz-header"><?php echo "Marks: ".$marks."/".$total_mark ?></h5>
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
                      <label>Choose mark for the above answer: </label>
                      <input class="mark" type="number" name=<?php echo 'o'.$o_attr['oid'] ?> value="0" min="0" max=<?php echo $o_attr['weightage'] ?>>
                    <?php endif ?>
                  </div>
                <?php endforeach; ?>
                <label>Send a feedback: </label>
                <input clas="feedback" type="text" name=<?php echo 'q'.$q_attr['qnid'] ?> placeholder="Enter feedback here">
              </div>
            <?php endforeach; ?>
          </div>
          <input type="submit" name="acknowledge" value="Acknowledge">
        </div>
      <div class="col-3"></div>
    </div>
  </form>
<?php endif; ?>
