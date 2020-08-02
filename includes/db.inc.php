<?php
  // Database Credentials
  $host = 'localhost';
  $dbname = 'Quizee';
  $usn = 'root';
  $pwd = '';

  $salt = 'test123';

  // Try to establish a connection, or die with error
  try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $usn, $pwd);
  } catch (Exception $e) {
    die("Connection to database failed");
  }
?>
