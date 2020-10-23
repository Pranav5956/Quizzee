<?php
  session_start();
  // Database connection
  require_once "db.inc.php";

  if (isset($_POST['signup-submit'])) {
    if (empty($_POST['first-name']) || empty($_POST['last-name']) ||
        empty($_POST['email']) || empty($_POST['password']) ||
        empty($_POST['confirm-password'])) {
      // Fill all fields - rollback nothing
      $_SESSION['ERROR'] = "Fill all fields";
      header("Location: ../signup");
      return;
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
      // Invalid email - rollback first-name and last-name
      $_SESSION['ERROR'] = "Invalid email";
      $params = "fname=".urlencode($_POST['first-name']).
                "&lname=".urlencode($_POST['last-name']);
      header("Location: ../signup?".$params);
      return;
    } elseif ($_POST['confirm-password'] != $_POST['password']) {
      // Wrong confirmation password - rollback first-name, last-name and email
      $_SESSION['ERROR'] = "Wrong password confirmation";
      $params = "fname=".urlencode($_POST['first-name']).
                "&lname=".urlencode($_POST['last-name']).
                "&email=".urlencode($_POST['email']);
      header("Location: ../signup?".$params);
      return;
    } else {
      try {
        // Check for existing user with same email Address
        $exists_query = $conn->prepare("SELECT * FROM Users WHERE email=:email AND login='LOGIN'");
        $exists_query->execute(array(
          ":email" => $_POST['email']
        ));

        if ($exists_query->rowCount() > 0) {
          $_SESSION['ERROR'] = "There is already an account with the same Email ID";
          header("Location: ../signup");
          return;
        } else {
          // Create a new Users entry in the database
          $uuid = 'U'.hash('crc32', $_POST['first-name'].$_POST['last-name'].$_POST['email'].$_POST['password'].'LOGIN');
          $signin_query = $conn->prepare("INSERT INTO Users(uuid, fname, lname, email, pwd, login, profile_pic)
                                          VALUES(:uuid, :fname, :lname, :email, :pwd, :login, NULL)");
          $signin_query->execute(array(
            ':uuid' => $uuid,
            ':fname' => $_POST['first-name'],
            ':lname' => $_POST['last-name'],
            ':email' => $_POST['email'],
            ':pwd' => hash('sha256', $salt.$_POST['password']),
            ':login' => 'LOGIN'
          ));

          // Success - sign in user
          $_SESSION['USERID'] = $conn->lastInsertId();
          $_SESSION['NAME'] = $_POST['first-name']." ".$_POST['last-name'];
          $_SESSION['TYPE'] = 'LOGIN';
          $_SESSION['SUCCESS'] = "Signed in successfully!";
          $_SESSION['UUID'] = $uuid;
          header("Location: ../my/dashboard");
          return;
        }
      } catch (Exception $e) {
        // Failure - redirect the user
        $_SESSION['ERROR'] = "Signup failure!";
        header("Location: ../signup");
        return;
      }
    }
  } elseif (isset($_GET['code'])) {
    // Google authentication
    require_once "../vendor/config.php";
    google_login($conn);
  } else {
    // User not logged in - redirect to landing page
    header("Location: ../quizzee");
    return;
  }
?>
