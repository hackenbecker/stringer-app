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

if (isset($_GET['marker'])) {
  $marker = $_GET['marker'];
} else {
  $marker = "1";
}
//-------------------------------------------------------
$query_Recordset4 = "SELECT * FROM rackets ORDER BY manuf ASC;";
$Recordset4 = mysqli_query($conn, $query_Recordset4) or die(mysqli_error($conn));
$row_Recordset4 = mysqli_fetch_assoc($Recordset4);
$totalRows_Recordset4 = mysqli_num_rows($Recordset4);
//-------------------------------------------------------
$query_Recordset5 = "SELECT * FROM all_string ORDER BY brand ASC";
$Recordset5 = mysqli_query($conn, $query_Recordset5) or die(mysqli_error($conn));
$row_Recordset5 = mysqli_fetch_assoc($Recordset5);
$totalRows_Recordset5 = mysqli_num_rows($Recordset5);
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

<body data-spy="scroll" data-target="#main-nav">
  <?php //main nav menu

  echo $main_menus; ?>


  <div class="home-section diva">
    <div class="subheader"> </div>
    <p class="fxdtextb"><strong>ADD</strong> Customer</p>
    <div class="container my-1  firstparaaltej">
      <div class="container  my-1 pb-3 px-1 firstparaej">
        <div class="container  px-1  pt-3 form-text">
          <form method="post" action="./db-update.php">

            <div class="container mt-3" style="margin-top: 120px;">
              <div class="row pt-3">
                <div class="col-6">
                  <div>
                    <input class="btn button-colours" type="submit" name="submitaddcust" value="Submit">
                  </div>
                </div>
                <div class="col-6">
                  <div>
                    <a class="btn button-colours-alt float-right" href="./customers.php">Cancel</a>
                  </div>
                </div>
              </div>
            </div>

            <div class="card cardvp my-3">
              <div class="card-body">
                <!-- Customer section-->
                <label>Customer Name</label>
                <div>
                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <input type="text" name="customername" class="form-control txtField">
                      </div>
                    </div>
                  </div>
                </div>
                <label class="mt-3">Email</label>
                <div>
                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <input type="text" name="customeremail" class="form-control txtField">
                      </div>
                    </div>
                  </div>
                </div>
                <label class="mt-3">Mobile</label>
                <div>
                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <input type="text" name="customermobile" class="form-control txtField">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <!--string form mains-->
            <div class="card cardvp px-3 pb-3">
              <div class="card-body"></div>
              <label>Preferred Main String</label>
              <div class="form-inline">
                <div class="container">
                  <div class="row">
                    <div class="col-10">
                      <select class="form-control" style="width:100%" name="stringid">
                        <option>Please select</option>
                        <?php if ($totalRows_Recordset5 > 0) {
                          do { ?>
                            <option value="<?php echo $row_Recordset5['string_id']; ?>">
                              <?php echo $row_Recordset5['brand'] . " " . $row_Recordset5['type']; ?>
                            </option>
                        <?php } while ($row_Recordset5 = mysqli_fetch_assoc($Recordset5));
                        } ?>
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
                        <?php if ($totalRows_Recordset5 > 0) {
                          do { ?>
                            <?php if (isset($row_Recordset5['string_id'])) { ?>
                              <option value="<?php echo $row_Recordset5['string_id']; ?>">
                                <?php echo $row_Recordset5['brand'] . " " . $row_Recordset5['type']; ?>
                              </option>
                            <?php } ?>
                        <?php } while ($row_Recordset5 = mysqli_fetch_assoc($Recordset5));
                        } ?>
                      </select>
                      <?php mysqli_data_seek($Recordset5, 0); ?>
                    </div>
                    <div class="col-2">
                      <a href="./addamarketstring.php?marker=2" class="btn button-colours"><i class="fa-solid fa-plus"></i></a>
                    </div>
                  </div>
                </div>
              </div>

              <!--Racket form-->
              <label class="mt-3">Preferred Racket</label>
              <div class="form-inline">
                <div class="container">
                  <div class="row">
                    <div class="col-10">
                      <select class="form-control" style="width:100%" name="racketid">
                        <option>Please select</option>
                        <?php if ($totalRows_Recordset4 > 0) {

                          do { ?>
                            <option value="<?php echo $row_Recordset4['racketid']; ?>">
                              <?php echo $row_Recordset4['manuf'] . " " . $row_Recordset4['model']; ?>
                            </option>
                        <?php } while ($row_Recordset4 = mysqli_fetch_assoc($Recordset4));
                        } ?>
                      </select>
                      <?php mysqli_data_seek($Recordset4, 0); ?>
                    </div>
                    <div class="col-2">
                      <a href="./addaracket.php?marker=2" class="btn button-colours"><i class="fa-solid fa-plus"></i></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!--Tension form--mains-->
            <div class="card cardvp px-3 mt-3 pb-3">
              <div class="card-body">
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <div class="slidecontainer">
                        <p class="mt-3">Tension Mains (lbs): <span id="tensionmV"></span></p>
                        <input type="range" min="0" max="70" class="slider" name="tension" id="tensionm">
                      </div>
                    </div>
                  </div>
                  <!--Tension form--crosses-->
                  <div class="col-12">
                    <div class="form-group">
                      <div class="slidecontainer">
                        <p class="mt-3">Tension Crosses (lbs): <span id="tensioncV"></span></p>
                        <input type="range" min="0" max="70" class="slider" name="tensionc" id="tensionc">
                      </div>
                    </div>
                  </div>
                  <!--Pre-Tension form-->
                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <div class="form-group">
                          <p class="mt-3">Pre-Stretch:</p>

                          <div class="col-12 btn-group btn-group-toggle" role="group" data-toggle="buttons">

                            <label class="border btn btn-warning">
                              <input type="radio" name="preten" id="option1" value="0" autocomplete="off" selected="selected"> 0%
                            </label>

                            <label class="border btn btn-warning">
                              <input type="radio" name="preten" id="option2" value="5" autocomplete="off"> 5%
                            </label>

                            <label class="border btn btn-warning">
                              <input type="radio" name="preten" id="option3" value="10" autocomplete="off"> 10%
                            </label>

                            <label class="border btn btn-warning">
                              <input type="radio" name="preten" id="option4" value="15" autocomplete="off"> 15%
                            </label>

                            <label class="border btn btn-warning">
                              <input type="radio" name="preten" id="option5" value="20" autocomplete="off"> 20%
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
      </div>






      <div class="card cardvp px-3 mt-3 pb-3">
        <div class="card-body"> <!--comments form-->
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
            <div>
              <input type="hidden" name="marker" class="txtField" value="<?php echo $marker; ?>">
              <input class="btn button-colours" type="submit" name="submitaddcust" value="Submit">

            </div>
          </div>
          <div class="col-6">
            <div>
              <a class="btn button-colours-alt float-right" href="./customers.php">Cancel</a>
            </div>
          </div>
        </div>
      </div>

      </form>
    </div>
  </div>
  </section>
  <div class="container center">
    <div class="p-3 row">

      <div class="col-2">
        <a href="#" type="button" class="dot fa-solid fa-plus fa-2x"></a>
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
                  <a class="btn modal_button_cancel" href="./customers.php">Cancel</a>
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