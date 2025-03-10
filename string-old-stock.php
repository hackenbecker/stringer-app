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
//load all of the DB Queries
$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");
if (isset($_POST['submitclearmessage'])) {
  unset($_SESSION['message']);
}
//-------------------------------------------------------
$query_Recordset2 = "SELECT * FROM string LEFT JOIN all_string ON string.stock_id = all_string.string_id LEFT JOIN sport ON all_string.sportid = sport.sportid LEFT JOIN reel_lengths ON string.lengthid = reel_lengths.reel_length_id WHERE empty = '1' ORDER BY string.stringid ASC";
$Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));
$row_Recordset2 = mysqli_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);
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
  <!-- bootstrap -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <!-- datepicker styles -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css">
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
  <title>SDBA</title>
  <link rel="icon" type="image/png" href="./img/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="./img/favicon-16x16.png" sizes="16x16" />
</head>



<body data-spy="scroll" data-target="#main-nav">
  <?php //main nav menu
  echo $main_menus;
  ?>
  <!-- HOME SECTION -->
  <section>
    <div class="home-section diva">
      <div class="subheader"></div>
      <!--Lets build the table-->
      <p class="fxdtextb"><strong>STOCK</strong> String</p>
      <a href="./string.php" class="fxdtexta">Stock String</a>
      <table id="tblUser" class="table-striped table-text table table-sm center">
        <thead>
          <tr>
            <th class="text-center">Reel ID.</th>
            <th class="text-center">Type</th>
            <th class="text-center d-none d-md-table-cell">Completed</th>
            <th class="text-center d-none d-md-table-cell">length</th>
            <th class="text-center d-none d-md-table-cell">Price per racke</th>
            <th class="text-center d-none d-md-table-cell">Show Jobs</th>
            <th class="text-center"></th>
            <th class="text-center"></th>
            <th class="text-center"></th>
          </tr>
        </thead>
        <tbody>
          <?php
          do { ?>
            <tr>
              <td style="text-align: center" data-toggle="modal" data-target="#StringViewModal<?php echo $row_Recordset2['stringid']; ?>"><?php echo $row_Recordset2['stringid']; ?>
              </td>
              <td data-toggle="modal" data-target="#StringViewModal<?php echo $row_Recordset2['stringid']; ?>"><?php echo $row_Recordset2['brand'] . " " . $row_Recordset2['type']; ?></td>
              <td class="d-none d-md-table-cell"><?php echo $row_Recordset2['string_number']; ?></td>
              <td class="d-none d-md-table-cell"><?php echo $row_Recordset2['length'] . "m"; ?></td>
              <td class="d-none d-md-table-cell"><?php echo "$currency" . $row_Recordset2['racket_price']; ?></td>
              <td style="text-align: center"><a class="fa-solid fa-eye" href="./string-jobs-from-reel.php?stringid=<?php echo $row_Recordset2['stringid']; ?>"></i></td>
              <td style="text-align: center"><a class="fa-solid fa-pen-to-square" href="./editstring.php?stringid=<?php echo $row_Recordset2['stringid']; ?>&sportid=<?php echo $row_Recordset2['sportid']; ?>"></i></td>
              <td style="text-align: center"><i class="fa-solid fa-trash-can" data-toggle="modal" data-target="#delModal<?php echo $row_Recordset2['stringid']; ?>"></i></td>
              <td><img class="imgsporticon m-0 p-0" src="./img/<?php echo $row_Recordset2['image']; ?>" width="18" height="18" style="padding:0; margin:0"></td>
            </tr>
            <!-- View string MODAL -->
            <div class="modal  fade text-white" id="StringViewModal<?php echo $row_Recordset2['stringid']; ?>">
              <div class="modal-dialog">
                <div class="modal-content  border radius">
                  <div class="modal-header modal_header">
                    <h5 class=" modal-title text-white">Viewing &nbsp;<?php echo $row_Recordset2['brand'] . " " . $row_Recordset2['type']; ?></h5>
                    <button class="close" data-dismiss="modal">
                      <span>&times;</span>
                    </button>
                  </div>
                  <div class="modal-body  modal_body">
                    <p class="mb-0" style="font-size:12px">String:</p>
                    <span class="h6 pb-3"><?php echo $row_Recordset2['brand'] . " " . $row_Recordset2['type']; ?></span?>
                      <p class="mb-0 mt-3" style="font-size:12px" style="font-size:12px">Current string number:</p>
                      <span class="h6 pb-3"><?php echo $row_Recordset2['string_number']; ?></span>
                      <p class="mb-0 mt-3" style="font-size:12px">Reel Number:</p>
                      <span class="h6 pb-3"><?php echo $row_Recordset2['reel_no']; ?></span>
                      <p class="mb-0 mt-3" style="font-size:12px">Internal ID:</p>
                      <span class="h6 pb-3"><?php echo $row_Recordset2['stringid']; ?></span>
                      <hr>
                      <p class="mb-0" style="font-size:12px">Reel Price:</p>
                      <span class="h6 pb-3"><?php echo "$currency" . $row_Recordset2['reel_price']; ?></span>
                      <p class="mb-0 mt-3" style="font-size:12px">Price per racket:</p>
                      <span class="h6 pb-3"><?php echo "$currency" . $row_Recordset2['racket_price']; ?></span>
                      <?php if (!empty($row_Recordset2['purchase_date'])) { ?>
                        <p class="mb-0 mt-3" style="font-size:12px">Purchase Date:</p>
                        <span class="h6 pb-3"><?php echo $row_Recordset2['purchase_date']; ?>
                        <?php } ?>
                        <?php if (!empty($row_Recordset2['note'])) { ?>
                          <p class=" mb-0 mt-3" style="font-size:12px">Notes:</p>
                          <span class="h6 pb-3"><?php echo $row_Recordset2['note']; ?>
                          <?php } ?>
                          <hr>
                          <p class=" mb-0" style="font-size:12px">Owner Supplied:</p>
                          <span class="h6 pb-3 text-capitalize"><?php echo $row_Recordset2['Owner_supplied']; ?></span>
                          <p class=" mb-0 mt-3" style="font-size:12px">Empty:</p>
                          <?php if ($row_Recordset2['empty'] == 1) { ?>
                            <span class="h6 pb-3 text-capitalize">Yes</spam><?php
                                                                          } else { ?>
                              <span class="h6 pb-3 text-capitalize">No</span>
                            <?php
                                                                          } ?>
                  </div>
                  <div class="modal-footer modal_footer">
                    <button class="btn modal_button_cancel" data-dismiss="modal">
                      <span>Cancel</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <!-- delete  modal -->
            <div class="modal  fade text-dark" id="delModal<?php echo $row_Recordset2['stringid']; ?>">
              <div class="modal-dialog">
                <div class="modal-content  border radius">
                  <div class="modal-header modal_header">
                    <h5 class=" modal-title">You are about to delete stock reel &nbsp;"<?php echo $row_Recordset2['stringid']; ?>"</h5>
                    <button class="close" data-dismiss="modal">
                      <span>&times;</span>
                    </button>
                  </div>
                  <div class="modal-body  modal_body">
                    <form method="post" action="./db-update.php">
                      <div>Please confirm or cancel!
                      </div>
                      <div style="padding-bottom:5px;">
                      </div>
                      <input type="hidden" name="refdelreel" class="txtField" value="<?php echo $row_Recordset2['stringid']; ?>">
                  </div>
                  <div class="modal-footer modal_footer">
                    <button class="btn modal_button_cancel" data-dismiss="modal">
                      <span>Cancel</span>
                    </button>
                    <input class="btn modal_button_submit" type="submit" name="submit" value="Delete">
                    </form>
                  </div>
                </div>
              </div>
            </div>
          <?php
          } while ($row_Recordset2 = mysqli_fetch_assoc($Recordset2)); ?>
        </tbody>
      </table>
    </div>
    </div>
  </section>
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
                  <a class="btn modal_button_cancel" href="./string.php">Cancel</a>
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
  <!-- Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script type="text/javascript" src="./js/theme.js"></script>

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
        stateSave: true,
        pagingType: "simple_numbers_no_ellipses",
        language: {
          'search': '',
          'searchPlaceholder': 'Search:',
          "sLengthMenu": "",
          "info": "",
          "infoEmpty": "",
        },
        pageLength: 15,
        autoWidth: false,
        columnDefs: [{
            target: 0,
            visible: false,
            searchable: false
          },
          {
            target: 6,
            orderable: false,
            targets: 'no-sort'
          },
          {
            target: 7,
            orderable: false,
            targets: 'no-sort'
          },
          {
            target: 8,
            orderable: false,
            targets: 'no-sort'
          }
        ],
        order: [
          [1, 'asc']
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
  <script type="text/javascript">
    document.getElementById('themeSwitch').addEventListener('change', function(event) {
      (event.target.checked) ? document.body.setAttribute('data-theme', 'dark'): document.body.removeAttribute('data-theme');
    });
  </script>

  <script>
    var themeSwitch = document.getElementById('themeSwitch');
    if (themeSwitch) {
      initTheme(); // on page load, if user has already selected a specific theme -> apply it

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