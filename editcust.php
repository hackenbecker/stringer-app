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

// SECURITY Helper: Escape output to prevent XSS
function e($string)
{
  return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// SECURITY: Safely cast the customer ID to an integer to prevent SQL Injection
$custid = 0;
if (isset($_POST['submitclearmessage'])) {
  unset($_SESSION['message']);
  $custid = (int)($_POST['custid'] ?? 0);
} elseif (isset($_GET['custid'])) {
  $custid = (int)$_GET['custid'];
}

// Fetch Customer Data
$query_Recordset2 = "SELECT * FROM customer 
                     LEFT JOIN string ON customer.pref_string = string.stringid 
                     LEFT JOIN rackets ON customer.racketid = rackets.racketid 
                     LEFT JOIN all_string ON all_string.string_id = string.stock_id  
                     WHERE cust_ID = '$custid'";
$Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));
$row_Recordset2 = mysqli_fetch_assoc($Recordset2);

// Fetch Rackets
$query_Recordset4 = "SELECT * FROM rackets ORDER BY manuf ASC;";
$Recordset4 = mysqli_query($conn, $query_Recordset4) or die(mysqli_error($conn));

// Fetch Strings (Excluding "String Generic")
$query_Recordset1 = "SELECT * FROM all_string WHERE brand != 'String Generic' AND type != 'String Generic' ORDER BY brand, type ASC;";
$Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));

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

