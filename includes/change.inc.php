<?php
  session_start();
  if (isset($_POST['upload'])) {
    $file = $_FILES['profile_pic'];
    print_r($file);

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
          $file_dest_addr = 'Resources/ProfilePictures/'.md5(uniqid('', true)).'.'.$file_ext;

          // Update the database
          require_once "db.inc.php";

          $update_query = $conn->prepare("UPDATE Users
                                          SET profile_pic=:profile_pic
                                          WHERE uid=:uid");
          $result = $update_query->execute(array(
            ':profile_pic' => $file_dest_addr,
            ':uid' => $_SESSION['USERID']
          ));

          if ($result == true) {
            // Move the file
            move_uploaded_file($file_temp_addr, '../'.$file_dest_addr);

            $_SESSION['SUCCESS'] = "Updated Profile Picture";
            $_SESSION['PROFILE-PICTURE'] = $file_dest_addr;
            header("Location: ../index.php");
            return;
          } else {
            $_SESSION['ERROR'] = "Unable to upload Profile Picture!";
            header("Location: ../index.php");
            return;
          }
        } else {
          $_SESSION['ERROR'] = "Please upload images within 1 MB!";
          header("Location: ../index.php");
          return;
        }
      } else {
        $_SESSION['ERROR'] = "Error uploading image.";
        header("Location: ../index.php");
        return;
      }
    } else {
      $_SESSION['ERROR'] = "Wrong image type.";
      header("Location: ../index.php?type=".$file_name);
      return;
    }

  } else {
    header("Location: ../index.php");
    return;
  }
?>
