<base href="/Quizzee/Quiz/">
<?php
  require_once "../header.php";

  if ($_GET['action'] == "create") {
    require_once "quiz.create.php";
  } elseif ($_GET['action'] == "view" && isset($_GET['uqid'])) {
    require_once "quiz.view.php";
  } elseif ($_GET['action'] == "attend" && isset($_GET['uqid'])) {
    require_once "quiz.attend.php";
  }
?>
