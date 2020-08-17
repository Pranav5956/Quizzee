<?php require_once "includes/utilities.inc.php" ?>

<?php if (isset($_GET['action'])): ?>
  <form action="includes/change.inc.php" method="post" enctype="multipart/form-data">
    <input type="file" name="profile_pic" value="Upload Profile Picture" required>
    <input type="submit" class="btn btn-primary" name="upload" value="Upload Profile Picture">
  </form>
<?php else: ?>
  <?php
    header("Location: ../index.php");
    return;
  ?>
<?php endif; ?>
