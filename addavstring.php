<?php
require_once('Connections/wcba.php');
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

// SECURITY Helper: Escape output to prevent XSS
function e($string)
{
  return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// SECURITY: Cast inputs to integers to prevent XSS / SQL Injection
$marker = isset($_GET['marker']) ? (int)$_GET['marker'] : 1;
$sportid = isset($_POST['sportid']) ? (int)$_POST['sportid'] : 0;

$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");

// Parse Dynamic Form Data securely
$totalRows_Recordset1 = 0;
$ims_stock_id = 0;
if (!empty($_POST['imsid'])) {
  $values = explode(':', $_POST['imsid']);
  $ims_stock_id = (int)($values[0] ?? 0);
  $ims_sport_id = (int)($values[1] ?? 0);

  $sql = "SELECT reel_lengths.reel_length_id, reel_lengths.length, sport.sportname 
          FROM reel_lengths 
          LEFT JOIN sport ON reel_lengths.sport = sport.sportid 
          WHERE reel_lengths.sport = '$ims_sport_id' ORDER BY length ASC";
  $Recordset1 = mysqli_query($conn, $sql) or die(mysqli_error($conn));
  $totalRows_Recordset1 = mysqli_num_rows($Recordset1);

  $query_Recordset4 = "SELECT brand, type FROM all_string WHERE string_id = '$ims_stock_id' ORDER BY brand ASC;";
  $Recordset4 = mysqli_query($conn, $query_Recordset4) or die(mysqli_error($conn));
  $row_Recordset4 = mysqli_fetch_assoc($Recordset4);
}

// Fetch basic sport list for step 1
$query_Recordset13 = "SELECT sportid, sportname FROM sport ORDER BY sportname ASC;";
$Recordset13 = mysqli_query($conn, $query_Recordset13) or die(mysqli_error($conn));
$totalRows_Recordset13 = mysqli_num_rows($Recordset13);

$sportname = '';
if ($sportid > 0) {
  // Fetch strings matching the chosen sport
  $query_Recordset3 = "SELECT string_id, sportid, brand, type FROM all_string WHERE sportid = '$sportid' ORDER BY brand ASC;";
  $Recordset3 = mysqli_query($conn, $query_Recordset3) or die(mysqli_error($conn));

  $query_Recordset14 = "SELECT sportname FROM sport WHERE sportid = '$sportid' ORDER BY sportname ASC;";
  $Recordset14 = mysqli_query($conn, $query_Recordset14) or die(mysqli_error($conn));
  $sportname = mysqli_fetch_assoc($Recordset14)['sportname'] ?? '';
}

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
  <link rel="stylesheet" href="./css/style.css">
  <title>SDBA - Add String</title>
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
    <p class="fxdtextb"><strong>ADD</strong> String</p>

    <div class="container my-1 mt-3 firstparaaltej">
      <div class="container my-1 pb-3 px-1 firstparaej">
        <div class="container px-1 pt-3 form-text">

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
                        <form method="post" action="">
                          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                          <select class="form-control input-sm" style="width:100%" name="sportid" onchange="this.form.submit()">
                            <option value="0">Select Sport</option>
                            <?php while ($row_Recordset13 = mysqli_fetch_assoc($Recordset13)) { ?>
                              <option value="<?php echo e($row_Recordset13['sportid']); ?>">
                                <?php echo e($row_Recordset13['sportname']); ?>
                              </option>
                            <?php } ?>
                          </select>
                          <input type="hidden" name="postpositive" value="1">
                          <input type="hidden" name="postpositive1" value="1">
                        </form>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>

          <?php if (isset($_POST['postpositive'])) { ?>
            <div class="card cardvp my-3">
              <div class="card-body">
                <label>Base reel</label>
                <div class="form-inline">
                  <div class="container">
                    <?php if (!isset($_POST['imsid'])) { ?>
                      <div class="row">
                        <div class="col-10">
                          <form method="post" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <select class="form-control" style="width:100%" name="imsid" onchange="this.form.submit()">
                              <option value="">Please select</option>
                              <?php while ($row_Recordset3 = mysqli_fetch_assoc($Recordset3)) { ?>
                                <option value="<?php echo e($row_Recordset3['string_id']) . ':' . e($row_Recordset3['sportid']); ?>">
                                  <?php echo e($row_Recordset3['brand']) . " " . e($row_Recordset3['type']); ?>
                                </option>
                              <?php } ?>
                            </select>
                            <input type="hidden" name="sportid" value="<?php echo e($sportid); ?>">
                            <input type="hidden" name="postpositive" value="1">
                          </form>
                        </div>
                        <div class="col-2">
                          <a href="./addamarketstring.php?marker=1" class="btn btn-success"><i class="fa-solid fa-plus"></i></a>
                        </div>
                      </div>
                    <?php } else {
                      echo "<strong>" . e($row_Recordset4['brand']) . " " . e($row_Recordset4['type']) . "</strong>";
                    } ?>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>

          <?php if (isset($_POST['imsid'])) { ?>
            <form method="post" action="./db-update.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

              <div class="card cardvp my-3">
                <div class="card-body">
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">
                        <div class="slidecontainer">
                          <p class="mt-3">Reel Purchase Price: <?php echo e($currency); ?><span id="purchpriceV"></span></p>
                          <input type="range" min="0" max="250" class="slider" name="purchprice" id="purchprice" value="0">
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group">
                        <div class="slidecontainer">
                          <p class="mt-3">Restring Price: <?php echo e($currency); ?><span id="racketpriceV"></span></p>
                          <input type="range" min="0" max="30" class="slider" name="racketprice" id="racketprice" value="0">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card cardvp my-3">
                <div class="card-body">
                  <div class="container">
                    <label class="mt-3">Reel Length</label>
                    <div class="row">
                      <div class="col-12">
                        <div class="form-group">
                          <select class="form-control" style="width:100%" name="length" required>
                            <option value="x">Please select</option>
                            <?php while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1)) { ?>
                              <option value="<?php echo e($row_Recordset1['reel_length_id']); ?>">
                                <?php echo e($row_Recordset1['length']) . e($units) . " (" . e($row_Recordset1['sportname']) . ")"; ?>
                              </option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                    </div>

                    <label class="mt-3">Reel Start Number (Usually 0)</label>
                    <div class="row">
                      <div class="col-12">
                        <div class="form-group">
                          <select class="form-control" style="width:100%" name="startnumber" required>
                            <?php for ($i = 0; $i <= 23; $i += 0.5) { ?>
                              <option value="<?php echo $i; ?>" <?php if ($i == 0) echo 'selected="selected"'; ?>><?php echo $i; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-12">
                        <div class="form-group">
                          <label class="mt-3">Purchase date</label>
                          <div class="input-group date" id="id_4">
                            <input type="text" name="datepurch" class="form-control datepicker" required />
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

              <div class="card cardvp my-3">
                <div class="card-body">
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">
                        <label for="notes">Reel Notes</label>
                        <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-6">
                      <div class="form-check">
                        <input type="hidden" name="ownersupplied" value="no">
                        <input class="form-check-input" type="checkbox" name="ownersupplied" value="yes" id="ownersupplied">
                        <label class="form-check-label" for="ownersupplied">Owner Supplied</label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <input type="hidden" name="marker" value="<?php echo e($marker); ?>">
              <input type="hidden" name="stockid" value="<?php echo e($ims_stock_id); ?>">
              <input type="hidden" name="addstringflag" value="1">
              <?php if (isset($_POST['calret'])) { ?>
                <input type="hidden" name="calret" value="<?php echo e($_POST['calret']); ?>">
              <?php } ?>

              <div class="container mt-3">
                <div class="row pt-3">
                  <div class="col-6">
                    <input class="btn button-colours" type="submit" name="submitaddstockstring" value="Submit">
                  </div>
                  <div class="col-6">
                    <a class="btn button-colours-alt float-right" href="./string.php">Cancel</a>
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
      <div class="col-2"><a href="./addavstring.php" type="button" class="dot fa-solid fa-plus fa-2x"></a></div>
      <div class="col-2">
        <h3 class="<?php echo !empty($_SESSION['message']) ? 'blinking' : 'dotb'; ?>" title="Warning Messages" data-toggle="modal" data-target="#warningModal"><strong>!</strong></h3>
      </div>
      <div class="col-2">
        <h3 class="dotbt h6" title="Restrings for <?php echo $current_month_text; ?>"><?php echo $totalRows_Recordset6 ?></h3>
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
                <a class="btn modal_button_cancel" href="./addavstring.php">Cancel</a>
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
  <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.min.js"></script>
  <script type="text/javascript" src="./js/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript" src="./js/demo.js"></script>

  <script>
    // Slider Initialization (Wrapped to prevent errors before form step 3)
    var slider = document.getElementById("purchprice");
    var output = document.getElementById("purchpriceV");
    if (slider && output) {
      output.innerHTML = slider.value;
      slider.oninput = function() {
        output.innerHTML = this.value;
      }
    }

    var slidera = document.getElementById("racketprice");
    var outputa = document.getElementById("racketpriceV");
    if (slidera && outputa) {
      outputa.innerHTML = slidera.value;
      slidera.oninput = function() {
        outputa.innerHTML = this.value;
      }
    }

    // Initialize Datepicker
    $(function() {
      if ($('.datepicker').length) {
        $('.datepicker').datepicker({
          language: "en",
          autoclose: true,
          format: "dd/mm/yyyy"
        });
      }
    });

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