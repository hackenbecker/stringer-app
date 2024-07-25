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


if (isset($_POST['submitclearmessage'])) {
  unset($_SESSION['message']);
}


$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");


//load all of the DB Queries


//---------------------------------------------------
//load all of the DB Queries
$sql = "SELECT * FROM accounts";
$Recordset1 = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);
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
$sql = "SELECT * FROM grip";
$Recordset2 = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$row_Recordset2 = mysqli_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);
//-------------------------------------------------------
$sqla = "SELECT * FROM settings where id ='2'";
$Recordset5 = mysqli_query($conn, $sqla) or die(mysqli_error($conn));
$row_Recordset5 = mysqli_fetch_assoc($Recordset5);
$totalRows_Recordset5 = mysqli_num_rows($Recordset5);
//-------------------------------------------------------
$sqlb = "SELECT * FROM settings where id ='3'";
$Recordset3 = mysqli_query($conn, $sqlb) or die(mysqli_error($conn));
$row_Recordset3 = mysqli_fetch_assoc($Recordset3);
$totalRows_Recordset3 = mysqli_num_rows($Recordset3);
//-------------------------------------------------------
//load all of the DB Queries
$sql = "SELECT * FROM reel_lengths LEFT JOIN sport ON reel_lengths.reel_length_id = sport.sportid";
$Recordset4 = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$row_Recordset4 = mysqli_fetch_assoc($Recordset4);
$totalRows_Recordset4 = mysqli_num_rows($Recordset4);
//-------------------------------------------------------
$sqlb = "SELECT * FROM settings where id ='4'";
$Recordset10 = mysqli_query($conn, $sqlb) or die(mysqli_error($conn));
$row_Recordset10 = mysqli_fetch_assoc($Recordset10);
$totalRows_Recordset10 = mysqli_num_rows($Recordset10);
//-------------------------------------------------------
$sqlb = "SELECT * FROM settings where id ='5'";
$Recordset11 = mysqli_query($conn, $sqlb) or die(mysqli_error($conn));
$row_Recordset11 = mysqli_fetch_assoc($Recordset11);
$totalRows_Recordset11 = mysqli_num_rows($Recordset11);
//-------------------------------------------------------
$sqlb = "SELECT * FROM settings where id ='6'";
$Recordset12 = mysqli_query($conn, $sqlb) or die(mysqli_error($conn));
$row_Recordset12 = mysqli_fetch_assoc($Recordset12);
$totalRows_Recordset12 = mysqli_num_rows($Recordset12);
//-------------------------------------------------------
$sqlb = "SELECT * FROM settings where id ='7'";
$Recordset13 = mysqli_query($conn, $sqlb) or die(mysqli_error($conn));
$row_Recordset13 = mysqli_fetch_assoc($Recordset13);
$totalRows_Recordset13 = mysqli_num_rows($Recordset13);
//-------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <!-- bootstrap -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <!-- datepicker styles -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css">
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
  <title>SDBA</title>

</head>

