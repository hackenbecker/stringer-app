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
if ($_SESSION['level'] < 1) {
  header('Location: ./nopermission.php');
  exit;
}

if (isset($_POST['submitclearmessage'])) {
  unset($_SESSION['message']);
}

$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");

// SECURITY Helper: Escape output to prevent XSS
function e($string)
{
  return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// SECURITY: Cast inputs to integers to prevent XSS / SQL Injection
$customerid = isset($_POST['customerid']) ? (int)$_POST['customerid'] : (isset($_GET['customerid']) ? (int)$_GET['customerid'] : 0);
$sportid = isset($_POST['sportid']) ? (int)$_POST['sportid'] : (isset($_GET['sportid']) ? (int)$_GET['sportid'] : 0);

$sportname = "";
$totalRows_Recordset2 = 0;
$totalRows_Recordset7 = 0;
$totalRows_Recordset14 = 0;

if ($sportid > 0) {
  // Fetch Mains Strings (Excluding String Generic)
  $query_Recordset2 = "SELECT * FROM string LEFT JOIN all_string ON string.stock_id = all_string.string_id WHERE sportid = '$sportid' AND empty = '0' AND brand != 'String Generic' AND type != 'String Generic' ORDER BY string.stringid ASC;";
  $Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));
  $totalRows_Recordset2 = mysqli_num_rows($Recordset2);

  // Fetch Crosses Strings (Excluding String Generic)
  $query_Recordset7 = "SELECT * FROM string LEFT JOIN all_string ON string.stock_id = all_string.string_id WHERE sportid = '$sportid' AND empty = '0' AND brand != 'String Generic' AND type != 'String Generic' ORDER BY string.stringid ASC;";
  $Recordset7 = mysqli_query($conn, $query_Recordset7) or die(mysqli_error($conn));
  $totalRows_Recordset7 = mysqli_num_rows($Recordset7);

  // Fetch Sport Name
  $query_Recordset14 = "SELECT sportname FROM sport WHERE sportid = '$sportid' ORDER BY sportname ASC;";
  $Recordset14 = mysqli_query($conn, $query_Recordset14) or die(mysqli_error($conn));
  $sportname = mysqli_fetch_assoc($Recordset14)['sportname'] ?? '';
}

// Fetch Customers
$query_Recordset3 = "SELECT cust_ID, Name FROM customer ORDER BY Name ASC;";
$Recordset3 = mysqli_query($conn, $query_Recordset3) or die(mysqli_error($conn));
$totalRows_Recordset3 = mysqli_num_rows($Recordset3);

// Fetch Rackets
$totalRows_Recordset4 = 0;
if ($sportid > 0) {
  $query_Recordset4 = "SELECT racketid, manuf, model FROM rackets WHERE sport = '$sportid' ORDER BY manuf ASC;";
  $Recordset4 = mysqli_query($conn, $query_Recordset4) or die(mysqli_error($conn));
  $totalRows_Recordset4 = mysqli_num_rows($Recordset4);
}

// Base customer data variables
$racketid = 0;
$prestretch = 0;
$customername = "";
$tension = 0;
$tensionc = 0;
$stringidm = 0;
$stringidc = 0;

if ($customerid > 0) {
  // Try finding customer with non-empty string stock
  $query_Recordset6 = "SELECT *,
    stockmains.string_id AS st_ma_id, stockcrosses.string_id AS st_cr_id,  
    crosses.stock_id AS cr_id, mains.stock_id AS ma_id,
    stockmains.brand AS st_ma_br, stockcrosses.brand AS st_cr_br,
    stockmains.type AS st_ma_ty, stockcrosses.type AS st_cr_ty
    FROM customer 
    LEFT JOIN all_string AS stockmains ON stockmains.string_id = customer.pref_string 
    LEFT JOIN all_string AS stockcrosses ON stockcrosses.string_id = customer.pref_stringc 
    LEFT JOIN rackets ON rackets.racketid = customer.racketid 
    LEFT JOIN string AS mains ON stockmains.string_id = mains.stock_id 
    LEFT JOIN string AS crosses ON stockcrosses.string_id = crosses.stock_id 
    WHERE mains.empty = '0' AND crosses.empty = '0' AND cust_ID = '$customerid'";
  $Recordset6 = mysqli_query($conn, $query_Recordset6) or die(mysqli_error($conn));
  $totalRows_Recordset6 = mysqli_num_rows($Recordset6);

  if ($totalRows_Recordset6 == 0) {
    // Fallback if strings are empty or don't exist
    $query_Recordset6 = "SELECT *,
      stockmains.string_id AS st_ma_id, stockcrosses.string_id AS st_cr_id,  
      crosses.stock_id AS cr_id, mains.stock_id AS ma_id,
      stockmains.brand AS st_ma_br, stockcrosses.brand AS st_cr_br,
      stockmains.type AS st_ma_ty, stockcrosses.type AS st_cr_ty
      FROM customer 
      LEFT JOIN all_string AS stockmains ON stockmains.string_id = customer.pref_string 
      LEFT JOIN all_string AS stockcrosses ON stockcrosses.string_id = customer.pref_stringc 
      LEFT JOIN rackets ON rackets.racketid = customer.racketid 
      LEFT JOIN string AS mains ON stockmains.string_id = mains.stock_id 
      LEFT JOIN string AS crosses ON stockcrosses.string_id = crosses.stock_id 
      WHERE cust_ID = '$customerid'";
    $Recordset6 = mysqli_query($conn, $query_Recordset6) or die(mysqli_error($conn));
  }

  $row_Recordset6 = mysqli_fetch_assoc($Recordset6);
  if ($row_Recordset6) {
    $customerid = $row_Recordset6['cust_ID'];
    $customername = $row_Recordset6['Name'];
    $tension = $row_Recordset6['tension'] ?? 0;
    $tensionc = $row_Recordset6['tensionc'] ?? 0;
    $prestretch = $row_Recordset6['prestretch'] ?? 0;
    $stringidm = $row_Recordset6['pref_string'];
    $stringidc = $row_Recordset6['pref_stringc'];
    $racketid = $row_Recordset6['racketid'];
  }
}

