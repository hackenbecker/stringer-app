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

// SECURITY: Cast marker to an integer to prevent XSS injection via the URL
$marker = isset($_GET['marker']) ? (int)$_GET['marker'] : 1;

$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");

// Fetch Sports
$query_Recordset1 = "SELECT sportid, sportname FROM sport ORDER BY sportid DESC";
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

  <title>SDBA - Add Racket</title>
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
    <div class="subheader"> </div>
    <p class="fxdtextb"><strong>ADD</strong> Racket</p>

    <div class="container my-1 firstparaaltej">
      <div class="container my-1 pb-3 px-1 firstparaej">
        <div class="container px-1 pt-3 form-text">
          <form method="post" action="./db-update.php">

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="container mt-3">
              <div class="row pt-3">
                <div class="col-6">
                  <input class="btn button-colours" type="submit" name="submitaddracket" value="Submit">
                </div>
                <div class="col-6">
                  <a class="btn button-colours-alt float-right" href="./rackets.php">Cancel</a>
                </div>
              </div>
            </div>

            <div class="card cardvp my-3">
              <div class="card-body">
                <label>Brand</label>
                <div class="container">
                  <div class="row">
                    <div class="col-12">
                      <input type="text" name="brand" class="form-control txtField" value="<?php echo isset($_SESSION['brand']) ? e($_SESSION['brand']) : ''; ?>">
                    </div>
                  </div>
                </div>

                <label class="mt-3 mb-3">Model</label>
                <div class="container mb-3">
                  <div class="row">
                    <div class="col-12">
                      <input type="text" name="type" class="form-control txtField" value="<?php echo isset($_SESSION['type']) ? e($_SESSION['type']) : ''; ?>">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card cardvp my-3">
              <div class="card-body">
                <div class="row">
                  <div class="col-12">
                    <label class="mt-1">Sport</label>
                    <div class="form-inline">
                      <div class="container">
                        <div class="row">
                          <div class="col-12">
                            <select class="form-control" style="width:100%" name="sport">
                              <option value="0">Please select</option>
                              <?php
                              $session_sport = $_SESSION['sport'] ?? '0';
                              while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1)) {
                                $is_selected = ($session_sport == $row_Recordset1['sportid']) ? "selected" : "";
                              ?>
                                <option value="<?php echo e($row_Recordset1['sportid']); ?>" <?php echo $is_selected; ?>>
                                  <?php echo e($row_Recordset1['sportname']); ?>
                                </option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-12">
                    <label class="mt-3">String Pattern</label>
                    <div class="container">
                      <div class="row">
                        <div class="col-12">
                          <textarea class="form-control mb-3" name="pattern" id="notes" rows="3"></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="container mt-3">
              <div class="row pt-3">
                <div class="col-6">
                  <input type="hidden" name="marker" class="txtField" value="<?php echo e($marker); ?>">
                  <input class="btn button-colours" type="submit" name="submitaddracket" value="Submit">
                </div>
                <div class="col-6">
                  <a class="btn button-colours-alt float-right" href="./rackets.php">Cancel</a>
                </div>
              </div>
            </div>

          </form>
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
                <a class="btn modal_button_cancel" href="./rackets.php">Cancel</a>
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