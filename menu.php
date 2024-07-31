<?php
// Initialize the session
if (!isset($_SESSION)) {
  session_start();
}
if ((filesize("./Connections/wcba.php")) == 0) {
  header("location:./db-config.php");
}
//---------------------------------------------------
$sql = "SELECT * FROM settings where id = 3";
$Recordset2 = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$row_Recordset2 = mysqli_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);
$units = $row_Recordset2['value'];
//-------------------------------------------------------
//load all of the DB Queries
$sql = "SELECT * FROM settings where id = 2";
$Recordset1 = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);
//-------------------------------------------------------
switch ($row_Recordset1['value']) {
  case "1":
    $currency = "$";
    break;
  case "2":
    $currency = "€";
    break;
  case "3":
    $currency = "£";
    break;
  case "4":
    $currency = "$";
    break;
  case "5":
    $currency = "$";
    break;
  case "6":
    $currency = "元";
    break;
  case "7":
    $currency = "₹";
    break;
  case "8":
    $currency = "¥";
    break;
  case "9":
    $currency = "₽";
    break;
  default:
    $currency = "£";
}
if (isset($_SESSION['loggedin'])) {
  $_SESSION['loginmenu'] = '
                <li class="nav-item">
                <a href="./logout.php" class="nav-link">Logout</a>
                </li>
                <li class="nav-item">
                <a href="./account_home.php" class="fa-solid fa-user"></a>
                </li>
                <li>&nbsp</li>';
} else {
  $_SESSION['loginmenu'] = '<li class="nav-item">
                <a href="./login.php" class="nav-link">Login</a>
                </li>';
}
$main_menus = '
<nav class="navbar">
<a href="./index.php" ><img class="logopos" src="./img/logo.png" height="95px" id="imglogo"></a>
            <ul class="nav-menu">
            <li>&nbsp</li>
                <li class="nav-item">
                <a href="./string-jobs.php" class="nav-link">Jobs</a>
                </li>
                               <li class="nav-item">
                <a href="./customers.php" class="nav-link">Customers</a>
                </li>
                <li class="nav-item">
                <a href="./string.php" class="nav-link">Stock String</a>
                </li>
              
                <li class="nav-item">
                <a href="./rackets.php" class="nav-link">Rackets</a>
                </li>
                
                <li class="nav-item">
                <a href="./help.php" class="nav-link">Help</a>
                </li>
                 <li class="nav-item">
                <a href="./settings.php" class="nav-link">Settings</a>
                </li>'
  . $_SESSION['loginmenu'] . '       
                 <li><div class="custom-control custom-switch ml-3">
  <input type="checkbox" class="custom-control-input switch_input" id="themeSwitch">
  <label class="custom-control-label" for="themeSwitch">Mode</label>
</div></li>
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </nav>';