// Fetch Sports List
$query_Recordset13 = "SELECT sportid, sportname FROM sport ORDER BY sportname ASC;";
$Recordset13 = mysqli_query($conn, $query_Recordset13) or die(mysqli_error($conn));
$totalRows_Recordset13 = mysqli_num_rows($Recordset13);

// PERFORMANCE: Optimized Counting Queries
$query_Recordset9 = "SELECT COUNT(job_id) as total FROM stringjobs";
$totalRows_Recordset9 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset9))['total'] ?? 0;

$query_Recordset10 = "SELECT COUNT(job_id) as total FROM stringjobs WHERE collection_date LIKE '___" . $current_month_numeric . "/" . $current_year . "%'";
$totalRows_Recordset10 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset10))['total'] ?? 0;

$query_Recordset11 = "SELECT SUM(price) as SUM from stringjobs";
$sum = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset11))['SUM'] ?? 0;

$query_Recordset12 = "SELECT SUM(price) as SUM from stringjobs WHERE paid != '1'";
$sum_owed = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset12))['SUM'] ?? 0;
$_SESSION['sum_owed'] = $sum_owed;

// Settings
$query_Recordset15 = "SELECT value FROM settings WHERE id = '21';";
$Recordset15 = mysqli_query($conn, $query_Recordset15) or die(mysqli_error($conn));
$weight = mysqli_fetch_assoc($Recordset15)['value'] ?? 'lbs';
$maxtension = ($weight == "kg") ? 35 : 70;

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="./css/bootstrap-datetimepicker.min.css" type="text/css" media="all" />
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./css/style.css">

  <title>SDBA - Add Job</title>
  <link rel="icon" type="image/png" href="./img/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="./img/favicon-16x16.png" sizes="16x16" />
</head>

