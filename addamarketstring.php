<?php require_once('./Connections/wcba.php');
require_once('./menu.php');

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
if (isset($_GET['marker'])) {
  $marker = $_GET['marker'];
} else {
  $marker = "1";
}

$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");

$query_Recordset1 = "SELECT * FROM sport ORDER BY sportid DESC";
$Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);
//-------------------------------------------------------------------
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
    <p class="fxdtext"><strong>ADD</strong> In Market String</p>
    <div class="container my-1  firstparaaltej">
      <div class="container  my-1 pb-3 px-1 firstparaej">
        <div class="container  px-1  pt-3 form-text">
          <form method="post" action="./db-update.php">

            <div class="container mt-3" style="margin-top: 120px;">
              <div class="row pt-3">
                <div class="col-9">
                  <div>
                    <input class="btn button-colours" type="submit" name="submitaddIMS" value="Submit">
                  </div>
                </div>
                <div class="col-3">
                  <div>
                    <a class="btn button-colours-alt" href="./string-im.php">Cancel</a>
                  </div>
                </div>
              </div>
            </div>


            <div class="card cardvp my-3">
              <div class="card-body"> <label>Brand</label>
                <div>
                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <input type="text" name="brand" class="form-control txtField">
                      </div>
                    </div>
                  </div>
                </div>


                <label class="mt-3">Type</label>
                <div>
                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <input type="text" name="type" class="form-control txtField">
                      </div>
                    </div>
                  </div>
                </div>


                <label class="mt-3">Reel Length</label>
                <div class="form-inline">
                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <select class="form-control" style="width:100%" name="length">
                          <option>Please select</option>
                          <option value="10">10m</option>
                          <option value="10">12m</option>
                          <option value="20">20m</option>
                          <option value="30">30m</option>
                          <option value="40">40m</option>
                          <option value="50">50m</option>
                          <option value="100">100m</option>
                          <option value="150">150m</option>
                          <option value="200">200m</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>





            <!--sport form-->

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
                              <option>Please select</option>
                              <?php do { ?>
                                <option value="<?php echo $row_Recordset1['sportid']; ?>">
                                  <?php echo $row_Recordset1['sportname']; ?>
                                </option>
                              <?php } while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1)); ?>
                            </select>
                            <?php mysqli_data_seek($Recordset1, 0); ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!--Notes form-->

                <div class="row">
                  <div class="col-12">
                    <label class="mt-3">String Notes</label>
                    <div>
                      <div class="container">
                        <div class="row">
                          <div class="col-12">
                            <textarea class="form-control mb-3" name="notes" id="notes" rows="3"></textarea>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <input type="hidden" name="addmarketstring" class="txtField" value="1">
            <input type="hidden" name="stockid" class="txtField" value="<?php echo $row_Recordset2['stock_id']; ?>">
            <?php
            if (isset($_GET['marker'])) { ?>
              <input type="hidden" name="marker" class="txtField" value="<?php echo $marker; ?>">
            <?php } ?>

            <div class="container mt-3" style="margin-top: 120px;">
              <div class="row pt-3">
                <div class="col-9">
                  <div>
                    <input class="btn button-colours" type="submit" name="submitaddIMS" value="Submit">
                  </div>
                </div>
                <div class="col-3">
                  <div>
                    <a class="btn button-colours-alt" href="./string-im.php">Cancel</a>
                  </div>
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

      <div class="col-2">
        <a href="./addamarketstring.php" type="button" class="dot fa-solid fa-plus fa-2x"></a>
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
    output.innerHTML = slidera.value;

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