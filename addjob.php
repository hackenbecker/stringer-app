<?php require_once('./Connections/wcba.php');
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

$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");

if (isset($_POST['submitclearmessage'])) {
  unset($_SESSION['message']);
}

if (isset($_GET['customerid'])) {
  $_POST['customerid'] = $_GET['customerid'];
}
//load all of the DB Queries

//-------------------------------------------------------
$query_Recordset2 = "SELECT * FROM string LEFT JOIN all_string ON string.stock_id = all_string.string_id WHERE empty = '0' ORDER BY string.stringid ASC;";
$Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));
$row_Recordset2 = mysqli_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);
//-------------------------------------------------------
$query_Recordset7 = "SELECT * FROM string LEFT JOIN all_string ON string.stock_id = all_string.string_id WHERE empty = '0' ORDER BY string.stringid ASC;";
$Recordset7 = mysqli_query($conn, $query_Recordset7) or die(mysqli_error($conn));
$row_Recordset7 = mysqli_fetch_assoc($Recordset7);
$totalRows_Recordset7 = mysqli_num_rows($Recordset7);
//-------------------------------------------------------
$query_Recordset3 = "SELECT * FROM customer ORDER BY Name ASC;";
$Recordset3 = mysqli_query($conn, $query_Recordset3) or die(mysqli_error($conn));
$row_Recordset3 = mysqli_fetch_assoc($Recordset3);
$totalRows_Recordset3 = mysqli_num_rows($Recordset3);
//-------------------------------------------------------
$query_Recordset4 = "SELECT * FROM rackets ORDER BY manuf ASC;";
$Recordset4 = mysqli_query($conn, $query_Recordset4) or die(mysqli_error($conn));
$row_Recordset4 = mysqli_fetch_assoc($Recordset4);
$totalRows_Recordset4 = mysqli_num_rows($Recordset4);
//-------------------------------------------------------
$query_Recordset5 = "SELECT * FROM grip;";
$Recordset5 = mysqli_query($conn, $query_Recordset5) or die(mysqli_error($conn));
$row_Recordset5 = mysqli_fetch_assoc($Recordset5);
$totalRows_Recordset5 = mysqli_num_rows($Recordset5);
$racketid = 0;
$prestretch = 0;
if ((isset($_POST['customerid'])) && ($_POST['customerid'] != 0)) {


  $query_Recordset6 = "SELECT *,
stockmains.string_id AS st_ma_id,
stockcrosses.string_id AS st_cr_id,  
crosses.stock_id AS cr_id,  
mains.stock_id AS ma_id,

stockmains.brand AS st_ma_br,
stockcrosses.brand AS st_cr_br,
stockmains.type AS st_ma_ty,
stockcrosses.type AS st_cr_ty

FROM customer 
LEFT JOIN all_string AS stockmains ON stockmains.string_id = customer.pref_string 
LEFT JOIN all_string AS stockcrosses ON stockcrosses.string_id = customer.pref_stringc 
LEFT JOIN rackets ON rackets.racketid = customer.racketid 
LEFT JOIN string AS mains ON stockmains.string_id = mains.stock_id 
LEFT JOIN string AS crosses ON stockcrosses.string_id = crosses.stock_id 
WHERE mains.empty = '0' AND crosses.empty = '0' AND cust_ID = " . $_POST['customerid'];


  $Recordset6 = mysqli_query($conn, $query_Recordset6) or die(mysqli_error($conn));
  $row_Recordset6 = mysqli_fetch_assoc($Recordset6);
  $totalRows_Recordset6 = mysqli_num_rows($Recordset6);
  mysqli_data_seek($Recordset6, 0);

  if ($totalRows_Recordset6 == 0) {
    $query_Recordset6 = "SELECT *,
stockmains.string_id AS st_ma_id,
stockcrosses.string_id AS st_cr_id,  
crosses.stock_id AS cr_id,  
mains.stock_id AS ma_id,

stockmains.brand AS st_ma_br,
stockcrosses.brand AS st_cr_br,
stockmains.type AS st_ma_ty,
stockcrosses.type AS st_cr_ty

FROM customer 
LEFT JOIN all_string AS stockmains ON stockmains.string_id = customer.pref_string 
LEFT JOIN all_string AS stockcrosses ON stockcrosses.string_id = customer.pref_stringc 
LEFT JOIN rackets ON rackets.racketid = customer.racketid 
LEFT JOIN string AS mains ON stockmains.string_id = mains.stock_id 
LEFT JOIN string AS crosses ON stockcrosses.string_id = crosses.stock_id 
WHERE cust_ID = " . $_POST['customerid'];

    $Recordset6 = mysqli_query($conn, $query_Recordset6) or die(mysqli_error($conn));
    $row_Recordset6 = mysqli_fetch_assoc($Recordset6);
    $totalRows_Recordset6 = mysqli_num_rows($Recordset6);
  }
  $customerid = $row_Recordset6['cust_ID'];
  $customername = $row_Recordset6['Name'];
  $tension = $row_Recordset6['tension'];
  $tensionc = $row_Recordset6['tensionc'];
  $prestretch = $row_Recordset6['prestretch'];
  $stringidm = $row_Recordset6['pref_string'];
  $stringidc = $row_Recordset6['pref_stringc'];
  $racketid = $row_Recordset6['racketid'];
}
$query_Recordset9 = "SELECT * FROM stringjobs ORDER BY job_id ASC;";
$Recordset9 = mysqli_query($conn, $query_Recordset9) or die(mysqli_error($conn));
$row_Recordset9 = mysqli_fetch_assoc($Recordset9);
$totalRows_Recordset9 = mysqli_num_rows($Recordset9);