<body id="home-section-results" data-spy="scroll" data-target="#main-nav">

  <script>
    if (localStorage.getItem('themeSwitch') === 'dark') {
      document.body.setAttribute('data-theme', 'dark');
    }
  </script>

  <?php echo $main_menus; ?>

  <div class="home-section diva">
    <div class="subheader"></div>
    <p class="fxdtextb"><strong>Add</strong> Restring</p>

    <div class="container my-3 firstparaalt">

      <div class="card cardvp" style="margin-top: 75px;">
        <div class="card-body">
          <div class="mt-3 container">
            <div class="row">
              <?php if ($customerid > 0) { ?>
                <div class="col-10">
                  <div class="form-group">
                    <h5 class='form-text'>Customer: <?php echo e($customername); ?></h5>
                  </div>
                </div>
              <?php } else { ?>
                <div class="col-10">
                  <div class="form-group">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                      <select class="form-control input-sm searchable-dropdown" style="width:100%" name="customerid" onchange="this.form.submit()">
                        <option value="0">Select Customer</option>
                        <?php while ($row_Recordset3 = mysqli_fetch_assoc($Recordset3)) { ?>
                          <option value="<?php echo e($row_Recordset3['cust_ID']); ?>">
                            <?php echo e($row_Recordset3['Name']); ?>
                          </option>
                        <?php } ?>
                      </select>
                      <input type="hidden" name="postpositive" value="1">
                    </form>
                  </div>
                </div>
                <div class="col-2">
                  <a href="./addcustomer.php?marker=3" class="btn button-colours"><i class="fa-solid fa-plus"></i></a>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>

      <?php if (isset($_POST['postpositive']) || $customerid > 0) { ?>
        <div class="card cardvp mt-3">
          <div class="card-body">
            <div class="mt-3 container">
              <div class="row">
                <?php if ($sportid > 0) { ?>
                  <div class="col-10">
                    <div class="form-group">
                      <h5 class='form-text'>Sport: <?php echo e($sportname); ?></h5>
                    </div>
                  </div>
                <?php } else { ?>
                  <div class="col-12">
                    <div class="form-group">
                      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <select class="form-control input-sm searchable-dropdown" style="width:100%" name="sportid" onchange="this.form.submit()">
                          <option value="0">Select Sport</option>
                          <?php while ($row_Recordset13 = mysqli_fetch_assoc($Recordset13)) { ?>
                            <option value="<?php echo e($row_Recordset13['sportid']); ?>">
                              <?php echo e($row_Recordset13['sportname']); ?>
                            </option>
                          <?php } ?>
                        </select>
                        <input type="hidden" name="postpositive" value="1">
                        <input type="hidden" name="postpositive1" value="1">
                        <input type="hidden" name="customerid" value="<?php echo e($customerid); ?>">
                      </form>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>

      <?php if (isset($_POST['postpositive1']) || $sportid > 0) { ?>
        <div class="card cardvp mt-3">
          <div class="card-body">

            <form method="post" action="./db-update.php" enctype="multipart/form-data">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

              <div class="container">
                <label class="pt-3 form-text">String (Mains)</label>
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <select class="form-control searchable-dropdown" style="width:100%" name="stringid" required>
                        <option value="">Please select</option>
                        <?php if ($totalRows_Recordset2 > 0) {
                          while ($row_Recordset2 = mysqli_fetch_assoc($Recordset2)) {
                            $selected = ($row_Recordset2['stock_id'] == $stringidm) ? "selected" : ""; ?>
                            <option value="<?php echo e($row_Recordset2['stringid']); ?>" <?php echo $selected; ?>>
                              <?php echo e($row_Recordset2['brand']) . " " . e($row_Recordset2['type']) . " " . e($row_Recordset2['note'] ?? ''); ?>
                            </option>
                        <?php }
                        } ?>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <div class="container">
                <label class="mt-3 form-text">String (Crosses)</label>
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <select class="form-control searchable-dropdown" style="width:100%" name="stringidc">
                        <option value="0">Same as mains</option>
                        <?php if ($totalRows_Recordset7 > 0) {
                          while ($row_Recordset7 = mysqli_fetch_assoc($Recordset7)) {
                            $selected = ($row_Recordset7['stock_id'] == $stringidc) ? "selected" : ""; ?>
                            <option value="<?php echo e($row_Recordset7['stringid']); ?>" <?php echo $selected; ?>>
                              <?php echo e($row_Recordset7['brand']) . " " . e($row_Recordset7['type']) . " " . e($row_Recordset7['note'] ?? ''); ?>
                            </option>
                        <?php }
                        } ?>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <div class="container mt-3 p-3">
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <div class="slidecontainer">
                        <p class="mt-3 form-text">Tension Mains (<?php echo e($weight); ?>): <span id="tensionmV">0</span></p>
                        <input type="range" step="0.5" min="0" max="<?php echo e($maxtension); ?>" value="<?php echo e($tension); ?>" class="slider" name="tensionm" id="tensionm" required>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group">
                      <div class="slidecontainer">
                        <p class="mt-3 form-text">Tension Crosses (<?php echo e($weight); ?>): <span id="tensioncV">0</span></p>
                        <input type="range" step="0.5" min="0" max="<?php echo e($maxtension); ?>" value="<?php echo e($tensionc); ?>" class="slider" name="tensionc" id="tensionc">
                      </div>
                    </div>
                  </div>

                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <div class="form-group">
                          <p class="mt-3 form-text">Pre-Stretch:</p>
                          <div class="col-12 btn-group btn-group-toggle" role="group" data-toggle="buttons">
                            <?php foreach ([0, 5, 10, 15, 20] as $pt_val) {
                              $is_active = ($prestretch == $pt_val) ? "active" : "";
                              $is_checked = ($prestretch == $pt_val) ? "checked" : "";
                            ?>
                              <label class="border btn btn-warning <?php echo $is_active; ?>">
                                <input type="radio" name="preten" value="<?php echo $pt_val; ?>" autocomplete="off" <?php echo $is_checked; ?>> <?php echo $pt_val; ?>%
                              </label>
                            <?php } ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          </div>
        </div>

        <div class="card cardvp mt-3">
          <div class="card-body">
            <label class="mt-3 form-text">Racket</label>
            <div class="row">
              <div class="col-12">
                <select class="form-control searchable-dropdown" style="width:100%" name="racketid" required>
                  <option value="">Please select</option>
                  <?php if ($totalRows_Recordset4 > 0) {
                    while ($row_Recordset4 = mysqli_fetch_assoc($Recordset4)) {
                      $selected = ($row_Recordset4['racketid'] == $racketid) ? "selected" : ""; ?>
                      <option value="<?php echo e($row_Recordset4['racketid']); ?>" <?php echo $selected; ?>>
                        <?php echo e($row_Recordset4['manuf']) . " " . e($row_Recordset4['model']); ?>
                      </option>
                  <?php }
                  } ?>
                </select>
                <div class="mt-3 custom-file">
                  <input class="custom-file-input" name="image" type="file" accept="image/*" capture="camera">
                  <label class="custom-file-label" for="customFile">Racket Picture ( jpg, png, gif )</label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card cardvp mt-3">
          <div class="card-body">
            <?php $current_date = date("d/m/Y"); ?>
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label class="mt-3 form-text">Date Received</label>
                  <div class="form-group">
                    <div class="input-group date" id="id_4">
                      <input type="text" value="<?php echo $current_date; ?>" name="daterecd" class="form-control" required />
                      <div class="input-group-addon input-group-append">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label class="mt-3 form-text">Date Required</label>
                  <div class="form-group">
                    <div class="input-group date" id="id_3">
                      <input type="text" name="datereqd" class="form-control" required />
                      <div class="input-group-addon input-group-append">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card cardvp mt-3">
          <div class="card-body">
            <div class="container">
              <div class="row">
                <div class="col-6">
                  <div class="form-check">
                    <input type="hidden" name="gripreqd" value="0">
                    <input class="form-check-input" type="checkbox" name="gripreqd" value="1" id="grip">
                    <label class="form-check-label form-text" for="grip">Grip Required</label>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-check">
                    <input type="hidden" name="freerestring" value="0">
                    <input class="form-check-input" type="checkbox" name="freerestring" value="1" id="flexCheckDefault1">
                    <label class="form-check-label form-text" for="flexCheckDefault1">Free Restring</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label class="mt-3 form-text" for="comments">Comments</label>
                  <textarea class="form-control" name="comments" id="comments" rows="3"></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <input type="hidden" name="marker" value="3">
        <input type="hidden" name="weight" value="<?php echo e($weight); ?>">
        <input type="hidden" name="customerid" value="<?php echo e($customerid); ?>">
        <input type="hidden" name="addflag" value="1">

        <div class="container mt-3">
          <div class="row pb-3">
            <div class="col-6">
              <input class="btn button-colours" type="submit" name="submitadd" value="Submit">
            </div>
            <div class="col-6">
              <a class="btn button-colours-alt float-right" href="./string-jobs.php">Cancel</a>
            </div>
          </div>
        </div>
        </form>
      <?php } ?>
    </div>
  </div>

  <div class="container center">
    <div class="p-3 row">
      <div class="col-2"><a href="#" type="button" class="dot fa-solid fa-plus fa-2x"></a></div>
      <div class="col-2">
        <h3 class="<?php echo !empty($_SESSION['message']) ? 'blinking' : 'dotb'; ?>" title="Warning Messages" data-toggle="modal" data-target="#warningModal"><strong>!</strong></h3>
      </div>
      <div class="col-2">
        <h3 class="dotbt h6" title="Restrings for <?php echo $current_month_text; ?>"><?php echo $totalRows_Recordset10 ?></h3>
      </div>
      <div class="col-2"><a href="#" class="dotbt h6" title="Total restrings"><?php echo $totalRows_Recordset9 ?></a></div>
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
                <a class="btn modal_button_cancel" href="./string.php">Cancel</a>
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

  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script type="text/javascript" src="./js/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript" src="./js/demo.js"></script>
  <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>

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

    $(function() {
      if ($.datepicker) {
        $.datepicker.parseDate("yy-mm-dd", "2007-01-26");
        $("#datepicker1").datepicker({});
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

    // Custom File input label
    $(".custom-file-input").on("change", function() {
      var fileName = $(this).val().split("\\").pop();
      $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

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
  <script>
    // Initialize the Select2 searchable dropdowns
    $(document).ready(function() {
      if ($('.searchable-dropdown').length) {
        $('.searchable-dropdown').select2({
          width: '100%',
          placeholder: "Type to search..."
        });
      }
    });
  </script>
</body>

</html>