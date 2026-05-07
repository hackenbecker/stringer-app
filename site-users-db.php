<?php
require_once('./Connections/wcba.php');

// Initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
  header('Location: login.php'); // Fixed incorrect redirect to information.html
  exit;
}

// SECURITY: CSRF Validation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF Token Validation Failed.");
  }
}

$_SESSION['message'] = ""; //clear the status message
$marker = isset($_POST['marker']) ? (int)$_POST['marker'] : 2;

if ($marker == 1) {
  $location = "./account_home.php";
} else {
  $location = "./site-users.php";
}

//-----------------------------------------------------------------------
//------------------Section to update password --------------------------
//-----------------------------------------------------------------------
if (isset($_POST['submitPass'])) {
  //lets check the password is strong enough
  $password = $_POST['password1'];

  //password string to check against
  $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

  //lets error check first: do the passwords match and validation
  if ($_POST['password1'] != $_POST['password2']) {
    $_SESSION['message'] = "Passwords do not match";
    header("location:$location");
    exit;
  } else {
    if (!preg_match($pattern, $password)) {
      $_SESSION['message'] = "Password is not strong enough";
      header("location:$location");
      exit;
    } else {
      // SECURITY: Protect against SQL injection
      $safe_password = mysqli_real_escape_string($conn, $password);
      $safe_id = (int)$_POST['id'];

      $sql = "UPDATE accounts SET password='$safe_password' WHERE id='$safe_id'";
      mysqli_query($conn, $sql);
      $_SESSION['message'] = "Password Updated Successfully";
      header("location:$location");
      exit;
    }
  }
}

//-----------------------------------------------------------------------
//------------------Section to delete user ------------------------------
//-----------------------------------------------------------------------
if (isset($_POST['submitDel'])) {
  // SECURITY: Ensure ID is an integer
  $safe_id = (int)$_POST['refdel'];

  $sql = "DELETE FROM accounts WHERE id='$safe_id'";
  mysqli_query($conn, $sql);
  $_SESSION['message'] = "User deleted Successfully";
  header("location:$location");
  exit;
}

//-----------------------------------------------------------------------
//------------------Section to add to Db table---------------------------
//-----------------------------------------------------------------------
if (isset($_POST['submitAdd'])) {
  // SECURITY: Protect against SQL Injection
  $safe_username = mysqli_real_escape_string($conn, trim($_POST['username']));
  $safe_email = mysqli_real_escape_string($conn, trim($_POST['email']));
  $safe_level = (int)$_POST['level'];

  //lets error check first: does the name already exist?
  $sql = "SELECT * FROM accounts WHERE username = '$safe_username'";
  $Recordset1 = mysqli_query($conn, $sql) or die(mysqli_error($conn));
  $totalRows_Recordset1 = mysqli_num_rows($Recordset1);

  if ($totalRows_Recordset1 > 0) {
    $_SESSION['message'] = "Failed to add user: Name already exists";
    header("location:site-users.php");
    exit;
  } else {
    $sql = "INSERT INTO accounts (username, level, email, active) VALUES ('$safe_username', '$safe_level', '$safe_email', '1')";
    mysqli_query($conn, $sql);
    $_SESSION['message'] = "User added Successfully";
    header("location:site-users.php");
    exit;
  }
}
