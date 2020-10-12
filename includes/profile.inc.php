<?php
  session_start();

  if (isset($_SESSION['USERID'])) {
    require_once "db.inc.php";
    if (isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['email'])) {
      // Edit profile
      if (!empty($_POST['fname']) && !empty($_POST['lname']) && !empty($_POST['email'])) {
        if ($_FILES['profile-pic']['error'] === 0) {
          // Change profile picture also
          $file = $_FILES['profile-pic'];

          // File attributes
          $file_name = $file['name'];
          $file_temp_addr = $file['tmp_name'];
          $file_size = $file['size'];
          $file_error = $file['error'];
          $file_type = $file['type'];

          // Processing
          $file_ext = strtolower(end(explode('.', $file_name)));
          $allowed_file_types = array("jpg", "jpeg", "png");

          if (in_array($file_ext, $allowed_file_types)) {
            if ($file_error === 0) {
              // Maximum upload file size is 1 MB
              if ($file_size < 1000000) {
                $file_dest_addr = '../Resources/ProfilePictures/'.md5($file_name.uniqid('', true)).'.'.$file_ext;

                $update_query = $conn->prepare("UPDATE Users
                                                SET fname=:fname,
                                                    lname=:lname,
                                                    email=:email,
                                                    profile_pic=:profile_pic
                                                WHERE uid=:uid");
                $result = $update_query->execute(array(
                  ':fname' => $_POST['fname'],
                  ':lname' => $_POST['lname'],
                  ':email' => $_POST['email'],
                  ':profile_pic' => $file_dest_addr,
                  ':uid' => $_SESSION['USERID']
                ));

                if ($result == true) {
                  // Move the file
                  move_uploaded_file($file_temp_addr, $file_dest_addr);

                  $_SESSION['PROFILE-PICTURE'] = $file_dest_addr;
                  $_SESSION['NAME'] = $_POST['fname']." ".$_POST['lname'];
                }

                header("Location: ../my/profile");
                return;
              }
            }
          }
        } else {
          // No need to change profile picture
          $update_query = $conn->prepare("UPDATE Users
                                          SET fname=:fname,
                                              lname=:lname,
                                              email=:email
                                          WHERE uid=:uid");
          $result = $update_query->execute(array(
            ':fname' => $_POST['fname'],
            ':lname' => $_POST['lname'],
            ':email' => $_POST['email'],
            ':uid' => $_SESSION['USERID']
          ));

          $_SESSION['NAME'] = $_POST['fname']." ".$_POST['lname'];
          header("Location: ../my/profile");
          return;
        }
      } else {
        // Inputs are empty
        header("Location: ../my/profile");
        return;
      }
    } elseif (isset($_POST['npwd']) && isset($_POST['cpwd'])) {
        // Change password
        if (!empty($_POST['npwd']) && !empty($_POST['cpwd']) && $_POST['npwd'] == $_POST['cpwd']) {
          $update_query = $conn->prepare("UPDATE Users
                                          SET pwd=:pwd
                                          WHERE uid=:uid");
          $result = $update_query->execute(array(
            ':pwd' => hash('sha256', $salt.$_POST['npwd']),
            ':uid' => $_SESSION['USERID']
          ));

          header("Location: ../my/profile");
          return;
        } else {
          header("Location: ../my/profile");
          return;
        }
    } else {
      // Go back to the main page
      header("Location: ../my/dashboard");
      return;
    }
  } else {
    header("Location: ../quizzee");
    return;
  }
?>
