<!-- Sidebar  start-->

<?php
// Initialize the session
if (!isset($_SESSION)) {
  session_start();
}

if (isset($_SESSION['loggedin'])) {
  $_SESSION['loginmenu'] = '<li class="nav-item">
                <a href="./logout.php" class="nav-link">Logout</a>
                </li>';
} else {
  $_SESSION['loginmenu'] = '<li class="nav-item">
                <a href="./login.php" class="nav-link">Login</a>
                </li>';
}
$main_menus = '
<nav class="navbar">
<a href="./string-jobs.php" ><img class="logopos" src="./img/logo.png" height="95px"></a>
            <ul class="nav-menu">
                <li class="nav-item">
                <a href="./string-jobs.php" class="nav-link">Home</a>
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
                <a href="./site-users.php" class="nav-link">User Accounts</a>
                </li>' . $_SESSION['loginmenu'] . '       
                <li class="nav-item dropdown">
                <a href="information.php" class="nav-link dropdown-toggle" data-toggle="dropdown">Information</a>
                <ul class="dropdown-menu">
                  <a href="./knots.php" class="dropdown-item">Knots</a>
                  <a href="./information.php" class="dropdown-item">Information</a>
                </ul>
              </li>

            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </nav>';
?>