$query_Recordset10 = "SELECT * FROM stringjobs WHERE collection_date LIKE '___" . $current_month_numeric . "/" . $current_year . "%'ORDER BY job_id ASC;";
$Recordset10 = mysqli_query($conn, $query_Recordset10) or die(mysqli_error($conn));
$row_Recordset10 = mysqli_fetch_assoc($Recordset10);
$totalRows_Recordset10 = mysqli_num_rows($Recordset10);
//-------------------------------------------------------
$query_Recordset11 = "SELECT SUM(price) as SUM from stringjobs;";
$Recordset11 = mysqli_query($conn, $query_Recordset11) or die(mysqli_error($conn));
$row_Recordset11 = mysqli_fetch_assoc($Recordset11);
$sum = $row_Recordset11['SUM'];
//-------------------------------------------------------
$query_Recordset12 = "SELECT SUM(price) as SUM from stringjobs WHERE paid != '1';";
$Recordset12 = mysqli_query($conn, $query_Recordset12) or die(mysqli_error($conn));
$row_Recordset12 = mysqli_fetch_assoc($Recordset12);
$sum_owed = $row_Recordset12['SUM'];
$_SESSION['sum_owed'] = $sum_owed;
//-------------------------------------------------------
//-------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
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
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>

  <title>SDBA</title>
</head>