// Conversion logic if KG
if ($weight == "kg") {
  $row_Recordset2['tension'] = (round(($row_Recordset2['tension'] ?? 0) * 0.45359237, 1));
  $row_Recordset2['tensionc'] = (round(($row_Recordset2['tensionc'] ?? 0) * 0.45359237, 1));
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
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./css/style.css">

  <title>SDBA - Edit Customer</title>
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
    <div class="subheader"></div>
    <p class="fxdtextb"><strong>Edit</strong> Customer</p>

    <div class="container my-1 firstparaaltej">
      <div class="container my-1 pb-3 px-1 firstparaej">
        <form method="post" action="./db-update.php">
          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

          <div class="card cardvp" style="margin-top: 75px;">
            <div class="card-body">
              <label>Customer Name</label>
              <div class="container">
                <div class="row">
                  <div class="col-12">
                    <input type="text" name="customername" class="form-control txtField" value="<?php echo e($row_Recordset2['Name']); ?>" required>
                  </div>
                </div>
              </div>

              <label class="mt-3">Email</label>
              <div class="container">
                <div class="row">
                  <div class="col-12">
                    <input type="email" name="customeremail" class="form-control txtField" value="<?php echo e($row_Recordset2['Email']); ?>">
                  </div>
                </div>
              </div>

              <label class="mt-3">Mobile</label>
              <div class="container">
                <div class="row">
                  <div class="col-12">
                    <input type="text" name="customermobile" class="form-control txtField" value="<?php echo e($row_Recordset2['Mobile']); ?>">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card cardvp mt-3">
            <div class="card-body">
              <label>String Mains</label>
              <div class="form-inline">
                <div class="container">
                  <div class="row">
                    <div class="col-10">
                      <select class="form-control searchable-dropdown" style="width:100%" name="stringid">
                        <option value="">Please select</option>
                        <?php while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1)) {
                          $selected = ($row_Recordset1['string_id'] == $row_Recordset2['pref_string']) ? "selected" : "";
                        ?>
                          <option value="<?php echo e($row_Recordset1['string_id']); ?>" <?php echo $selected; ?>>
                            <?php echo e($row_Recordset1['brand']) . " " . e($row_Recordset1['type']) . " " . e($row_Recordset1['notes']); ?>
                          </option>
                        <?php } ?>
                        <?php mysqli_data_seek($Recordset1, 0); ?>
                      </select>
                    </div>
                    <div class="col-2">
                      <a href="./addavstring.php?calret=editcust.php" class="btn button-colours"><i class="fa-solid fa-plus"></i></a>
                    </div>
                  </div>
                </div>
              </div>

              <label class="mt-3">String Crosses</label>
              <div class="form-inline">
                <div class="container">
                  <div class="row">
                    <div class="col-10">
                      <select class="form-control searchable-dropdown" style="width:100%" name="stringidc">
                        <option value="0">Same as Mains</option>
                        <?php while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1)) {
                          $selected = ($row_Recordset1['string_id'] == $row_Recordset2['pref_stringc']) ? "selected" : "";
                        ?>
                          <option value="<?php echo e($row_Recordset1['string_id']); ?>" <?php echo $selected; ?>>
                            <?php echo e($row_Recordset1['brand']) . " " . e($row_Recordset1['type']) . " " . e($row_Recordset1['notes']); ?>
                          </option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-2">
                      <a href="./addavstring.php?calret=editcust.php" class="btn button-colours"><i class="fa-solid fa-plus"></i></a>
                    </div>
                  </div>
                </div>
              </div>

              <div class="px-3 row">
                <div class="col-12">
                  <div class="form-group">
                    <div class="slidecontainer">
                      <p class="mt-3">Tension Mains (<?php echo e($weight); ?>): <span id="tensionmV"></span></p>
                      <input type="range" step="0.5" min="0" max="<?php echo e($maxtension); ?>" value="<?php echo e($row_Recordset2['tension'] ?? 0); ?>" class="slider" name="tension" id="tensionm">
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <div class="slidecontainer">
                      <p class="mt-3">Tension Crosses (<?php echo e($weight); ?>): <span id="tensioncV"></span></p>
                      <input type="range" step="0.5" min="0" max="<?php echo e($maxtension); ?>" value="<?php echo e($row_Recordset2['tensionc'] ?? 0); ?>" class="slider" name="tensionc" id="tensionc">
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-group">
                    <p class="mt-3">Pre-Stretch:</p>
                    <div class="col-12 btn-group btn-group-toggle" role="group" data-toggle="buttons">
                      <?php
                      $prestretch_val = $row_Recordset2['prestretch'] ?? 0;
                      foreach ([0, 5, 10, 15, 20] as $pt_val) {
                        $is_active = ($prestretch_val == $pt_val) ? "active" : "";
                        $is_checked = ($prestretch_val == $pt_val) ? "checked" : "";
                      ?>
                        <label class="border btn btn-warning <?php echo $is_active; ?>">
                          <input type="radio" name="preten" value="<?php echo $pt_val; ?>" autocomplete="off" <?php echo $is_checked; ?>> <?php echo $pt_val; ?>%
                        </label>
                      <?php } ?>
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <label class="mt-3">Racket</label>
                  <div class="form-group">
                    <div class="container">
                      <div class="row">
                        <div class="col-10">
                          <select class="form-control searchable-dropdown" style="width:100%" name="racketid">
                            <option value="">Please select</option>
                            <?php while ($row_Recordset4 = mysqli_fetch_assoc($Recordset4)) {
                              $selected = ($row_Recordset4['racketid'] == $row_Recordset2['racketid']) ? "selected" : "";
                            ?>
                              <option value="<?php echo e($row_Recordset4['racketid']); ?>" <?php echo $selected; ?>>
                                <?php echo e($row_Recordset4['manuf']) . " " . e($row_Recordset4['model']); ?>
                              </option>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="col-2">
                          <a href="./addaracket.php" class="btn button-colours"><i class="fa-solid fa-plus"></i></a>
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
              <div class="container">
                <div class="row">
                  <div class="col-12">
                    <label for="discount">Discount (%):</label>
                    <input type="number" step="0.1" name="discount" id="discount" class="form-control txtField" value="<?php echo e($row_Recordset2['discount']); ?>">
                  </div>
                </div>
              </div>
              <div class="container mt-3">
                <div class="row">
                  <div class="col-12">
                    <label for="comments">Comments</label>
                    <textarea class="form-control" name="comments" id="comments" rows="3"><?php echo e($row_Recordset2['Notes']); ?></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <input type="hidden" name="editcustomer" value="1">
          <input type="hidden" name="customerid" value="<?php echo e($custid); ?>">
          <?php if (isset($_GET['calret'])) { ?>
            <input type="hidden" name="calret" value="<?php echo e($_GET['calret']); ?>">
          <?php } ?>

          <div class="mb-3 container">
            <div class="row">
              <div class="col-6">
                <input class="btn button-colours" type="submit" name="submit" value="Submit">
              </div>
              <div class="col-6">
                <a class="btn button-colours-alt float-right" href="./customers.php">Cancel</a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="container center">
    <div class="p-3 row">
      <div class="col-2"><a href="./addcustomer.php" type="button" class="dot fa-solid fa-plus fa-2x"></a></div>
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
                  <input type="hidden" name="custid" value="<?php echo e($custid); ?>">
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
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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

    // Slider Initialization
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