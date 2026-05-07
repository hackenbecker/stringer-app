<?php
require_once('./Connections/wcba.php');
require_once('./menu.php');

// Initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// SECURITY: Generate a CSRF token if one doesn't exist
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['loggedin'])) {
  header('Location: ./login.php');
  exit;
}
if ($_SESSION['level'] != 1) {
  header('Location: ./nopermission.php');
  exit;
}
if (isset($_POST['submitclearmessage'])) {
  unset($_SESSION['message']);
}

// SECURITY Helper: Escape output to prevent XSS
function e($string)
{
  return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");

// PERFORMANCE OPTIMIZATION: 
// Combine the 15 individual settings queries into ONE single query.
$settingsData = [];
$query_settings = "SELECT id, value FROM settings";
$result_settings = mysqli_query($conn, $query_settings) or die(mysqli_error($conn));
while ($row = mysqli_fetch_assoc($result_settings)) {
  $settingsData[$row['id']] = $row['value'];
}

// Map the old variables to the new unified array
$currency_val   = $settingsData['2'] ?? 1;
$units_val      = $settingsData['3'] ?? 'ft';
$weight         = $settingsData['21'] ?? 'lbs';
$maxtension     = ($weight == "kg") ? 35 : 70;

// PERFORMANCE OPTIMIZATION: Use COUNT() and SUM() instead of SELECT *
$query_Recordset6 = "SELECT COUNT(job_id) as total FROM stringjobs WHERE collection_date LIKE '___" . $current_month_numeric . "/" . $current_year . "%'";
$totalRows_Recordset6 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset6))['total'] ?? 0;

$query_Recordset7 = "SELECT COUNT(job_id) as total FROM stringjobs";
$totalRows_Recordset7 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset7))['total'] ?? 0;

$query_Recordset8 = "SELECT SUM(price) as SUM from stringjobs";
$sum = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset8))['SUM'] ?? 0;

$query_Recordset9 = "SELECT SUM(price) as SUM from stringjobs WHERE paid != '1'";
$sum_owed = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset9))['SUM'] ?? 0;
$_SESSION['sum_owed'] = $sum_owed;

// Fetch Grip (Just the first one used for the modal)
$query_Recordset2 = "SELECT * FROM grip ORDER BY gripid ASC LIMIT 1";
$row_Recordset2 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset2));

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="./css/style.css">

  <title>SDBA - Settings</title>
  <link rel="icon" type="image/png" href="./img/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="./img/favicon-16x16.png" sizes="16x16" />
</head>

