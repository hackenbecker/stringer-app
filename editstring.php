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

$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");

// SECURITY Helper: Escape output to prevent XSS
function e($string)
{
  return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// SECURITY: Safely cast the string ID to an integer to prevent SQL Injection
$stringid = 0;
if (isset($_POST['submitclearmessage'])) {
  unset($_SESSION['message']);
  $stringid = (int)($_POST['stringid'] ?? 0);
} elseif (isset($_GET['stringid'])) {
  $stringid = (int)$_GET['stringid'];
}

// Fetch String Data
$query_Recordset2 = "SELECT string.*, all_string.brand, all_string.type, all_string.sportid, all_string.notes as stock_notes 
                     FROM string 
                     LEFT JOIN all_string ON string.stock_id = all_string.string_id 
                     WHERE stringid = '$stringid'";
$Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));
$row_Recordset2 = mysqli_fetch_assoc($Recordset2);

// Securely grab the sport ID to filter the reel lengths
$sportid = isset($_GET['sportid']) ? (int)$_GET['sportid'] : (int)($row_Recordset2['sportid'] ?? 0);

// Fetch Base Strings (Excluding "String Generic")
$query_Recordset1 = "SELECT * FROM all_string WHERE brand != 'String Generic' AND type != 'String Generic' ORDER BY brand ASC;";
$Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));

