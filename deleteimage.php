<?php require_once('./Connections/wcba.php');
//-------------------------------------------------------------------
// Initialize the session
if (!isset($_SESSION)) {
  session_start();
}
if (!isset($_SESSION['loggedin'])) {
  header('Location: ./login.php');
  exit;
}
if ($_SESSION['level'] < 1) {
  header('Location: ./nopermission.php');
  exit;
}
//----------------------------------------------------------------
//------------Section to delete a sport from DB---------------
//----------------------------------------------------------------
if (!empty($_GET['imageid'])) {
  mysqli_query($conn, "DELETE FROM images 
  WHERE id='" . $_GET['imageid'] . "'");
  $_SESSION['message'] = "Image deleted Successfully";
  $location = "./editjob.php?jobid=" . $_GET['jobid'];
  //redirect back to the main page.
  header("location:$location"); //Redirecting To the main page
}