<body data-spy="scroll" data-target="#main-nav">

  <script>
    if (localStorage.getItem('themeSwitch') === 'dark') {
      document.body.setAttribute('data-theme', 'dark');
    }
  </script>

  <?php echo $main_menus; ?>

  <div>
    <div class="home-section diva">
      <div class="subheader"></div>
      <p class="fxdtextb"><strong>SETTINGS &</strong> Accounts</p>
      <div class="container">

        <div class="row firstparavp text-center" style="margin-top:40px">
          <div class="col-6">
            <button class="btn button-colours-settings btn-block" data-toggle="modal" data-target="#currencyModal">Currency: <?php echo e($currency); ?></button>
          </div>
          <div class="col-6">
            <button class="btn button-colours-settings btn-block" data-toggle="modal" data-target="#unitsModal">Units: <?php echo e($units_val) . " / " . e($weight); ?></button>
          </div>
        </div>

        <div class="row text-center mt-2">
          <div class="col-6">
            <button class="btn button-colours-settings btn-block" data-toggle="modal" data-target="#gripModal">Grip: <?php echo e($currency) . e($row_Recordset2['Price']); ?></button>
          </div>
          <div class="col-6">
            <a class="btn button-colours-settings btn-block" href="./string-im.php">In Market String</a>
          </div>
        </div>

        <div class="row text-center mt-2">
          <div class="col-6">
            <a class="btn button-colours-settings btn-block" href="./reel-lengths.php">Reel Lengths</a>
          </div>
          <div class="col-6">
            <a class="btn button-colours-settings btn-block" href="./site-users.php">User Accounts</a>
          </div>
        </div>

        <div class="row text-center mt-2">
          <div class="col-6">
            <a class="btn button-colours-settings btn-block" href="./sports.php">Sports</a>
          </div>
          <div class="col-6">
            <button class="btn button-colours-settings btn-block" data-toggle="modal" data-target="#accModal">Bank Account Details:</button>
          </div>
        </div>

        <div class="row text-center mt-2">
          <div class="col-6">
            <button class="btn button-colours-settings btn-block" data-toggle="modal" data-target="#dbModal">Reset Database:</button>
          </div>
          <div class="col-6">
            <button class="btn button-colours-settings btn-block" data-toggle="modal" data-target="#domModal">Domain name:</button>
          </div>
        </div>

        <div class="row text-center mt-2">
          <div class="col-6">
            <input type="checkbox" id="themeSwitch" class="d-none">
            <label class="py-2 rounded button-colours-settings btn-block mb-0" for="themeSwitch" style="cursor: pointer;">Toggle Theme</label>
          </div>
          <div class="col-6">
            <button class="btn button-colours-settings btn-block" data-toggle="modal" data-target="#compModal">Company details:</button>
          </div>
        </div>

      </div>
    </div>

    <div class="modal fade" id="gripModal">
      <div class="modal-dialog">
        <div class="modal-content border radius">
          <div class="modal-header modal_header">
            <h5 class="modal-title">Edit Grip</h5>
            <button class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body modal_body">
            <form method="post" action="./db-update.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
              <label>Description</label>
              <input type="text" name="gripname" value="<?php echo e($row_Recordset2['type']); ?>" class="form-control txtField">
              <label class="mt-2">Price</label>
              <input type="text" name="price" value="<?php echo e($row_Recordset2['Price']); ?>" class="form-control txtField">
          </div>
          <div class="modal-footer modal_footer">
            <button type="button" class="btn modal_button_cancel" data-dismiss="modal"><span>Cancel</span></button>
            <input type="hidden" name="gripid" value="<?php echo e($row_Recordset2['gripid']); ?>">
            <input class="btn modal_button_submit" type="submit" name="submiteditgrip" value="Submit">
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="compModal">
      <div class="modal-dialog">
        <div class="modal-content border radius">
          <div class="modal-header modal_header">
            <h5 class="modal-title">Edit Company details</h5>
            <button class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body modal_body">
            <form method="post" action="./db-update.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
              <label>Company Name</label>
              <input type="text" name="compname" value="<?php echo e($settingsData['12'] ?? ''); ?>" class="form-control txtField">
              <label class="mt-2">Address</label>
              <input type="text" name="address" value="<?php echo e($settingsData['13'] ?? ''); ?>" class="form-control txtField">
              <label class="mt-2">Town</label>
              <input type="text" name="town" value="<?php echo e($settingsData['14'] ?? ''); ?>" class="form-control txtField">
              <label class="mt-2">County</label>
              <input type="text" name="county" value="<?php echo e($settingsData['15'] ?? ''); ?>" class="form-control txtField">
              <label class="mt-2">Postcode</label>
              <input type="text" name="postcode" value="<?php echo e($settingsData['16'] ?? ''); ?>" class="form-control txtField">
              <label class="mt-2">Email address</label>
              <input type="text" name="email" value="<?php echo e($settingsData['17'] ?? ''); ?>" class="form-control txtField">
              <label class="mt-2">Telephone:</label>
              <input type="text" name="tel" value="<?php echo e($settingsData['18'] ?? ''); ?>" class="form-control txtField">
          </div>
          <div class="modal-footer modal_footer">
            <button type="button" class="btn modal_button_cancel" data-dismiss="modal"><span>Cancel</span></button>
            <input class="btn modal_button_submit" type="submit" name="submiteditcomp" value="Submit">
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="accModal">
      <div class="modal-dialog">
        <div class="modal-content border radius">
          <div class="modal-header modal_header">
            <h5 class="modal-title">Edit payment account</h5>
            <button class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body modal_body">
            <p>(Only required to populate the labels and invoices)</p>
            <form method="post" action="./db-update.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
              <label>Account name</label>
              <input type="text" name="accname" value="<?php echo e($settingsData['4'] ?? ''); ?>" class="form-control txtField">
              <label class="mt-2">Account Number</label>
              <input type="text" name="accnum" value="<?php echo e($settingsData['5'] ?? ''); ?>" class="form-control txtField">
              <label class="mt-2">Sort Code</label>
              <input type="text" name="scode" value="<?php echo e($settingsData['6'] ?? ''); ?>" class="form-control txtField">
          </div>
          <div class="modal-footer modal_footer">
            <button type="button" class="btn modal_button_cancel" data-dismiss="modal"><span>Cancel</span></button>
            <input class="btn modal_button_submit" type="submit" name="submiteditacc" value="Submit">
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="dbModal">
      <div class="modal-dialog">
        <div class="modal-content border radius">
          <div class="modal-header modal_header">
            <h5 class="modal-title">Reset Database connection.</h5>
            <button class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body modal_body">
            <h5>Warning: Pressing continue will erase your current database settings!</h5>
          </div>
          <div class="modal-footer modal_footer">
            <button type="button" class="btn modal_button_cancel" data-dismiss="modal"><span>Cancel</span></button>
            <a class="btn modal_button_submit" href="./db-config.php?code=1378907769354882">Continue</a>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="domModal">
      <div class="modal-dialog">
        <div class="modal-content border radius">
          <div class="modal-header modal_header">
            <h5 class="modal-title">Edit Domain</h5>
            <button class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body modal_body">
            <form method="post" action="./db-update.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
              <label>Domain name for your site</label>
              <input type="text" name="domname" value="<?php echo e($settingsData['7'] ?? ''); ?>" class="form-control txtField">
          </div>
          <div class="modal-footer modal_footer">
            <button type="button" class="btn modal_button_cancel" data-dismiss="modal"><span>Cancel</span></button>
            <input class="btn modal_button_submit" type="submit" name="submiteditdom" value="Submit">
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="unitsModal">
      <div class="modal-dialog">
        <div class="modal-content border radius">
          <div class="modal-header modal_header">
            <h5 class="modal-title">Edit units</h5>
            <button class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body modal_body">
            <form method="post" action="./db-update.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
              <label>Length Units (Will only change the symbol not the values)</label>
              <select class="form-control" name="units">
                <option value="ft" <?php if ($units_val == "ft") echo "selected"; ?>>Feet</option>
                <option value="m" <?php if ($units_val == "m") echo "selected"; ?>>Metres</option>
              </select>

              <label class="mt-3">Tension Units (set once after installation)</label>
              <select class="form-control" name="wunits">
                <option value="kg" <?php if ($weight == "kg") echo "selected"; ?>>KG</option>
                <option value="lbs" <?php if ($weight == "lbs") echo "selected"; ?>>LBS</option>
              </select>
          </div>
          <div class="modal-footer modal_footer">
            <button type="button" class="btn modal_button_cancel" data-dismiss="modal"><span>Cancel</span></button>
            <input class="btn modal_button_submit" type="submit" name="submiteditunits" value="Submit">
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="currencyModal">
      <div class="modal-dialog">
        <div class="modal-content border radius">
          <div class="modal-header modal_header">
            <h5 class="modal-title">Edit Currency<br><small>(This will only change the symbol not the values)</small></h5>
            <button class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body modal_body">
            <form method="post" action="./db-update.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
              <label>Currency</label>
              <select class="form-control" name="currency">
                <option value="1" <?php if ($currency_val == 1) echo "selected"; ?>>United States Dollars</option>
                <option value="2" <?php if ($currency_val == 2) echo "selected"; ?>>Euro</option>
                <option value="3" <?php if ($currency_val == 3) echo "selected"; ?>>United Kingdom Pounds</option>
                <option value="4" <?php if ($currency_val == 4) echo "selected"; ?>>Australia Dollars</option>
                <option value="5" <?php if ($currency_val == 5) echo "selected"; ?>>Canada Dollars</option>
                <option value="6" <?php if ($currency_val == 6) echo "selected"; ?>>China Yuan Renmimbi</option>
                <option value="7" <?php if ($currency_val == 7) echo "selected"; ?>>India Rupees</option>
                <option value="8" <?php if ($currency_val == 8) echo "selected"; ?>>Japan Yen</option>
                <option value="9" <?php if ($currency_val == 9) echo "selected"; ?>>Russia Rubles</option>
              </select>
          </div>
          <div class="modal-footer modal_footer">
            <button type="button" class="btn modal_button_cancel" data-dismiss="modal"><span>Cancel</span></button>
            <input class="btn modal_button_submit" type="submit" name="submiteditcurrency" value="Submit">
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="container center">
    <div class="p-3 row">
      <div class="col-2">
        <a href="#" type="button" class="dot fa-solid fa-plus fa-2x" data-toggle="modal" data-target="#AddUser"></a>
      </div>
      <div class="col-2">
        <h3 class="<?php echo !empty($_SESSION['message']) ? 'blinking' : 'dotb'; ?>" title="Warning Messages" data-toggle="modal" data-target="#warningModal"><strong>!</strong></h3>
      </div>
      <div class="col-2">
        <h3 class="dotbt h6 " title="Restrings for <?php echo $current_month_text; ?>"><?php echo $totalRows_Recordset6 ?></h3>
      </div>
      <div class="col-2">
        <a href="#" class="dotbt h6" title="Total restrings"><?php echo $totalRows_Recordset7 ?></a>
      </div>
      <div class="col-2">
        <a href="./jobs-unpaid.php" class="dotbt h6" title="Amount Owed"><?php echo e($currency) . e($sum_owed); ?></a>
      </div>
      <div class="col-2">
        <a href="#" class="dotbtt h7" title="Total Income"><small><?php echo e($currency) . e($sum); ?></small></a>
      </div>
    </div>
  </div>

  <div class="modal fade text-dark" id="warningModal">
    <div class="modal-dialog">
      <div class="modal-content border radius">
        <div class="modal-header modal_header">
          <h5 class="modal-title">Information:</h5>
          <button class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body modal_body">
          <div><?php echo $_SESSION['message'] ?? ''; ?></div>
        </div>
        <div class="modal-footer modal_footer">
          <div class="container mt-2" style="margin-top: 120px;">
            <div class="row pt-3">
              <div class="col-8">
                <a class="btn modal_button_cancel" href="./site-users.php">Cancel</a>
              </div>
              <div class="col-4">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                  <input class="btn modal_button_submit float-right" type="submit" name="submitclearmessage" value="Clear">
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade text-dark" id="AddUser">
    <div class="modal-dialog">
      <div class="modal-content border radius">
        <div class="modal-header modal_header">
          <h5 class="modal-title">You are adding a new user</h5>
          <button class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <form method="post" action="site-users-db.php">
          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
          <div class="modal-body modal_body">
            <div class="form-group">
              <label for="name">User Name</label>
              <input class="form-control" id="name" type="text" placeholder="Enter Username" name="username" required>
              <label class="pt-3" for="email">Email Address</label>
              <input class="form-control" id="email" type="email" placeholder="Enter Email" name="email" required>
            </div>
            <input type="hidden" name="active" value="1">
            <label for="level">Access Level</label>
            <select class="form-control" id="level" name="level">
              <option value="1">1 (Super User)</option>
              <option value="2">2 (Add jobs only)</option>
            </select>
          </div>
          <div class="modal-footer modal_footer">
            <button type="button" class="btn modal_button_cancel" data-dismiss="modal"><span>Cancel</span></button>
            <input class="btn modal_button_submit" type="submit" name="submitAdd" value="Submit">
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

  <script>
    // General UI interactions
    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.querySelector(".nav-menu");
    if (hamburger) {
      hamburger.addEventListener("click", () => {
        hamburger.classList.toggle("active");
        navMenu.classList.toggle("active");
      });
    }

    $('body').scrollspy({
      target: '#main-nav'
    });

    $("#main-nav a").on('click', function(event) {
      if (this.hash !== "") {
        event.preventDefault();
        const hash = this.hash;
        $('html, body').animate({
          scrollTop: $(hash).offset().top
        }, 800, function() {
          window.location.hash = hash;
        });
      }
    });

    // Cleaned up Theme Toggle Logic
    var themeSwitch = document.getElementById('themeSwitch');

    if (themeSwitch) {
      var darkThemeSelected = (localStorage.getItem('themeSwitch') === 'dark');
      themeSwitch.checked = darkThemeSelected;

      themeSwitch.addEventListener('change', function() {
        var logo = document.getElementById("imglogo");
        if (this.checked) {
          document.body.setAttribute('data-theme', 'dark');
          localStorage.setItem('themeSwitch', 'dark');
          if (logo) logo.src = "./img/logo-dark.png";
        } else {
          document.body.removeAttribute('data-theme');
          localStorage.removeItem('themeSwitch');
          if (logo) logo.src = "./img/logo.png";
        }
      });
    }

    // Set logo correctly on load if it exists
    var imgsrc = localStorage.getItem('themeSwitch');
    var initLogo = document.getElementById("imglogo");
    if (initLogo) {
      initLogo.src = (imgsrc === "dark") ? "./img/logo-dark.png" : "./img/logo.png";
    }
  </script>
</body>

</html>