// Fetch Reel Lengths based on sport
$sql = "SELECT * FROM reel_lengths LEFT JOIN sport ON reel_lengths.sport = sport.sportid WHERE sport = '$sportid' ORDER BY length ASC";
$Recordset5 = mysqli_query($conn, $sql) or die(mysqli_error($conn));

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
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./css/style.css">

  <title>SDBA - Edit String</title>
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
    <p class="fxdtextb"><strong>Edit</strong> String</p>

    <div class="container my-1 firstparaaltej">
      <div class="container my-1 pb-3 px-1 firstparaej">
        <div class="container px-1 pt-3 form-text"></div>
        <form method="post" action="./db-update.php">
          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

          <div class="card cardvp" style="margin-top: 75px;">
            <div class="card-body">
              <label class="text-dark">Base String</label>
              <div class="form-inline">
                <div class="container">
                  <div class="row">
                    <div class="col-10">
                      <select class="form-control searchable-dropdown" style="width:100%" name="stringid">
                        <option value="">Please select</option>
                        <?php while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1)) {
                          $selected = ($row_Recordset1['string_id'] == $row_Recordset2['stock_id']) ? "selected" : "";
                        ?>
                          <option value="<?php echo e($row_Recordset1['string_id']); ?>" <?php echo $selected; ?>>
                            <?php echo e($row_Recordset1['brand']) . " " . e($row_Recordset1['type']) . " " . e($row_Recordset1['notes']); ?>
                          </option>
                        <?php } ?>
                        <?php mysqli_data_seek($Recordset1, 0); ?>
                      </select>
                    </div>
                    <div class="col-2">
                      <a href="./addavstring.php?calret=editcust.php" class="btn btn-success"><i class="fa-solid fa-plus"></i></a>
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
                    <div class="slidecontainer">
                      <p class="mt-3 text-dark">Reel Purchase Price: <?php echo e($currency); ?><span id="purchpriceV"></span></p>
                      <input type="range" min="0" max="250" value="<?php echo e($row_Recordset2['reel_price'] ?? 0); ?>" class="slider" name="purchprice" id="purchprice">
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <div class="slidecontainer">
                      <p class="mt-3 text-dark">Restring Price: <?php echo e($currency); ?><span id="racketpriceV"></span></p>
                      <input type="range" min="0" max="30" class="slider" value="<?php echo e($row_Recordset2['racket_price'] ?? 0); ?>" name="racketprice" id="racketprice">
                    </div>
                  </div>
                </div>
              </div>

              <label class="mt-3 text-dark">Purchase date</label>
              <div class="form-group">
                <div class="d-flex">
                  <div class="input-group date" id="id_4">
                    <input type="text" value="<?php echo e($row_Recordset2['purchase_date']); ?>" name="datepurch" class="form-control" required />
                    <div class="input-group-addon input-group-append">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
              </div>

              <p class="mt-3 text-dark">Reel Length</p>
              <div class="form-inline">
                <div class="container">
                  <div class="row">
                    <div class="col-12 p-0">
                      <select class="form-control" style="width:100%" name="length" required>
                        <option value="">Please select</option>
                        <?php while ($row_Recordset5 = mysqli_fetch_assoc($Recordset5)) {
                          $selected = ($row_Recordset5['reel_length_id'] == $row_Recordset2['lengthid']) ? "selected" : "";
                        ?>
                          <option value="<?php echo e($row_Recordset5['reel_length_id']); ?>" <?php echo $selected; ?>>
                            <?php echo e($row_Recordset5['length']) . e($units); ?>
                          </option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <label class="mt-3 text-dark">Modify string number (Use this if you scrap some string)</label>
              <div class="row">
                <div class="col-12">
                  <div class="form-group">
                    <select class="form-control" style="width:100%" name="startnumber" required>
                      <?php
                      $current_num = $row_Recordset2['string_number'] ?? 0;
                      for ($i = 0; $i <= 23; $i += 0.5) {
                        $selected = ($current_num == $i) ? "selected" : "";
                      ?>
                        <option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card cardvp mb-3">
            <div class="card-body">
              <div class="row">
                <div class="col-12">
                  <div class="form-group">
                    <label class="text-dark" for="comments">Reel Notes</label>
                    <textarea class="form-control" name="notes" id="notes" rows="3"><?php echo e($row_Recordset2['note']); ?></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card cardvp my-3">
            <div class="card-body">
              <div class="col">
                <div class="form-check">
                  <input class="form-check-input" type="hidden" name="ownersupplied" value="no">
                  <input class="form-check-input" type="checkbox" name="ownersupplied" value="yes" id="ownersupplied" <?php if (($row_Recordset2['Owner_supplied'] ?? '') == "yes") echo " checked"; ?>>
                  <label class="form-check-label text-dark" for="ownersupplied">Owner Supplied</label>
                </div>
              </div>
              <div class="col mt-2">
                <div class="form-check">
                  <input class="form-check-input" type="hidden" name="emptyreel" value="0">
                  <input class="form-check-input" type="checkbox" name="emptyreel" value="1" id="emptyreel" <?php if (($row_Recordset2['empty'] ?? '') == "1") echo " checked"; ?>>
                  <label class="form-check-label text-dark" for="emptyreel">Reel Finished</label>
                </div>
              </div>
            </div>
          </div>

          <input type="hidden" name="editstockstring" value="<?php echo e($stringid); ?>">

          <div class="container mb-3">
            <div class="row pt-3">
              <div class="col-6">
                <input class="btn button-colours" type="submit" name="submit" value="Submit">
              </div>
              <div class="col-6">
                <a class="btn button-colours-alt float-right" href="./string.php">Cancel</a>
              </div>
            </div>
          </div>
        </form>
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
        <h3 class="dotbt h6 " title="Restrings for <?php echo $current_month_text; ?>"><?php echo $totalRows_Recordset6 ?></h3>
      </div>
      <div class="col-2"><a href="#" class="dotbt h6" title="Total restrings"><?php echo $totalRows_Recordset7 ?></a></div>
      <div class="col-2"><a href="./jobs-unpaid.php" class="dotbt h6" title="Amount Owed"><?php echo $currency . e($sum_owed); ?></a></div>
      <div class="col-2"><a href="./jobs-unpaid.php" class="dotbtt h7" title="Total Income"><small><?php echo $currency . e($sum); ?></small></a></div>
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
                  <input type="hidden" name="stringid" value="<?php echo e($stringid); ?>">
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
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
    // Initialize the Select2 searchable dropdown
    $(document).ready(function() {
      if ($('.searchable-dropdown').length) {
        $('.searchable-dropdown').select2({
          width: '100%',
          placeholder: "Type to search..."
        });
      }
    });

    // Initialize Sliders
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