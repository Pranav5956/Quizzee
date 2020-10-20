<?php
  require_once "autoload.php";

  // Creating the google api client
  $CLIENT_ID = "183632463982-d7p45iv3b7k1n1f6jqf4k8kupp6akq5o.apps.googleusercontent.com";
  $CLIENT_SECRET = "KQhQtEAL96wcuEWte4lGLaMI";
  $google_client = new Google_Client();

  // Setting the credentials and other settings
  $google_client->setClientId($CLIENT_ID);
  $google_client->setClientSecret($CLIENT_SECRET);
  $google_client->setRedirectUri("http://localhost/Quizzee/includes/login.inc.php");
  unset($CLIENT_ID);
  unset($CLIENT_SECRET);

  // Requirements
  $google_client->addScope('email');
  $google_client->addScope('profile');

  function google_logout() {
    global $google_client;
    try {
      // Revoke the existing access token on logout
      $google_client->revokeToken();
      return true;
    } catch (Exception $e) {
      // No access token exists or other exceptions
      return false;
    }
  }

  function google_login($conn) {
    // Check for account creation
    global $google_client;
    $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
      $google_client->setAccessToken($token['access_token']);

      $google_service = new Google_Service_Oauth2($google_client);

      $data = $google_service->userinfo->get();
      // Store the values from the data
      $fname = $data['given_name'];
      $lname = $data['family_name'];
      $email = $data['email'];
      $profilepic = $data['picture'];

      $exists_query = $conn->prepare("SELECT * FROM Users WHERE email=:email AND login='GOOGLE'");
      $exists_query->execute(array(
        ":email" => $email
      ));

      if ($exists_query->rowCount() > 0) {
        $_SESSION['USERID'] = $exists_query->fetch(PDO::FETCH_ASSOC)['uid'];
        $_SESSION['NAME'] = $fname." ".$lname;
        $_SESSION['NAME_URL'] = str_replace(' ', '', $_SESSION['NAME']);

        $_SESSION['PROFILE-PICTURE'] = $profilepic;
        $_SESSION['TYPE'] = 'GOOGLE';
        $_SESSION['SUCCESS'] = "Logged in!";
        header("Location: ../my/dashboard");
        return;
      } else {
        // Create the user in the database
        $create_query = $conn->prepare("INSERT INTO Users(uuid, fname, lname, email, pwd, login, profile_pic)
                                        VALUES(:uuid, :fname, :lname, :email, NULL, :login, NULL)");
        $create_query->execute(array(
          ":uuid" => 'U'.hash('crc32', $fname.$lname.$email.'GOOGLE'),
          ":fname" => $fname,
          ":lname" => $lname,
          ":email" => $email,
          ":login" => "GOOGLE"
        ));
      }

      // Set user credentials
      $_SESSION['USERID'] = $conn->lastInsertId();
      $_SESSION['NAME'] = $fname." ".$lname;
      $_SESSION['NAME_URL'] = str_replace(' ', '', $_SESSION['NAME']);
      $_SESSION['PROFILE-PICTURE'] = $profilepic;
      $_SESSION['TYPE'] = 'GOOGLE';
      $_SESSION['SUCCESS'] = "Successfully Logged in!";
      header("Location: ../my/dashboard");
      return;
    } else {
      $_SESSION['ERROR'] = "Cannot login with Google Account!";
      header("Location: ../login");
      return;
    }
  }
?>
