<!-- Sidebar  start-->
<?php
// Initialize the session
if (!isset($_SESSION)) {
  session_start();
}



//---------------------------------------------------
//load all of the DB Queries
$sql = "SELECT * FROM settings where id = 2";
$Recordset1 = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);
//-------------------------------------------------------

$currency = $row_Recordset1['value'];



if (isset($_SESSION['loggedin'])) {
  $_SESSION['loginmenu'] = '

                <li class="nav-item">
                <a href="./logout.php" class="nav-link">Logout</a>
                </li><li class="nav-item">
                <a href="./account_home.php" class="fa-solid fa-user"></a>
                </li>';
} else {
  $_SESSION['loginmenu'] = '<li class="nav-item">
                <a href="./login.php" class="nav-link">Login</a>
                </li>';
}
$main_menus = '
<nav class="navbar">
<a href="./index.php" ><img class="logopos" src="./img/logo.png" height="95px"></a>

            <ul class="nav-menu">
                <li class="nav-item">
                <a href="./string-jobs.php" class="nav-link">Jobs</a>
                </li>
                
                <li class="nav-item">
                <a href="./customers.php" class="nav-link">Customers</a>
                </li>
                <li class="nav-item">
                <a href="./string.php" class="nav-link">String</a>
                </li>
                <li class="nav-item">
                <a href="./string-im.php" class="nav-link">IMS</a>
                </li>
                <li class="nav-item">
                <a href="./rackets.php" class="nav-link">Rackets</a>
                </li>
                <li class="nav-item">
                <a href="./knots.php" class="nav-link">Knots</a>
                </li>
                 <li class="nav-item">
                <a href="./site-users.php" class="nav-link">Settings</a>
                </li>'
  . $_SESSION['loginmenu'] . '       
                
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </nav>';
?>