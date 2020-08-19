<?php
  require_once "includes/utilities.inc.php";
  require_once "header.php";
?>

<base href="/" />
<?php if (isset($_GET['action']) && isset($_SESSION['TYPE']) && $_SESSION['TYPE'] == 'LOGIN'): ?>
  <form action="/OnlineQuizManagement/includes/profile.inc.php" method="post" enctype="multipart/form-data">
    <input type="file" name="profile_pic" value="Upload Profile Picture" required>
    <input type="submit" class="btn btn-primary" name="upload" value="Upload Profile Picture">
  </form>
<?php else: ?>
  <?php
    if (isset($_SESSION['USERID'])) {
      header("Location: /OnlineQuizManagement/Users/".$_SESSION['NAME_URL']."/Dashboard");
      return;
    } else {
      header("Location: /OnlineQuizManagement/Quizee");
      return;
    }

  ?>
<?php endif; ?>
