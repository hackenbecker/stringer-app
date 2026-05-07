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

$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");

if (isset($_POST['submitclearmessage'])) {
  unset($_SESSION['message']);
}

// SECURITY: Cast variables to integers to prevent XSS / SQL Injection
$marker = isset($_GET['marker']) ? (int)$_GET['marker'] : 1;
$sportid = isset($_POST['sportid']) ? (int)$_POST['sportid'] : 0;
$customerid = isset($_POST['customerid']) ? (int)$_POST['customerid'] : 0;

$sportname = "";
$totalRows_Recordset4 = 0;
$totalRows_Recordset5 = 0;

if ($sportid > 0) {
  // Fetch Rackets
  $query_Recordset4 = "SELECT racketid, manuf, model FROM rackets WHERE sport = '$sportid' ORDER BY manuf ASC;";
  $Recordset4 = mysqli_query($conn, $query_Recordset4) or die(mysqli_error($conn));
  $totalRows_Recordset4 = mysqli_num_rows($Recordset4);

  // Fetch Strings (Excluding "String Generic")
  $query_Recordset5 = "SELECT string_id, brand, type FROM all_string WHERE sportid = '$sportid' AND brand != 'String Generic' AND type != 'String Generic' ORDER BY brand ASC";
  $Recordset5 = mysqli_query($conn, $query_Recordset5) or die(mysqli_error($conn));
  $totalRows_Recordset5 = mysqli_num_rows($Recordset5);

  // Fetch Sport Name
  $query_Recordset14 = "SELECT sportname FROM sport WHERE sportid = '$sportid' ORDER BY sportname ASC;";
  $Recordset14 = mysqli_query($conn, $query_Recordset14) or die(mysqli_error($conn));
  $sportname = mysqli_fetch_assoc($Recordset14)['sportname'] ?? '';
}

// Fetch all sports for the initial dropdown
$query_Recordset13 = "SELECT sportid, sportname FROM sport ORDER BY sportname ASC;";
$Recordset13 = mysqli_query($conn, $query_Recordset13) or die(mysqli_error($conn));
$totalRows_Recordset13 = mysqli_num_rows($Recordset13);

// PERFORMANCE: Optimized Counting Queries
$query_Recordset6 = "SELECT COUNT(job_id) as total FROM stringjobs WHERE collection_date LIKE '___" . $current_month_numeric . "/" . $current_year . "%'";
$totalRows_Recordset6 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset6))['total'] ?? 0;

$query_Recordset7 = "SELECT COUNT(job_id) as total FROM stringjobs";
$totalRows_Recordset7 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset7))['total'] ?? 0;

$query_Recordset8 = "SELECT SUM(price) as SUM from stringjobs";
$sum = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset8))['SUM'] ?? 0;

$query_Recordset9 = "SELECT SUM(price) as SUM from stringjobs WHERE paid != '1'";
$sum_owed = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset9))['SUM'] ?? 0;
$_SESSION['sum_owed'] = $sum_owed;

// Settings logic
$query_Recordset15 = "SELECT value FROM settings WHERE id = '21';";
$Recordset15 = mysqli_query($conn, $query_Recordset15) or die(mysqli_error($conn));
$weight = mysqli_fetch_assoc($Recordset15)['value'] ?? 'lbs';
$maxtension = ($weight == "kg") ? 35 : 70;

