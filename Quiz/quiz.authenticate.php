<?php
  if (isset($_GET['uqid'])) {
    require_once "../includes/db.inc.php";
    $selectQuizQuery = $conn->prepare("SELECT qid, qname, type, code, create_time
                                       FROM quizzes
                                       WHERE uqid = :uqid");
    $selectQuizQuery->execute(array(
      ":uqid" => urldecode($_GET['uqid'])
    ));
    $quiz = $selectQuizQuery->fetch(PDO::FETCH_ASSOC);

    $selectAttemptsQuery = $conn->prepare("SELECT attempt_no, attempt_time
                                           FROM responses
                                           WHERE qid = :qid AND uid = :uid
                                           GROUP BY attempt_no
                                           ORDER BY attempt_no");
    $selectAttemptsQuery->execute(array(
      ":qid" => $quiz['qid'],
      ":uid" => $_SESSION['USERID']
    ));
    $attempts = $selectAttemptsQuery->fetchAll(PDO::FETCH_ASSOC);
  }
  $attempt_url = "../my/quizzes/attempt/".$_GET['uqid'];
  $attempt_view_url = "../my/quizzes/view/".$_GET['uqid']."/";
?>

<link rel="stylesheet" href="response_authenticate.css">
<body class="auth">
<div class="quiz-details">
  <h1><?php echo $quiz['qname'] ?></h1>
  <p>Created on: <?php echo date(DATE_COOKIE, $quiz['create_time']) ?></p>
  <?php if ($quiz['type'] == "O"): ?>
    <p>This is an Open Quiz. Would you like to attempt it?</p>
    <form class="form-authenticate" action=<?php echo $attempt_url ?> method="post">
      <input type="submit" name="attempt" value="Attempt" class="btn btn-success">
      <input type="button" class="leave-button btn btn-danger" name="leave" value="Leave">
    </form>
  <?php elseif ($quiz['type'] == "C"): ?>
    <p>This is a 'Code-Protected' Quiz. Enter the Quiz Code to attempt this Quiz.</p>
    <form class="form-authenticate" action=<?php echo $attempt_url ?> method="post" onsubmit="return validate('<?php echo $quiz['code'] ?>');">
      <label for="code-input">Enter the Quiz Code: </label>
      <input type="password" id="code-input" name="code" required>
      <input type="submit" name="attempt" value="Attempt" class="btn btn-success">
      <input type="button" class="leave-button btn btn-danger" name="leave" value="Leave">
    </form>
  <?php endif; ?>
  <p class="error" style="color: red; display: none;">Incorrect Code</p>
</div>

<?php if (isset($attempts) && $attempts): ?>
  <h3 class="attempt-header">Previous Attempts: </h3>
  <?php foreach ($attempts as $key => $value): ?>
    <div class="attempts-container">
      <a href=<?php echo $attempt_view_url.$value["attempt_no"] ?>><?php echo "Attempt-".$value["attempt_no"]." (".date(DATE_COOKIE, $value["attempt_time"]).")"; ?></a>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
</body>
<script type="text/javascript">
  $(".leave-button").click(function() {
    window.history.back();
  });

  function validate(code) {
    if ($("#code-input").val() == code) {
      $(".error").hide();
      return true;
    } else {
      $(".error").show();
      return false;
    }
  };
</script>
