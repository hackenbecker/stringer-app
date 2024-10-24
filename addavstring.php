<?php require_once('Connections/wcba.php');
require_once('./menu.php');
//-------------------------------------------------------------------
// Initialize the session
if (!isset($_SESSION)) {
  session_start();
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
if (isset($_GET['marker'])) {
  $marker = $_GET['marker'];
} else {
  $marker = "1";
}
$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");
//-------------------------------------------------------
//load all of the DB Queries
if (isset($_POST['imsid'])) {
  $values = explode(':', $_POST['imsid']);
  $sql = "SELECT * FROM reel_lengths left join sport on reel_lengths.sport = sport.sportid WHERE reel_lengths.sport = '" .  $values['1'] . "' ORDER BY length asc";
  $Recordset1 = mysqli_query($conn, $sql) or die(mysqli_error($conn));
  $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
  $totalRows_Recordset1 = mysqli_num_rows($Recordset1);
}
//-------------------------------------------------------
$query_Recordset13 = "SELECT * FROM sport ORDER BY sportname ASC;";
$Recordset13 = mysqli_query($conn, $query_Recordset13) or die(mysqli_error($conn));
$row_Recordset13 = mysqli_fetch_assoc($Recordset13);
$totalRows_Recordset13 = mysqli_num_rows($Recordset13);
//-------------------------------------------------------
$query_Recordset2 = "SELECT * FROM string LEFT JOIN all_string ON string.stock_id = all_string.string_id LEFT JOIN sport ON all_string.sportid = sport.sportid WHERE empty = '0' ORDER BY string.stringid ASC";
$Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));
$row_Recordset2 = mysqli_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);
//-------------------------------------------------------
if (isset($_POST['sportid'])) {
  $sportid = $_POST['sportid'];
  $query_Recordset3 = "SELECT * FROM all_string WHERE sportid = '" . $sportid . "' ORDER BY brand ASC;";
  $Recordset3 = mysqli_query($conn, $query_Recordset3) or die(mysqli_error($conn));
  $row_Recordset3 = mysqli_fetch_assoc($Recordset3);
  $totalRows_Recordset3 = mysqli_num_rows($Recordset3);
  //-----------------------------------------------------
  $query_Recordset14 = "SELECT * FROM sport WHERE sportid = '" . $sportid . "' ORDER BY sportname ASC;";
  $Recordset14 = mysqli_query($conn, $query_Recordset14) or die(mysqli_error($conn));
  $row_Recordset14 = mysqli_fetch_assoc($Recordset14);
  $totalRows_Recordset14 = mysqli_num_rows($Recordset14);
  $sportname = $row_Recordset14['sportname'];
}
//-------------------------------------------------------
$query_Recordset6 = "SELECT * FROM stringjobs WHERE collection_date LIKE '___" . $current_month_numeric . "/" . $current_year . "%'ORDER BY job_id ASC;";
$Recordset6 = mysqli_query($conn, $query_Recordset6) or die(mysqli_error($conn));
$row_Recordset6 = mysqli_fetch_assoc($Recordset6);
$totalRows_Recordset6 = mysqli_num_rows($Recordset6);
//-------------------------------------------------------
$query_Recordset7 = "SELECT * FROM stringjobs ORDER BY job_id ASC;";
$Recordset7 = mysqli_query($conn, $query_Recordset7) or die(mysqli_error($conn));
$row_Recordset7 = mysqli_fetch_assoc($Recordset7);
$totalRows_Recordset7 = mysqli_num_rows($Recordset7);
//-------------------------------------------------------
$query_Recordset8 = "SELECT SUM(price) as SUM from stringjobs;";
$Recordset8 = mysqli_query($conn, $query_Recordset8) or die(mysqli_error($conn));
$row_Recordset8 = mysqli_fetch_assoc($Recordset8);
$sum = $row_Recordset8['SUM'];
//-------------------------------------------------------
$query_Recordset9 = "SELECT SUM(price) as SUM from stringjobs WHERE paid != '1';";
$Recordset9 = mysqli_query($conn, $query_Recordset9) or die(mysqli_error($conn));
$row_Recordset9 = mysqli_fetch_assoc($Recordset9);
$sum_owed = $row_Recordset9['SUM'];
$_SESSION['sum_owed'] = $sum_owed;
//------------------------------------------------------
if (isset($_POST['imsid'])) {
  $query_Recordset4 = "SELECT * FROM all_string WHERE string_id = '" . $values['0'] . "' ORDER BY brand ASC;";
  $Recordset4 = mysqli_query($conn, $query_Recordset4) or die(mysqli_error($conn));
  $row_Recordset4 = mysqli_fetch_assoc($Recordset4);
  $totalRows_Recordset4 = mysqli_num_rows($Recordset4);
}
//-------------------------------------------------------
$racketid = 0;
if (isset($_POST['customerid'])) {
  $query_Recordset6 = "SELECT * FROM customer LEFT JOIN string ON string.stringid = customer.pref_string LEFT JOIN rackets ON rackets.racketid = customer.racketid LEFT JOIN all_string ON all_string.string_id = string.stock_id WHERE cust_ID = " . $_POST['customerid'];
  $Recordset16 = mysqli_query($conn, $query_Recordset16) or die(mysqli_error($conn));
  $row_Recordset16 = mysqli_fetch_assoc($Recordset16);
  $totalRows_Recordset6 = mysqli_num_rows($Recordset16);
  do {
    $customerid = $row_Recordset16['cust_ID'];
    $customername = $row_Recordset16['Name'];
    $tension = $row_Recordset16['tension'];
    $prestretch = $row_Recordset16['prestretch'];
    $stringid = $row_Recordset16['pref_string'];
    $racketid = $row_Recordset16['racketid'];
  } while ($row_Recordset16 = mysqli_fetch_assoc($Recordset6));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script type="text/javascript" src="./js/theme.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="./css/bootstrap-datetimepicker.min.css" type="text/css" media="all" />
  <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.min.js"></script>
  <script type="text/javascript" src="./js/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript" src="./js/demo.js"></script>
  <!-- datatables styles -->
  <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
  <link rel="stylesheet" href="./css/style.css">
  <title>SDBA</title>
  <link rel="icon" type="image/png" href="./img/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="./img/favicon-16x16.png" sizes="16x16" />
</head>



<body id="home-section-results" data-spy="scroll" data-target="#main-nav">
  <?php //main nav menu
  echo $main_menus; ?>
  <div class="home-section diva">
    <div class="subheader"> </div>
    <p class="fxdtextb"><strong>ADD</strong> String</p>
    <div class="container my-1 mt-3 firstparaaltej">
      <div class="container  my-1 pb-3 px-1 firstparaej">
        <div class="container  px-1  pt-3 form-text">
          <div class="card cardvp mt-3">
            <div class="card-body">
              <div class=" mt-3 container">
                <div class="row">
                  <?php
                  if (isset($_POST['sportid'])) {
                  ?>
                    <div class="col-10">
                      <div class="form-group">
                        <?php echo "<h5 class='form-text'>Sport: " . $sportname . "</h5>"; ?>
                      </div>
                    </div>
                    <div class="col-2">
                    </div>
                  <?php
                  } else {
                  ?>
                    <div class="col-12">
                      <div class="form-group ">
                        <form method="post" action="">
                          <select class="form-control input-sm" value="" style="width:100%" name="sportid" onchange="this.form.submit()">
                            <option value="0">Select Sport</option>
                            <?php if ($totalRows_Recordset13 > 0) {
                              do { ?>
                                <option value="<?php echo $row_Recordset13['sportid']; ?>">
                                  <?php echo $row_Recordset13['sportname']; ?>
                                </option>
                            <?php } while ($row_Recordset13 = mysqli_fetch_assoc($Recordset13));
                            }
                            ?>
                          </select>
                          <input type="hidden" name="postpositive" class="txtField" value="1">
                          <?php mysqli_data_seek($Recordset13, 0); ?>
                          <input type="hidden" name="postpositive1" class="txtField" value="1">
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
                            <select class="form-control" style="width:100%" name="imsid" onchange="this.form.submit()">
                              <option value="">Please select</option>
                              <?php do { ?>
                                <option value="<?php echo $row_Recordset3['string_id']; ?>:<?php echo $row_Recordset3['sportid']; ?>">
                                  <?php echo $row_Recordset3['brand'] . " " . $row_Recordset3['type']; ?>
                                </option>
                              <?php } while ($row_Recordset3 = mysqli_fetch_assoc($Recordset3));  ?>
                            </select>
                            <input type="hidden" name="sportid" class="txtField" value="<?php echo $sportid; ?>">
                            <input type="hidden" name="postpositive" class="txtField" value="1">
                          </form>
                          <?php mysqli_data_seek($Recordset3, 0); ?>
                        </div>
                        <div class="col-2">
                          <a href="./addamarketstring.php?marker=1" class="btn btn-success"><i class="fa-solid fa-plus"></i></a>
                        </div>
                      </div>
                    <?php } else {
                      echo "<strong>" . $row_Recordset4['brand'] . " " . $row_Recordset4['type'] . "</strong>";
                    } ?>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>
          <?php if (isset($_POST['imsid'])) { ?>
            <!--Purchase price form-->
            <form method="post" action="./db-update.php">
              <div class="card cardvp my-3">
                <div class="card-body">
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">
                        <div class="slidecontainer">
                          <p class="mt-3">Reel Purchase Price: <?php echo $currency; ?><span id="purchpriceV"></span></p>
                          <input type="range" min="0" max="250" class="slider" name="purchprice" id="purchprice">
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-group">
                        <div class="slidecontainer">
                          <p class="mt-3">Restring Price: <?php echo $currency; ?><span id="racketpriceV"></span></p>
                          <input type="range" min="0" max="30" class="slider" name="racketprice" id="racketprice">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--length form-->
              <div class="card cardvp my-3">
                <div class="card-body">
                  <div class="container">
                    <label class="mt-3">Reel Length</label>
                    <div class="row">
                      <div class="col-12">
                        <div class="form-group">
                          <select class="form-control" style="width:100%" name="length" required>
                            <option value="x">Please select</option>
                            <?php
                            do {
                              if (isset($row_Recordset1['reel_length_id'])) { ?>
                                <option value="<?php echo $row_Recordset1['reel_length_id']; ?>">
                                  <?php echo $row_Recordset1['length'] . $units . " (" . $row_Recordset1['sportname'] . ")"; ?>
                                </option>
                            <?php }
                            } while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));
                            ?>
                          </select>
                          <?php mysqli_data_seek($Recordset1, 0); ?>
                        </div>
                      </div>
                    </div>
                    <label class="mt-3">Reel Start Number (Usually 0)</label>
                    <div class="row">
                      <div class="col-12">
                        <div class="form-group">
                          <select class="form-control" style="width:100%" name="startnumber" required>
                            <option value="0" selected="selected">0</option>
                            <option value="0.5">0.5</option>
                            <option value="1">1</option>
                            <option value="1.5">1.5</option>
                            <option value="2">2</option>
                            <option value="2.5">2.5</option>
                            <option value="3">3</option>
                            <option value="3.5">3.5</option>
                            <option value="4">4</option>
                            <option value="4.5">4.5</option>
                            <option value="5">5</option>
                            <option value="5.5">5.5</option>
                            <option value="6">6</option>
                            <option value="6.5">6.5</option>
                            <option value="7">7</option>
                            <option value="7.5">7.5</option>
                            <option value="8">8</option>
                            <option value="8.5">8.5</option>
                            <option value="9">9</option>
                            <option value="9.5">9.5</option>
                            <option value="10">10</option>
                            <option value="10.5">10.5</option>
                            <option value="11">11</option>
                            <option value="11.5">11.5</option>
                            <option value="12">12</option>
                            <option value="12.5">12.5</option>
                            <option value="13">13</option>
                            <option value="13.5">13.5</option>
                            <option value="14">14</option>
                            <option value="14.5">14.5</option>
                            <option value="15">15</option>
                            <option value="15.5">15.5</option>
                            <option value="16">16</option>
                            <option value="16.5">16.5</option>
                            <option value="17">17</option>
                            <option value="17.5">17.5</option>
                            <option value="18">18</option>
                            <option value="18.5">18.5</option>
                            <option value="19">19</option>
                            <option value="19.5">19.5</option>
                            <option value="20">20</option>
                            <option value="20.5">20.5</option>
                            <option value="21">21</option>
                            <option value="21.5">21.5</option>
                            <option value="22">22</option>
                            <option value="22.5">22.5</option>
                            <option value="23">23</option>

                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12">
                        <div class="form-group">
                          <label class="mt-3">Purchase date</label>
                          <div class="form-group">
                            <div class="input-group date" id="id_4">
                              <input type="text" value="" name="datepurch" class="form-control" required />
                              <div class="input-group-addon input-group-append">
                                <div class="input-group-text">
                                  <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--Notes form-->
              <div class="card cardvp my-3">
                <div class="card-body">
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">
                        <label for="comments">Reel Notes</label>
                        <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
                      </div>
                    </div>
                  </div>
                  <!--owner supplied form-->
                  <div class="row">
                    <div class="col-6">
                      <div class="form-check">
                        <input class="form-check-input" type="hidden" name="ownersupplied" value="no" id="ownersupplied">
                        <input class="form-check-input" type="checkbox" name="ownersupplied" value="yes" id="ownersupplied">
                        <label class="form-check-label" for="ownersupplied">
                          Owner Supplied
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
        </div>
        <input type="hidden" name="marker" class="txtField" value="<?php echo $marker; ?>">
        <input type="hidden" name="stockid" class="txtField" value="<?php echo $values['0']; ?>">
        <input type="hidden" name="addstringflag" class="txtField" value="1">
        <?php
            if (isset($_POST['calret'])) { ?>
          <input type="hidden" name="calret" class="txtField" value="<?php echo $_POST['calret']; ?>">
        <?php } ?>
        <div class="container mt-3" style="margin-top: 120px;">
          <div class="row pt-3">
            <div class="col-6">
              <div>
                <input class="btn button-colours" type="submit" name="submitaddstockstring" value="Submit">
              </div>
            </div>
            <div class="col-6">
              <div>
                <a class="btn button-colours-alt float-right" href="./string.php">Cancel</a>
                </form>
              <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container center">
    <div class="p-3 row">
      <div class="col-2">
        <a href="./addavstring.php" type="button" class="dot fa-solid fa-plus fa-2x"></a>
      </div>
      <?php if (!empty($_SESSION['message'])) { ?>
        <div class="col-2">
          <h3 class="blinking" title="Warning Messages" data-toggle="modal" data-target="#warningModal"><strong>!</strong></h3>
        </div>
      <?php } else { ?>
        <div class="col-2">
          <h3 class="dotb" title="Warning Messages"><strong>!</strong></h3>
        </div>
      <?php } ?>
      <div class="col-2">
        <h3 class="dotbt h6 " title="Restrings for <?php echo $current_month_text; ?>"><?php echo $totalRows_Recordset6 ?></h3>
      </div>
      <div class="col-2">
        <a href="#" class="dotbt h6" title="Total restrings"><?php echo $totalRows_Recordset7 ?></a>
      </div>
      <div class="col-2">
        <a href="./jobs-unpaid.php" class="dotbt h6" title="Amount Owed"><?php echo "$currency" . $sum_owed ?></a>
      </div>
      <div class="col-2">
        <a href="#" class="dotbtt h7" title="Total Income"><small><?php echo "$currency" . $sum ?></small></a>
      </div>
    </div>
  </div>
  <!-- Information modal -->
  <div class="modal  fade text-dark" id="warningModal">
    <div class="modal-dialog">
      <div class="modal-content  border radius">
        <div class="modal-header modal_header">
          <h5 class=" modal-title">Information:</h5>
          <button class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body  modal_body">
          <div><?php echo $_SESSION['message']; ?>
          </div>
          <div style="padding-bottom:5px;">
          </div>
        </div>
        <div class="modal-footer modal_footer">
          <div class="container mt-3" style="margin-top: 120px;">
            <div class="row pt-3">
              <div class="col-8">
                <div>
                  <a class="btn modal_button_cancel" href="./addavstring.php">Cancel</a>
                </div>
              </div>
              <div class="col-4">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                  <input class="btn modal_button_submit float-right" type="submit" name="submitclearmessage" value="Clear">
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    var slider = document.getElementById("purchprice");
    var output = document.getElementById("purchpriceV");
    output.innerHTML = slider.value;
    slider.oninput = function() {
      output.innerHTML = this.value;
    }
    var slidera = document.getElementById("racketprice");
    var outputa = document.getElementById("racketpriceV");
    outputa.innerHTML = slidera.value;
    slidera.oninput = function() {
      outputa.innerHTML = this.value;
    }
  </script>
  <script>
    // Get the current year for the copyright
    $('#year').text(new Date().getFullYear());
    // Init Scrollspy
    $('body').scrollspy({
      target: '#main-nav'
    });
    // Smooth Scrolling
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
  </script>
  <script>
    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.querySelector(".nav-menu");
    hamburger.addEventListener("click", mobileMenu);

    function mobileMenu() {
      hamburger.classList.toggle("active");
      navMenu.classList.toggle("active");
    }
    const navLink = document.querySelectorAll(".nav-link");
  </script>
  <script>
    jQuery(document).ready(function($) {
      $('#tblUser').DataTable({
        order: [
          [0, 'desc']
        ]
      });
    });
  </script>
  <script>
    output$(function() {
      $('.datepicker').datepicker({
        language: "es",
        autoclose: true,
        format: "dd/mm/yyyy"
      });
    });
  </script>
  <script type="text/javascript">
    document.getElementById('themeSwitch').addEventListener('change', function(event) {
      (event.target.checked) ? document.body.setAttribute('data-theme', 'dark'): document.body.removeAttribute('data-theme');
    });
  </script>
  <script>
    var themeSwitch = document.getElementById('themeSwitch');
    if (themeSwitch) {
      themeSwitch.addEventListener('change', function(event) {
        resetTheme(); // update color theme
      });

      function resetTheme() {
        if (themeSwitch.checked) { // dark theme has been selected
          document.body.setAttribute('data-theme', 'dark');
          document.getElementById("imglogo").src = "./img/logo-dark.png";
          localStorage.setItem('themeSwitch', 'dark'); // save theme selection 
        } else {
          document.body.removeAttribute('data-theme');
          document.getElementById("imglogo").src = "./img/logo.png";
          localStorage.removeItem('themeSwitch'); // reset theme selection 
        }
      };
    }
  </script>
  <script>
    var themeSwitch = document.getElementById('themeSwitch');
    if (themeSwitch) {
      initTheme(); // on page load, if user has already selected a specific theme -> apply it
      document.getElementById("imglogo").src = "./img/logo-dark.png";

      themeSwitch.addEventListener('change', function(event) {
        resetTheme(); // update color theme
      });

      function initTheme() {
        var darkThemeSelected = (localStorage.getItem('themeSwitch') !== null && localStorage.getItem('themeSwitch') === 'dark');
        // update checkbox
        themeSwitch.checked = darkThemeSelected;
        // update body data-theme attribute
        darkThemeSelected ? document.body.setAttribute('data-theme', 'dark') : document.body.removeAttribute('data-theme');
      };

      function resetTheme() {
        if (themeSwitch.checked) { // dark theme has been selected
          document.body.setAttribute('data-theme', 'dark');
          document.getElementById("imglogo").src = "./img/logo-dark.png";
          localStorage.setItem('themeSwitch', 'dark'); // save theme selection 
        } else {
          document.body.removeAttribute('data-theme');
          document.getElementById("imglogo").src = "./img/logo.png";
          localStorage.removeItem('themeSwitch'); // reset theme selection 
        }
      };
    }
  </script>
  <script>
    var imgsrc = localStorage.getItem('themeSwitch');
    if (imgsrc == "dark") {
      document.getElementById("imglogo").src = "./img/logo-dark.png";
    } else {
      document.getElementById("imglogo").src = "./img/logo.png";

    }
  </script>
</body>

</html>