<body data-spy="scroll" data-target="#main-nav">
  <?php //main nav menu

  echo $main_menus;
  ?>

  <!-- HOME SECTION -->
  <div>
    <div class="home-section diva">
      <div class="subheader"></div>
      <!--Lets build the table-->
      <p class="fxdtext"><strong>SETTINGS &</strong> Accounts</p>


      <div class="container mt-3 px-3 firstparavp">
        <div class="card cardvp">
          <div class="card-body p-3 px-3">
            <h5 class="text-dark">Currency: <?php echo $currency; ?>
              <i class="text-dark fa-solid fa-pen-to-square fa-lg" data-toggle="modal" data-target="#currencyModal"></i>
            </h5>
          </div>
        </div>
      </div>

      <div class="container mt-2 px-3">
        <div class="card cardvp">
          <div class="card-body p-3 px-3">
            <h5 class="text-dark">Units: <?php echo $row_Recordset3['value']; ?>
              <i class="text-dark fa-solid fa-pen-to-square fa-lg" data-toggle="modal" data-target="#unitsModal"></i>
            </h5>
          </div>
        </div>
      </div>

      <div class="container mt-2 px-3 ">
        <div class="card cardvp">
          <div class="card-body p-3 px-3">
            <h5 class="text-dark">Grip: <?php echo $row_Recordset2['type']; ?>
              <i class="text-dark fa-solid fa-pen-to-square fa-lg" data-toggle="modal" data-target="#gripModal"></i>
            </h5>
          </div>
        </div>
      </div>

      <div class="container mt-2 px-3 ">
        <div class="card cardvp">
          <div class="card-body p-3 px-3">
            <h5 class="text-dark">In market string:
              <a class="text-dark fa-solid fa-pen-to-square fa-lg" href="./string-im.php"></a>
            </h5>
          </div>
        </div>
      </div>

      <div class="container mt-2 px-3 ">
        <div class="card cardvp">
          <div class="card-body p-3 px-3">
            <h5 class="text-dark">Reel Lengths:
              <a class="text-dark fa-solid fa-pen-to-square fa-lg" href="./reel-lengths.php"></a>
            </h5>
          </div>
        </div>
      </div>

      <div class="container mt-2 px-3 ">
        <div class="card cardvp">
          <div class="card-body p-3 px-3">
            <h5 class="text-dark">User accounts:
              <a class="text-dark fa-solid fa-pen-to-square fa-lg" href="./site-users.php"></a>
            </h5>
          </div>
        </div>
      </div>

      <div class="container mt-2 px-3 ">
        <div class="card cardvp">
          <div class="card-body p-3 px-3">
            <h5 class="text-dark">Sports:
              <a class="text-dark fa-solid fa-pen-to-square fa-lg" href="./sports.php"></a>
            </h5>
          </div>
        </div>
      </div>

      <div class="container mt-2 px-3 ">
        <div class="card cardvp">
          <div class="card-body p-3 px-3">
            <h5 class="text-dark">Payment Account details:
              <i class="text-dark fa-solid fa-pen-to-square fa-lg" data-toggle="modal" data-target="#accModal"></i>
            </h5>
          </div>
        </div>
      </div>

      <div class="container mt-2 px-3 ">
        <div class="card cardvp">
          <div class="card-body p-3 px-3">
            <h5 class="text-dark">Domain Name:
              <i class="text-dark fa-solid fa-pen-to-square fa-lg" data-toggle="modal" data-target="#domModal"></i>
            </h5>
          </div>
        </div>
      </div>

      <!-- grip  modal -->
      <div class="modal  fade" id="gripModal">
        <div class="modal-dialog">
          <div class="modal-content  border radius">
            <div class="modal-header modal_header">
              <h5 class=" modal-title">Edit Grip</h5>
              <button class="close" data-dismiss="modal">
                <span>&times;</span>
              </button>
            </div>
            <div class="modal-body modal_body">
              <form method="post" action="./db-update.php">

                <label>Description</label>
                <div>
                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <input type="text" name="gripname" value="<?php echo $row_Recordset2['type']; ?>" class="form-control txtField">
                      </div>
                    </div>
                  </div>
                </div>

                <label class="mt-2">Price</label>
                <div>
                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <input type="text" name="price" value="<?php echo $currency . $row_Recordset2['Price']; ?>" class="form-control txtField">
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <div class="modal-footer modal_footer">
              <button class="btn modal_button_cancel" data-dismiss="modal">
                <span>Cancel</span>
              </button>
              <input type="hidden" name="gripid" class="txtField" value="<?php echo $row_Recordset2['gripid']; ?>">

              <input class="btn modal_button_submit" type="submit" name="submiteditgrip" value="Submit">
              </form>
            </div>
          </div>
        </div>
      </div>


      <!-- account  modal -->
      <div class="modal  fade" id="accModal">
        <div class="modal-dialog">
          <div class="modal-content  border radius">
            <div class="modal-header modal_header">
              <h5 class=" modal-title">Edit payment account</h5>
              <button class="close" data-dismiss="modal">
                <span>&times;</span>
              </button>
            </div>
            <div class="modal-body modal_body">
              <form method="post" action="./db-update.php">

                <label>Account name</label>
                <div>
                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <input type="text" name="accname" value="<?php echo $row_Recordset10['value']; ?>" class="form-control txtField">
                      </div>
                    </div>
                  </div>
                </div>

                <label class="mt-2">Account Number</label>
                <div>
                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <input type="text" name="accnum" value="<?php echo $row_Recordset11['value']; ?>" class="form-control txtField">
                      </div>
                    </div>
                  </div>
                </div>

                <label class="mt-2">Sort Code</label>
                <div>
                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <input type="text" name="scode" value="<?php echo $row_Recordset12['value']; ?>" class="form-control txtField">
                      </div>
                    </div>
                  </div>
                </div>

            </div>
            <div class="modal-footer modal_footer">
              <button class="btn modal_button_cancel" data-dismiss="modal">
                <span>Cancel</span>
              </button>

              <input class="btn modal_button_submit" type="submit" name="submiteditacc" value="Submit">
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- domain  modal -->
      <div class="modal  fade" id="domModal">
        <div class="modal-dialog">
          <div class="modal-content  border radius">
            <div class="modal-header modal_header">
              <h5 class=" modal-title">Edit Domain</h5>
              <button class="close" data-dismiss="modal">
                <span>&times;</span>
              </button>
            </div>
            <div class="modal-body modal_body">
              <form method="post" action="./db-update.php">

                <label>Domain name for your site</label>
                <div>
                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <input type="text" name="domname" value="<?php echo $row_Recordset13['value']; ?>" class="form-control txtField">
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <div class="modal-footer modal_footer">
              <button class="btn modal_button_cancel" data-dismiss="modal">
                <span>Cancel</span>
              </button>

              <input class="btn modal_button_submit" type="submit" name="submiteditdom" value="Submit">
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- units  modal -->
      <div class="modal  fade" id="unitsModal">
        <div class="modal-dialog">
          <div class="modal-content  border radius">
            <div class="modal-header modal_header">
              <h5 class=" modal-title">Edit units<br><small>(This will only change the symbol not the values)</small></h5>
              <button class="close" data-dismiss="modal">
                <span>&times;</span>
              </button>
            </div>
            <div class="modal-body modal_body">
              <form method="post" action="./db-update.php">
                <label>Units
                </label>
                <div>
                  <div class="container">
                    <div class="row">
                      <div class="col-12">

                        <select class="form-control" name="units">

                          <?php if ($row_Recordset3['value'] == "ft") {
                            $selected = "selected='selected'";
                          } else {
                            $selected = "";
                          } ?>
                          <option value="ft" <?php echo $selected; ?>>Feet</option>
                          <?php if ($row_Recordset3['value'] == "m") {
                            $selected = "selected='selected'";
                          } else {
                            $selected = "";
                          } ?>
                          <option value="m" <?php echo $selected; ?>>Metres</option>


                        </select>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <div class="modal-footer modal_footer">
              <button class="btn modal_button_cancel" data-dismiss="modal">
                <span>Cancel</span>
              </button>
              <input class="btn modal_button_submit" type="submit" name="submiteditunits" value="Submit">
              </form>
            </div>
          </div>
        </div>
      </div>





      <!-- currency  modal -->
      <div class="modal  fade" id="currencyModal">
        <div class="modal-dialog">
          <div class="modal-content  border radius">
            <div class="modal-header modal_header">
              <h5 class=" modal-title">Edit Currency<br><small>(This will only change the symbol not the values)</small></h5>
              <button class="close" data-dismiss="modal">
                <span>&times;</span>
              </button>
            </div>
            <div class="modal-body modal_body">
              <form method="post" action="./db-update.php">
                <label>Currency
                </label>
                <div>
                  <div class="container">
                    <div class="row">
                      <div class="col-12">

                        <select class="form-control" name="currency">

                          <?php if ($row_Recordset5['value'] == 1) {
                            $selected = "selected='selected'";
                          } else {
                            $selected = "";
                          } ?>
                          <option value="1" <?php echo $selected; ?>>United States Dollars</option>
                          <?php if ($row_Recordset5['value'] == 2) {
                            $selected = "selected='selected'";
                          } else {
                            $selected = "";
                          } ?>
                          <option value="2" <?php echo $selected; ?>>Euro</option>
                          <?php if ($row_Recordset5['value'] == 3) {
                            $selected = "selected='selected'";
                          } else {
                            $selected = "";
                          } ?>
                          <option value="3" <?php echo $selected; ?>>United Kingdom Pounds</option>
                          <?php if ($row_Recordset5['value'] == 4) {
                            $selected = "selected='selected'";
                          } else {
                            $selected = "";
                          } ?>
                          <option value="4" <?php echo $selected; ?>>Australia Dollars</option>
                          <?php if ($row_Recordset5['value'] == 5) {
                            $selected = "selected='selected'";
                          } else {
                            $selected = "";
                          } ?>
                          <option value="5" <?php echo $selected; ?>>Canada Dollars</option>
                          <?php if ($row_Recordset5['value'] == 6) {
                            $selected = "selected='selected'";
                          } else {
                            $selected = "";
                          } ?>
                          <option value="6" <?php echo $selected; ?>>China Yuan Renmimbi</option>
                          <?php if ($row_Recordset5['value'] == 7) {
                            $selected = "selected='selected'";
                          } else {
                            $selected = "";
                          } ?>
                          <option value="7" <?php echo $selected; ?>>India Rupees</option>
                          <?php if ($row_Recordset5['value'] == 8) {
                            $selected = "selected='selected'";
                          } else {
                            $selected = "";
                          } ?>
                          <option value="8" <?php echo $selected; ?>>Japan Yen</option>
                          <?php if ($row_Recordset5['value'] == 9) {
                            $selected = "selected='selected'";
                          } else {
                            $selected = "";
                          } ?>
                          <option value="9" <?php echo $selected; ?>>Russia Rubles</option>

                        </select>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <div class="modal-footer modal_footer">
              <button class="btn modal_button_cancel" data-dismiss="modal">
                <span>Cancel</span>
              </button>
              <input class="btn modal_button_submit" type="submit" name="submiteditcurrency" value="Submit">
              </form>
            </div>
          </div>
        </div>
      </div>




    </div>
  </div>

  <div class="container center">
    <div class="p-3 row">

      <div class="col-2">
        <a href="#" type="button" class="dot fa-solid fa-plus fa-2x" data-toggle="modal" data-target="#AddUser"></a>
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
          <div class="container mt-2" style="margin-top: 120px;">
            <div class="row pt-3">
              <div class="col-8">
                <div>
                  <a class="btn modal_button_cancel" href="./site-users.php">Cancel</a>
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

  <!-- Add MODAL -->

  <div class="modal  fade text-dark" id="AddUser">
    <div class="modal-dialog">
      <div class="modal-content  border radius">
        <div class="modal-header modal_header">
          <h5 class=" modal-title">You are adding a new user"</h5>
          <button class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <form method="post" action="site-users-db.php">
          <div class="modal-body  modal_body">
            <div class="form-group">
              <label for="name">User Name</label>
              <input class="form-control" id="name" type="text" placeholder="Enter Username" name="username">
              <label class="pt-3" for="email">Email Address</label>
              <input class="form-control" id="email" placeholder="Enter Email" name="email">
            </div>
            <input type="hidden" name="active" value="1">
            <label for="name">Access Level</label>

            <select style='font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12pt; width:80%' class=" form-control" id="level" name="level">
              <option value="1">1 (Super User)</option>
              <option value="2">2 (Add jobs only)</option>

            </select>

          </div>
          <div class="modal-footer modal_footer">
            <button class="btn modal_button_cancel" data-dismiss="modal">
              <span>Cancel</span>
            </button>
            <input class="btn modal_button_submit" type="submit" name="submitAdd" value="Submit">
        </form>
      </div>
    </div>
  </div>


  <!-- Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

  <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
  <script type="text/javascript" src="./js/noellipses.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
  <script src="https://cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js"></script>
  <!-- Datepicker -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>



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
    jQuery(document).ready(function($) {

      $('#tblUser').DataTable({
        pagingType: "simple_numbers_no_ellipses",
        language: {
          'search': '',
          'searchPlaceholder': 'Search Users:',
          "sLengthMenu": "",
          "info": "",
          "infoEmpty": "",
        },
        pageLength: 15,
        autoWidth: false,
        order: [
          [0, 'desc']
        ]
      });
    });
  </script>

  <script>
    jQuery(document).ready(function($) {

      $('#tblUser1').DataTable({
        pagingType: "simple_numbers_no_ellipses",
        language: {
          'search': '',
          'searchPlaceholder': 'Search Users:',
          "sLengthMenu": "",
          "info": "",
          "infoEmpty": "",
        },
        pageLength: 15,
        autoWidth: false,
        order: [
          [0, 'desc']
        ]
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
</body>

</html>