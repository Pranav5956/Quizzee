<style>
body{
  background:url("https://images.unsplash.com/photo-1597008641621-cefdcf718025?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1874&q=80");
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center center;
  background-attachment: fixed;
}
  img {
    width: 150px;
    height: 150px;
  }
  form{
    width:30%;
    margin:3rem;
  }
  input.form-control{
    background-color: rgba(0,0,0,0.2);
    color:white;
    border: 2px;
  }
  input.form-control:focus{
    background-color:rgba(0,0,0,0.5);
    color:white;
  }
  .profile-upload-btn {
    border-radius: 50%;
    padding-left: 2.5px;
    padding-top: 3px;
    border: 2px solid black;
    position: relative;
    left: -36px;
    top: 50px;
    width: 25px;
    height: 25px;
    transform: scale(2);
    background-color: white;
  }

  .profile-upload-btn:hover {
    background-color: #EEE;
  }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.css">

<?php
  require_once "includes/utilities.inc.php";
  require_once "header.php";

  if (isset($_GET['action']) && isset($_SESSION['TYPE']) && $_SESSION['TYPE'] == 'LOGIN') {
    require_once "includes/db.inc.php";
    $user = $conn->prepare("SELECT * FROM Users WHERE uid=:uid");
    $user->execute(array(
      ":uid" => $_SESSION['USERID']
    ));
    $user = $user->fetch(PDO::FETCH_ASSOC);
  }
?>

<?php if (isset($_GET['action']) && isset($_SESSION['TYPE']) && $_SESSION['TYPE'] == 'LOGIN'): ?>
  <form action="../includes/profile.inc.php" method="post" enctype="multipart/form-data">
    <div class="form-group">
      <?php if (isset($_SESSION['PROFILE-PICTURE'])): ?>
        <img id="profile-pic" src=<?php echo $user['profile_pic'] ?> alt="Profile Picture" class="rounded-circle">
      <?php else: ?>
        <img id="profile-pic" src="https://www.gstatic.com/images/branding/product/2x/avatar_square_blue_120dp.png" alt="Profile Picture" class="rounded-circle">
      <?php endif; ?>
      <label class="fa fa-camera profile-upload-btn">
        <input id="profile-pic-upload" type="file" name="profile-pic" value="Upload Profile Picture" style="display: none">
      </label>
      <small id="profile-pic-msg" style="color:red; display:none;">Upload images with size less than 1MB</small>
    </div>
    <div class="form-group">
      <input type="text" name="fname" value=<?php echo $user['fname'] ?> class="form-control" placeholder="First Name" required>
    </div>
    <div class="form-group">
      <input type="text" name="lname" value=<?php echo $user['lname'] ?> class="form-control" placeholder="Last Name" required>
    </div>
    <div class="form-group">
      <input type="text" name="email" value=<?php echo $user['email'] ?> class="form-control" placeholder="Email ID" required>
    </div>
    <input type="submit" class="btn btn-primary" name="update-profile" value="Update Profile">
  </form>

  <form action="../includes/profile.inc.php" method="post">
    <div class="form-group">
      <input type="password" id="npwd" name="npwd" class="form-control" placeholder="New Password" required>
    </div>
    <div class="form-group">
      <input type="password" id="cpwd" name="cpwd" class="form-control" placeholder="Confirm Password" required>
    </div>
    <small id="password-msg" style="color:red; display:none;">Confirmation Password is Wrong</small>
    <input type="submit" class="btn btn-primary" name="update-password" value="Update Password">
  </form>
<?php else: ?>
  <?php
    if (isset($_SESSION['USERID'])) {
      header("Location: dashboard");
      return;
    } else {
      header("Location: ../quizzee");
      return;
    }
  ?>
<?php endif; ?>

<script>
  $("#profile-pic-upload").change(function() {
      let file = document.getElementById("profile-pic-upload").files;
      if (file[0].size <= 1000000) {
        let filereader = new FileReader();
        filereader.onload = function(filedata)
        {
          $("#profile-pic").attr("src", filedata.target.result);
        }
        filereader.readAsDataURL(file[0]);
      } else {
        $("#profile-pic-msg").fadeIn(function() {
          setTimeout(function() {
            $("#profile-pic-msg").fadeOut();
          }, 5000);
        });
      }
    });

  $("#cpwd").change(function() {
    if ($(this).val() != $("#npwd").val()) {
      $("#password-msg").css("display", "block");
    } else {
      $("#password-msg").css("display", "none");
    }
  })

  $("#npwd").change(function() {
    if ($(this).val() != $("#cpwd").val() && $("#cpwd").val() != "") {
      $("#password-msg").css("display", "block");
    } else {
      $("#password-msg").css("display", "none");
    }
  })
</script>