// SECURITY Helper: Escape output to prevent XSS
function e($string)
{
  return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
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
  <title>SDBA - Add Customer</title>
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

  <div class="home-section diva">
    <div class="subheader"> </div>
    <p class="fxdtextb"><strong>ADD</strong> Customer</p>

    <div class="container my-1 firstparaaltej">
      <div class="container my-1 pb-3 px-1 firstparaej">
        <div class="container px-1 pt-3 form-text">
          <div class="card cardvp" style="margin-top: 75px;">
            <div class="card-body">
              <div class="mt-3 container">
                <div class="row">
                  <?php if ($sportid == 0) { ?>
                    <div class="mt-3 col-12">
                      <div class="form-group">
                        <form method="post" action="">
                          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                          <select class="form-control input-sm" style="width:100%" name="sportid" onchange="this.form.submit()">
                            <option value="0">Select primary sport for new customer</option>
                            <?php while ($row_Recordset13 = mysqli_fetch_assoc($Recordset13)) { ?>
                              <option value="<?php echo e($row_Recordset13['sportid']); ?>">
                                <?php echo e($row_Recordset13['sportname']); ?>
                              </option>
                            <?php } ?>
                          </select>
                          <input type="hidden" name="customerid" class="txtField" value="<?php echo e($customerid); ?>">
                        </form>
                      </div>
                    </div>
                  <?php } else { ?>
                    <div class="col-10">
                      <div class="form-group">
                        <h5 class='form-text'>Sport: <?php echo e($sportname); ?></h5>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>

          <?php if ($sportid > 0) { ?>
            <form method="post" action="./db-update.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

              <div class="container mt-3">
                <div class="row pt-3">
                  <div class="col-6">
                    <input class="btn button-colours" type="submit" name="submitaddcust" value="Submit">
                  </div>
                  <div class="col-6">
                    <a class="btn button-colours-alt float-right" href="./customers.php">Cancel</a>
                  </div>
                </div>
              </div>

              <div class="card cardvp my-3">
                <div class="card-body">
                  <label>Customer Name</label>
                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <input type="text" name="customername" class="form-control txtField" required>
                      </div>
                    </div>
                  </div>

                  <label class="mt-3">Email</label>
                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <input type="email" name="customeremail" class="form-control txtField">
                      </div>
                    </div>
                  </div>

                  <label class="mt-3">Mobile</label>
                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <input type="text" name="customermobile" class="form-control txtField">
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card cardvp px-3 pb-3">
                <div class="card-body"></div>
                <label>Preferred Main String</label>
                <div class="form-inline">
                  <div class="container">
                    <div class="row">
                      <div class="col-10">
                        <select class="form-control" style="width:100%" name="stringid">
                          <option value="">Please select</option>
                          <?php while ($row_Recordset5 = mysqli_fetch_assoc($Recordset5)) { ?>
                            <option value="<?php echo e($row_Recordset5['string_id']); ?>">
                              <?php echo e($row_Recordset5['brand']) . " " . e($row_Recordset5['type']); ?>
                            </option>
                          <?php } ?>
                        </select>
                        <?php mysqli_data_seek($Recordset5, 0); ?>
                      </div>
                      <div class="col-2">
                        <a href="./addamarketstring.php?marker=2" class="btn button-colours"><i class="fa-solid fa-plus"></i></a>
                      </div>
                    </div>
                  </div>
                </div>

                <label class="mt-3">Preferred Cross String</label>
                <div class="form-inline">
                  <div class="container">
                    <div class="row">
                      <div class="col-10">
                        <select class="form-control" style="width:100%" name="stringidc">
                          <option value="0">Same as mains</option>
                          <?php while ($row_Recordset5 = mysqli_fetch_assoc($Recordset5)) { ?>
                            <option value="<?php echo e($row_Recordset5['string_id']); ?>">
                              <?php echo e($row_Recordset5['brand']) . " " . e($row_Recordset5['type']); ?>
                            </option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="col-2">
                        <a href="./addamarketstring.php?marker=2" class="btn button-colours"><i class="fa-solid fa-plus"></i></a>
                      </div>
                    </div>
                  </div>
                </div>

                <label class="mt-3">Preferred Racket</label>
                <div class="form-inline">
                  <div class="container">
                    <div class="row">
                      <div class="col-10">
                        <select class="form-control" style="width:100%" name="racketid">
                          <option value="">Please select</option>
                          <?php while ($row_Recordset4 = mysqli_fetch_assoc($Recordset4)) { ?>
                            <option value="<?php echo e($row_Recordset4['racketid']); ?>">
                              <?php echo e($row_Recordset4['manuf']) . " " . e($row_Recordset4['model']); ?>
                            </option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="col-2">
                        <a href="./addaracket.php?marker=2" class="btn button-colours"><i class="fa-solid fa-plus"></i></a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card cardvp px-3 mt-3 pb-3">
                <div class="card-body">
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">
                        <div class="slidecontainer">
                          <p class="mt-3">Tension Mains (<?php echo e($weight); ?>): <span id="tensionmV">0</span></p>
                          <input type="range" step="0.5" min="0" max="<?php echo e($maxtension); ?>" class="slider" name="tension" id="tensionm" value="0">
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group">
                        <div class="slidecontainer">
                          <p class="mt-3">Tension Crosses (<?php echo e($weight); ?>): <span id="tensioncV">0</span></p>
                          <input type="range" step="0.5" min="0" max="<?php echo e($maxtension); ?>" class="slider" name="tensionc" id="tensionc" value="0">
                        </div>
                      </div>
                    </div>
                    <div class="container">
                      <div class="row">
                        <div class="col-12">
                          <div class="form-group">
                            <p class="mt-3">Pre-Stretch:</p>
                            <div class="col-12 btn-group btn-group-toggle" role="group" data-toggle="buttons">
                              <label class="border btn btn-warning active">
                                <input type="radio" name="preten" value="0" autocomplete="off" checked> 0%
                              </label>
                              <label class="border btn btn-warning">
                                <input type="radio" name="preten" value="5" autocomplete="off"> 5%
                              </label>
                              <label class="border btn btn-warning">
                                <input type="radio" name="preten" value="10" autocomplete="off"> 10%
                              </label>
                              <label class="border btn btn-warning">
                                <input type="radio" name="preten" value="15" autocomplete="off"> 15%
                              </label>
                              <label class="border btn btn-warning">
                                <input type="radio" name="preten" value="20" autocomplete="off"> 20%
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card cardvp px-3 mt-3 pb-3">
                <div class="card-body">
                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <div class="form-group">
                          <p class="mt-3 text-dark">Comments:</p>
                          <textarea class="form-control" name="comments" id="comments" rows="3"></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="my-3 container">
                <div class="row">
                  <div class="col-6">
                    <input type="hidden" name="marker" value="<?php echo e($marker); ?>">
                    <input class="btn button-colours" type="submit" name="submitaddcust" value="Submit">
                  </div>
                  <div class="col-6">
                    <a class="btn button-colours-alt float-right" href="./customers.php">Cancel</a>
                  </div>
                </div>
              </div>
            </form>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>

  <div class="container center">
    <div class="p-3 row">
      <div class="col-2"><a href="#" type="button" class="dot fa-solid fa-plus fa-2x"></a></div>
      <div class="col-2">
        <h3 class="<?php echo !empty($_SESSION['message']) ? 'blinking' : 'dotb'; ?>" title="Warning Messages" data-toggle="modal" data-target="#warningModal"><strong>!</strong></h3>
      </div>
      <div class="col-2">
        <h3 class="dotbt h6 " title="Restrings for <?php echo $current_month_text; ?>"><?php echo $totalRows_Recordset6 ?></h3>
      </div>
      <div class="col-2"><a href="#" class="dotbt h6" title="Total restrings"><?php echo $totalRows_Recordset7 ?></a></div>
      <div class="col-2"><a href="./jobs-unpaid.php" class="dotbt h6" title="Amount Owed"><?php echo $currency . e($sum_owed); ?></a></div>
      <div class="col-2"><a href="#" class="dotbtt h7" title="Total Income"><small><?php echo $currency . e($sum); ?></small></a></div>
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
          <div class="container mt-3" style="margin-top: 120px;">
            <div class="row pt-3">
              <div class="col-8">
                <a class="btn modal_button_cancel" href="./customers.php">Cancel</a>
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

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

  <script>
    // Slider JS (wrapped to prevent errors if elements don't load yet)
    var sliderm = document.getElementById("tensionm");
    var outputm = document.getElementById("tensionmV");
    if (sliderm && outputm) {
      outputm.innerHTML = sliderm.value;
      sliderm.oninput = function() {
        outputm.innerHTML = this.value;
      }
    }

    var sliderc = document.getElementById("tensionc");
    var outputc = document.getElementById("tensioncV");
    if (sliderc && outputc) {
      outputc.innerHTML = sliderc.value;
      sliderc.oninput = function() {
        outputc.innerHTML = this.value;
      }
    }

    $('#year').text(new Date().getFullYear());
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

    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.querySelector(".nav-menu");
    if (hamburger) {
      hamburger.addEventListener("click", () => {
        hamburger.classList.toggle("active");
        navMenu.classList.toggle("active");
      });
    }

    // Clean, unified Theme Switch Logic
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

    // Set logo correctly on initial load
    var imgsrc = localStorage.getItem('themeSwitch');
    var initLogo = document.getElementById("imglogo");
    if (initLogo) {
      initLogo.src = (imgsrc === "dark") ? "./img/logo-dark.png" : "./img/logo.png";
    }
  </script>
</body>

</html>