<?php require_once('./Connections/wcba.php');
//-------------------------------------------------------------------
// Initialize the session
if (!isset($_SESSION)) {
  session_start();
}
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
  header('Location: information.html');
  exit;
}
$_SESSION['message'] = ""; //clear the status message
$marker = $_POST['marker'];
if ($marker == 1) {
  $location = "./account_home.php";
} else {
  $location = "./site-users.php";
}
//-----------------------------------------------------------------------
//------------------Section to update password ---------------------------
//-----------------------------------------------------------------------
if (isset($_POST['submitPass'])) {
  //lets check the password is strong enough
  $_SESSION['password1'] = $_POST['password1'];
  $_SESSION['password2'] = $_POST['password2'];
  $password = $_POST['password1'];
  //password string to check against
  $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
  //lets error check first: do the passwords match and validation
  //---------------------------------------------------
  if ($_POST['password1'] != $_POST['password2']) {
    $_SESSION['message'] = "Passwords do not match";
    header("location:$location"); //Redirecting To the main page
  } else {
    if (preg_match($pattern, $password)) {
      $hashedpass = password_hash($password, PASSWORD_DEFAULT);
      $sql = "UPDATE accounts set password='" . $hashedpass . "'WHERE id='" . $_POST['refedit'] . "'";
      mysqli_query($conn, $sql);
      $_SESSION['message'] = "Passsord updated successfully";
      unset($_SESSION['password1']);
      unset($_SESSION['password2']);
      header("location:site-users.php"); //Redirecting To the main page
      exit;
    } else {
      $_SESSION['message'] = "Password is invalid";
      header("location:$location"); //Redirecting To the main page
    }
  }
}
//-----------------------------------------------------------------------
//------------------Section to update user details ---------------------------
//-----------------------------------------------------------------------
if (isset($_POST['submitEdit'])) {
  //lets error check first: does the name already exist?
  //---------------------------------------------------
  $sql = "SELECT * FROM accounts WHERE username = '" . $_POST['username'] . "'";
  $Recordset1 = mysqli_query($conn, $sql) or die(mysqli_error($conn));
  $row = mysqli_fetch_assoc($Recordset1);
  $totalRows_Recordset1 = mysqli_num_rows($Recordset1);
  if (($totalRows_Recordset1 > 0) && ($_POST['refedit'] != $row['id'])) {
    $_SESSION['message'] = "Failed to modify user: Name already exists";
    header("location:$location"); //Redirecting To the main page
  } else {
    if ($_POST['active'] == '1') {
      $active = "1";
    } else {
      $active = "0";
    }
    $sql = "UPDATE accounts set
    username = '" . $_POST['username'] . "',
    level ='" . $_POST['level'] . "',
    email = '" . $_POST['email'] . "',
    active ='" . $_POST['active'] . "'
    WHERE id='" . $_POST['refedit'] . "'";
    mysqli_query($conn, $sql);
    $_SESSION['message'] = "User modified Successfully";
    header("location:$location"); //Redirecting To the main page
    exit;
  }
}
//-----------------------------------------------------------------------
//------------------Section to delete from table---------------------------
//-----------------------------------------------------------------------
if (isset($_POST['submitDel'])) {
  $sql = "DELETE FROM accounts WHERE id='" . $_POST['refdel'] . "'";
  mysqli_query($conn, $sql);
  $_SESSION['message'] = "User deleted Successfully";
  header("location:$location"); //Redirecting To the main page
  exit;
}
//-----------------------------------------------------------------------
//------------------Section to add to Db table---------------------------
//-----------------------------------------------------------------------
if (isset($_POST['submitAdd'])) {
  //lets error check first: does the name already exist?
  //---------------------------------------------------
  $sql = "SELECT * FROM accounts WHERE username = '" . $_POST['username'] . "'";
  $Recordset1 = mysqli_query($conn, $sql) or die(mysqli_error($conn));
  $totalRows_Recordset1 = mysqli_num_rows($Recordset1);
  if ($totalRows_Recordset1 > 0) {
    $_SESSION['message'] = "Failed to add user: Name already exists";
    header("location:site-users.php"); //Redirecting To the main page
  } else {
    $sql = "INSERT INTO accounts (username, level, email, active) VALUES ('"
      . $_POST['username'] . "', '"
      . $_POST['level'] . "', '"
      . $_POST['email'] . "', '"
      . "1')";
    mysqli_query($conn, $sql);
    $_SESSION['message'] = "User added Successfully";
    header("location:$location"); //Redirecting To the main page
    exit;
  }
}