<body id="home-section-results" data-spy="scroll" data-target="#main-nav">
  <?php //main nav menu

  echo $main_menus; ?>

  <!-- HOME SECTION -->

  <div class="home-section diva">
    <div class="subheader"></div>
    <p class="fxdtext"><strong>Add</strong> Restring</p>
    <div class="container my-3  firstparaalt">
      <div class="card cardvp" style="margin-top: 60px;">
        <div class="card-body">
          <div class=" mt-3 container">
            <div class="row">
              <?php
              if (isset($customerid)) {
              ?>
                <div class="col-10">
                  <div class="form-group">
                    <?php echo "<h5 class='form-text'>Customer: " . $customername . "</h5>"; ?>
                  </div>
                </div>
                <div class="col-2">

                </div>
              <?php
              } else {
              ?>
                <div class="col-10">
                  <div class="form-group ">
                    <form method="post" action="">
                      <select class="form-control input-sm" value="<?php echo $_SERVER['PHP_SELF']; ?>" style="width:100%" name="customerid" onchange="this.form.submit()">
                        <option value="0">Select Customer</option>

                        <?php if ($totalRows_Recordset3 > 0) {
                          do { ?>
                            <option value="<?php echo $row_Recordset3['cust_ID']; ?>">
                              <?php echo $row_Recordset3['Name']; ?>
                            </option>
                        <?php } while ($row_Recordset3 = mysqli_fetch_assoc($Recordset3));
                        }
                        ?>


                      </select>
                      <?php mysqli_data_seek($Recordset3, 0); ?>
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

      <div class="card cardvp mt-3">
        <div class="card-body">
          <!--String form-->
          <div class="container">
            <label class="pt-3 form-text">String (Mains)</label>
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <form method="post" action="./db-update.php" enctype="multipart/form-data">
                    <select class="form-control" style="width:100%" name="stringid">
                      <option>Please select</option>

                      <?php if ($totalRows_Recordset2 > 0) {

                        do {
                          if ($row_Recordset2['stock_id'] == $stringidm) { ?>
                            <option value="<?php echo $row_Recordset2['stringid']; ?>" selected="selected">
                              <?php echo $row_Recordset2['brand'] . " " . $row_Recordset2['type'] . " " . $row_Recordset2['note']; ?>
                            </option>
                          <?php } else { ?>

                            <option value="<?php echo $row_Recordset2['stringid']; ?>">
                              <?php echo $row_Recordset2['brand'] . " " . $row_Recordset2['type'] . " " . $row_Recordset2['note']; ?>
                            </option>
                          <?php } ?>
                      <?php } while ($row_Recordset2 = mysqli_fetch_assoc($Recordset2));
                      } ?>
                      <?php mysqli_data_seek($Recordset2, 0); ?>
                    </select>
                </div>
              </div>

            </div>
          </div>

          <!--String form-->
          <div class="container">
            <label class="mt-3 form-text">String (Crosses)</label>
            <div class="row">
              <div class="col-12">
                <div class="form-group">

                  <select class="form-control" style="width:100%" name="stringidc">
                    <option value="0">Same as mains</option>
                    <?php if ($totalRows_Recordset2 > 0) {

                      do {
                        if ($row_Recordset7['stock_id'] == $stringidc) { ?>
                          <option value="<?php echo $row_Recordset7['stringid']; ?>" selected="selected">
                            <?php echo $row_Recordset7['brand'] . " " . $row_Recordset7['type'] . " " . $row_Recordset7['note']; ?>
                          </option>
                        <?php } else { ?>

                          <option value="<?php echo $row_Recordset7['stringid']; ?>">
                            <?php echo $row_Recordset7['brand'] . " " . $row_Recordset7['type'] . " " . $row_Recordset7['note']; ?>
                          </option>
                        <?php } ?>
                    <?php } while ($row_Recordset7 = mysqli_fetch_assoc($Recordset7));
                    } ?>
                  </select>
                </div>

              </div>

            </div>
          </div>





          <!--Tension form-->

          <div class="container mt-3 p-3">
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <div class="slidecontainer">
                    <p class="mt-3 form-text">Tension Mains (lbs): <span id="tensionmV"></span></p>
                    <input type="range" min="0" max="70" value="<?php echo  $tension ?>" class="slider" name="tensionm" id="tensionm">
                  </div>
                </div>
              </div>


              <div class="col-12">
                <div class="form-group">
                  <div class="slidecontainer">
                    <p class="mt-3 form-text">Tension Crosses (lbs): <span id="tensioncV"></span></p>
                    <input type="range" min="0" max="70" value="<?php echo  $tensionc ?>" class="slider" name="tensionc" id="tensionc">
                  </div>
                </div>
              </div>


              <!--pre stretch form-->

              <div class="container">
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <p class="mt-3 form-text">Pre-Stretch:</p>

                      <div class="col-12 btn-group btn-group-toggle" role="group" data-toggle="buttons">

                        <?php if ($prestretch == 0) {
                          $checked = "checked";
                          $active = "active";
                        } else {
                          $checked = "";
                          $active = "";
                        } ?>
                        <label class="border btn btn-warning <?php echo $active; ?>">
                          <input type="radio" name="preten" id="option1" value="0" autocomplete="off" <?php echo $checked; ?>> 0%
                        </label>

                        <?php if ($prestretch == 5) {
                          $checked = "checked";
                          $active = "active";
                        } else {
                          $checked = "";
                          $active = "";
                        } ?>
                        <label class="border btn btn-warning <?php echo $active; ?>">
                          <input type="radio" name="preten" id="option1" value="0" autocomplete="off" <?php echo $checked; ?>> 5%
                        </label>

                        <?php if ($prestretch == 10) {
                          $checked = "checked";
                          $active = "active";
                        } else {
                          $checked = "";
                          $active = "";
                        } ?>
                        <label class="border btn btn-warning <?php echo $active; ?>">
                          <input type="radio" name="preten" id="option1" value="0" autocomplete="off" <?php echo $checked; ?>> 10%
                        </label>

                        <?php if ($prestretch == 15) {
                          $checked = "checked";
                          $active = "active";
                        } else {
                          $checked = "";
                          $active = "";
                        } ?>
                        <label class="border btn btn-warning <?php echo $active; ?>">
                          <input type="radio" name="preten" id="option1" value="0" autocomplete="off" <?php echo $checked; ?>> 15%
                        </label>

                        <?php if ($prestretch == 20) {
                          $checked = "checked";
                          $active = "active";
                        } else {
                          $checked = "";
                          $active = "";
                        } ?>
                        <label class="border btn btn-warning <?php echo $active; ?>">
                          <input type="radio" name="preten" id="option1" value="0" autocomplete="off" <?php echo $checked; ?>> 20%
                        </label>


                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>






      <!--Racket form-->
      <div class="card cardvp mt-3">
        <div class="card-body">
          <label class="mt-3 form-text">Racket</label>

          <div class="row">

            <div class="col-12">
              <select class="form-control" style="width:100%" name="racketid">
                <option value="Generic racket">Please select</option>
                <?php if ($totalRows_Recordset2 > 0) {

                  do {
                    if ($row_Recordset4['racketid'] == $racketid) { ?>
                      <option value="<?php echo $row_Recordset4['racketid']; ?>" selected="selected">
                        <?php echo $row_Recordset4['manuf'] . " " . $row_Recordset4['model']; ?>
                      </option>
                    <?php } else { ?>
                      <option value="<?php echo $row_Recordset4['racketid']; ?>">
                        <?php echo $row_Recordset4['manuf'] . " " . $row_Recordset4['model']; ?>
                      </option>
                    <?php } ?>
                <?php } while ($row_Recordset4 = mysqli_fetch_assoc($Recordset4));
                } ?>
              </select>
              <?php mysqli_data_seek($Recordset4, 0); ?>

              <div class="mt-3 custom-file">
                <input class="custom-file-input" name="image" placeholder="Take image" type="file" accept="image/*" capture="camera">
                <label class="custom-file-label" for="customFile">Choose file</label>
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
                      <div class="input-group-text">
                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                      </div>
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
                      <div class="input-group-text">
                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!--comments form-->
          </div>
        </div>
      </div>


      <!--grip form-->
      <div class="card cardvp mt-3">
        <div class="card-body">
          <div class="container">
            <div class="row">
              <div class="col-6">
                <div class="form-check">
                  <input class="form-check-input" type="hidden" name="gripreqd" value="0" id="grip">
                  <input class="form-check-input" type="checkbox" name="gripreqd" value="1" id="grip">
                  <label class="form-check-label form-text" for="grip">
                    Grip Required
                  </label>
                </div>
              </div>

              <!--free job form-->

              <div class="col-6">
                <div class="form-check">
                  <input class="form-check-input" type="hidden" name="freerestring" value="0" id="flexCheckDefault1">
                  <input class="form-check-input" type="checkbox" name="freerestring" value="1" id="flexCheckDefault1">
                  <label class="form-check-label form-text" for="flexCheckDefault1">
                    Free Restring
                  </label>
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
    </div>
    <input type="hidden" name="customerid" class="txtField" value="<?php echo $customerid;                                       ?>">
    <input type="hidden" name="addflag" class="txtField" value="1">

    <div class="container mt-3">
      <div class="row pb-3">
        <div class="col-9">
          <div>
            <input class="btn button-colours" type="submit" name="submitadd" value="Submit" class="buttom">
          </div>
        </div>
        <div class="col-3">
          <div>
            <a class="btn button-colours-alt" href="./string-jobs.php" class="buttom">Cancel</a>
          </div>
        </div>
      </div>
    </div>
    </form>
  </div>
  <div class="container center">
    <div class="p-3 row">

      <div class="col-2">
        <a href="./addjob.php" type="button" class="dot fa-solid fa-plus fa-2x"></a>
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
        <h3 class="dotbt h6 " title="Restrings for <?php echo $current_month_text; ?>"><?php echo $totalRows_Recordset10 ?></h3>
      </div>
      <div class="col-2">
        <a href="#" class="dotbt h6" title="Total restrings"><?php echo $totalRows_Recordset9 ?></a>

      </div>
      <div class="col-2">
        <a href="./jobs-unpaid.php" class="dotbt h6" title="Amount Owed"><?php echo "£" . $sum_owed ?></a>
      </div>
      <div class="col-2">
        <a href="#" class="dotbtt h7" title="Total Income"><small><?php echo "£" . $sum ?></small></a>
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
                  <a class="btn modal_button_cancel" href="./string.php">Cancel</a>
                </div>
              </div>
              <div class="col-4">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                  <input class="btn modal_button_submit" type="submit" name="submitclearmessage" value="Clear">
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    var sliderm = document.getElementById("tensionm");
    var outputm = document.getElementById("tensionmV");
    outputm.innerHTML = sliderm.value;

    sliderm.oninput = function() {
      outputm.innerHTML = this.value;
    }




    var sliderc = document.getElementById("tensionc");
    var outputc = document.getElementById("tensioncV");
    outputc.innerHTML = sliderc.value;

    sliderc.oninput = function() {
      outputc.innerHTML = this.value;
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
    $(function() {
      $.datepicker.parseDate("yy-mm-dd", "2007-01-26");

      $("#datepicker1").datepicker({

      });
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
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
      var fileName = $(this).val().split("\\").pop();
      $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
  </script>
</body>

</html>