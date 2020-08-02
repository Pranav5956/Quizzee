<?php
  require_once "autoload.php";

  // Creating the google api client
  $google_client = new Google_Client();

  // Setting the credentials and other settings
  $google_client->setClientId("183632463982-d7p45iv3b7k1n1f6jqf4k8kupp6akq5o.apps.googleusercontent.com");
  $google_client->setClientSecret("KQhQtEAL96wcuEWte4lGLaMI");
  $google_client->setRedirectUri("http://localhost/OnlineQuizManagement/includes/login.inc.php");

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

      $exists_query = $conn->prepare("SELECT * FROM Users WHERE email=:email");
      $exists_query->execute(array(
        ":email" => $email
      ));

      if ($exists_query->rowCount() > 0) {
        $_SESSION['userId'] = $exists_query->fetch(PDO::FETCH_ASSOC)['uid'];
        $_SESSION['name'] = $fname." ".$lname;
        $_SESSION['pic'] = $data['picture'];
        $_SESSION['success'] = "Successfully Logged in!";
        header("Location: ../index.php");
        return;
      } else {
        $create_query = $conn->prepare("INSERT INTO Users(fname, lname, email, pwd, login)
                                        VALUES(:fname, :lname, :email, NULL, :login)");
        $create_query->execute(array(
          ":fname" => $fname,
          ":lname" => $lname,
          ":email" => $email,
          ":login" => "GOOGLE"
        ));
      }
      $_SESSION['pic'] = $data['picture'];
      $_SESSION['userId'] = $conn->lastInsertId();
      $_SESSION['name'] = $fname." ".$lname;
      $_SESSION['success'] = "Successfully Logged in!";
      header("Location: ../index.php");
      return;
    } else {
      $_SESSION['error'] = "Cannot login with Google Account!";
      header("Location: ../login.php");
      return;
    }
  }
?>
