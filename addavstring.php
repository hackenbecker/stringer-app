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
if (isset($_POST['submitclearmessage'])) {
  unset($_SESSION['message']);
}

$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");
//load all of the DB Queries
//-------------------------------------------------------
$query_Recordset2 = "SELECT * FROM string LEFT JOIN all_string ON string.stock_id = all_string.string_id LEFT JOIN sport ON all_string.sportid = sport.sportid WHERE empty = '0' ORDER BY string.stringid ASC";
$Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));
$row_Recordset2 = mysqli_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);
//-------------------------------------------------------
$query_Recordset3 = "SELECT * FROM all_string ORDER BY brand ASC;";
$Recordset3 = mysqli_query($conn, $query_Recordset3) or die(mysqli_error($conn));
$row_Recordset3 = mysqli_fetch_assoc($Recordset3);
$totalRows_Recordset3 = mysqli_num_rows($Recordset3);
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
//-------------------------------------------------------

$racketid = 0;

if (isset($_POST['customerid'])) {

  $query_Recordset6 = "SELECT * FROM customer LEFT JOIN string ON string.stringid = customer.pref_string LEFT JOIN rackets ON rackets.racketid = customer.racketid LEFT JOIN all_string ON all_string.string_id = string.stock_id WHERE cust_ID = " . $_POST['customerid'];


  $Recordset6 = mysqli_query($conn, $query_Recordset6) or die(mysqli_error($conn));
  $row_Recordset6 = mysqli_fetch_assoc($Recordset6);
  $totalRows_Recordset6 = mysqli_num_rows($Recordset6);

  do {
    $customerid = $row_Recordset6['cust_ID'];
    $customername = $row_Recordset6['Name'];
    $tension = $row_Recordset6['tension'];
    $prestretch = $row_Recordset6['prestretch'];
    $stringid = $row_Recordset6['pref_string'];
    $racketid = $row_Recordset6['racketid'];
  } while ($row_Recordset6 = mysqli_fetch_assoc($Recordset6));
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
</head>

<body id="home-section-results" data-spy="scroll" data-target="#main-nav">
  <?php //main nav menu

  echo $main_menus; ?>


  <div class="home-section diva">
    <div class="subheader"> </div>
    <p class="fxdtext"><strong>ADD</strong> String</p>
    <div class="container my-1  firstparaaltej">
      <div class="container  my-1 pb-3 px-1 firstparaej">
        <div class="container  px-1  pt-3 form-text">
          <form method="post" action="./db-update.php">

            <div class="container mt-3" style="margin-top: 120px;">
              <div class="row pt-3">
                <div class="col-9">
                  <div>
                    <input class="btn button-colours" type="submit" name="submit" value="Submit">
                  </div>
                </div>
                <div class="col-3">
                  <div>
                    <a class="btn button-colours-alt" href="./string.php">Cancel</a>
                  </div>
                </div>
              </div>
            </div>

            <div class="card cardvp my-3">
              <div class="card-body">
                <label>Base reel</label>
                <div class="form-inline">
                  <div class="container">
                    <div class="row">
                      <div class="col-10">
                        <select class="form-control" style="width:100%" name="stockid">
                          <option>Please select</option>
                          <?php do { ?>
                            <option value="<?php echo $row_Recordset3['string_id']; ?>">
                              <?php echo $row_Recordset3['brand'] . " " . $row_Recordset3['type'] . " " . $row_Recordset3['length'] . "m"; ?>
                            </option>
                          <?php } while ($row_Recordset3 = mysqli_fetch_assoc($Recordset3));  ?>
                        </select>
                        <?php mysqli_data_seek($Recordset3, 0); ?>
                      </div>
                      <div class="col-2">
                        <a href="./addamarketstring.php?marker=1" class="btn btn-success"><i class="fa-solid fa-plus"></i></a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!--Purchase price form-->

            <div class="card cardvp my-3">
              <div class="card-body">
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <div class="slidecontainer">
                        <p class="mt-3">Reel Purchase Price: £<span id="purchpriceV"></span></p>
                        <input type="range" min="0" max="250" class="slider" name="purchprice" id="purchprice">
                      </div>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="form-group">
                      <div class="slidecontainer">
                        <p class="mt-3">Restring Price: £<span id="racketpriceV"></span></p>
                        <input type="range" min="0" max="30" class="slider" name="racketprice" id="racketprice">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!--date form-->

            <div class="card cardvp my-3">
              <div class="card-body">
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
        <input type="hidden" name="addstringflag" class="txtField" value="1">
        <?php
        if (isset($_POST['calret'])) { ?>
          <input type="hidden" name="calret" class="txtField" value="<?php echo $_POST['calret']; ?>">
        <?php } ?>

        <div class="container mt-3" style="margin-top: 120px;">
          <div class="row pt-3">
            <div class="col-9">
              <div>
                <input class="btn button-colours" type="submit" name="submit" value="Submit">
              </div>
            </div>
            <div class="col-3">
              <div>
                <a class="btn button-colours-alt" href="./string.php">Cancel</a>
              </div>
            </div>
          </div>
        </div>
        </form>
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
                  <a class="btn modal_button_cancel" href="./addavstring.php">Cancel</a>
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
</body>

</html>