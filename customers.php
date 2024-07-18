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

if ($_SESSION['level'] < 1) {
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

$query_Recordset2 = "SELECT * FROM customer LEFT JOIN rackets ON customer.racketid = rackets.racketid LEFT JOIN all_string ON customer.pref_string = all_string.string_id ORDER BY name ASC";
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
      <p class="fxdtext"><strong>All</strong> Customers</p>
      <?php if ($totalRows_Recordset2 == 0) {
        echo "<h5 class='text-center text-dark' style='margin-top: 200px;'>No Records found</h5> ";
      } else { ?>
        <table id="tblUser" class="table-text table table-sm center">
          <thead>
            <tr>
              <th style="text-align: center">Name</th>
              <th class="text-center d-none d-md-table-cell">Mobile</th>
              <th class="text-center d-none d-md-table-cell">Email</th>
              <th></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php
            do { ?>
              <tr>
                <td><?php echo $row_Recordset2['Name']; ?></td>
                <td class="d-none d-md-table-cell"><?php echo $row_Recordset2['Mobile']; ?></td>
                <td class="d-none d-md-table-cell"><?php echo $row_Recordset2['Email']; ?></td>

                <td style="text-align: center"><a class="fa-solid fa-pen-to-square" href="./editcust.php?custid=<?php echo $row_Recordset2['cust_ID']; ?>"></a></td>
                <td style="text-align: center"><i class="fa-solid fa-trash-can" data-toggle="modal" data-target="#delModal<?php echo $row_Recordset2['cust_ID']; ?>"></i></td>
              </tr>

              <!-- View customer MODAL -->
              <div class="modal fade text-white" id="CustViewModal<?php echo $row_Recordset2['cust_ID']; ?>">
                <div class="modal-dialog">
                  <div class="modal-content  border radius">
                    <div class="modal-header modal_header">
                      <h5 class=" modal-title text-white">Viewing &nbsp;<?php echo $row_Recordset2['Name']; ?></h5>
                      <button class="close" data-dismiss="modal">
                        <span>&times;</span>
                      </button>
                    </div>
                    <div class="modal-body modal_body">
                      <?php if (!empty($row_Recordset2['Name'])) { ?>
                        <p style="font-size:12px">Name:</p>
                        <a href="mailto:<?php echo $row_Recordset2['Email']; ?>"><span class="h6"><?php echo $row_Recordset2['Name']; ?></a><?php } ?>
                      <?php if (!empty($row_Recordset2['Mobile'])) { ?>
                        <p class=" mb-0 mt-2" style="font-size:12px" style="font-size:12px">Mobile:</p>
                        <span class="h6"><?php echo $row_Recordset2['Mobile']; ?></span><?php } ?>
                      <?php if (!empty($row_Recordset2['Email'])) { ?>
                        <p class="mb-0" style="font-size:12px">Email:</p>
                        <a href="mailto:<?php echo $row_Recordset2['Email']; ?>"><span class="h6"><?php echo $row_Recordset2['Email']; ?></span></a>
                      <?php } ?>
                      <p class=" mb-0 mt-2" style="font-size:12px" style="font-size:12px">Discount:</p>
                      <span class="h6"><?php echo $row_Recordset2['discount']; ?>%</span>
                      <hr>
                      <?php if (!empty($row_Recordset2['manuf'])) { ?>
                        <p class=" mb-0" style="font-size:12px">Preferred Racket:</p>
                        <span class="h6"><?php echo $row_Recordset2['manuf'] . " " . $row_Recordset2['model']; ?></span><?php } ?>


                      <?php if (!empty($row_Recordset2['type'])) { ?>
                        <p class=" mb-0 mt-2" style="font-size:12px">Preferred String:</p>
                        <span class="h6"><?php echo $row_Recordset2['brand'] . " " . $row_Recordset2['type'] . " " . $row_Recordset2['notes']; ?></span>
                      <?php } ?>

                      <?php if (!empty($row_Recordset2['tension'])) { ?>
                        <p class=" mb-0 mt-2" style="font-size:12px">Preferred Tension:</p>
                        <span class="h6"><?php echo $row_Recordset2['tension'] . " lbs";
                                        } ?>

                        <?php if (!empty($row_Recordset2['pre_tension'])) { ?>
                          <p class=" mb-0 mt-2" style="font-size:12px">Pre-Tension:</p>
                          <span class="h6"><?php echo $row_Recordset2['pre_tension'] . "%";
                                          } ?>
                          <hr>
                          <?php if (!empty($row_Recordset2['Notes'])) { ?>
                            <p class=" mb-0 mt-2" style="font-size:12px">Notes:</p>
                            <span class="h6"><?php echo $row_Recordset2['Notes'];
                                            } ?>



                    </div>

                    <div class="modal-footer modal_footer">
                      <button class="btn modal_button_submit">
                        <span><a href="./editcust.php?custid=<?php echo $row_Recordset2['cust_ID']; ?>">Edit</a></span>
                      </button>
                      <button class="btn modal_button_cancel" data-dismiss="modal">
                        <span>Cancel</span>
                      </button>
                    </div>

                  </div>
                </div>
              </div>
              <!-- End of view customer modal-->
              <!-- delete  modal -->

              <?php
              if ($_SESSION['level'] == 1) { ?>
                <div class="modal  fade text-dark" id="delModal<?php echo $row_Recordset2['cust_ID']; ?>">
                  <div class="modal-dialog">
                    <div class="modal-content  border radius">
                      <div class="modal-header modal_header">
                        <h5 class=" modal-title">You are about to delete &nbsp;"<?php echo $row_Recordset2['Name']; ?>"</h5>
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
                          <input type="hidden" name="refdelcust" class="txtField" value="<?php echo $row_Recordset2['cust_ID']; ?>">
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



              <?php } else { ?>
                <div class="modal  fade text-dark" id="delModal<?php echo $row_Recordset2['cust_ID']; ?>">
                  <div class="modal-dialog">
                    <div class="modal-content  border radius">
                      <div class="modal-header modal_header">
                        <h5 class=" modal-title">You do not have permission"</h5>
                        <button class="close" data-dismiss="modal">
                          <span>&times;</span>
                        </button>
                      </div>
                      <div class="modal-body  modal_body">

                      </div>
                    </div>
                  </div>
                </div>
              <?php } ?>

            <?php
            } while ($row_Recordset2 = mysqli_fetch_assoc($Recordset2)); ?>
          </tbody>
        </table>
      <?php } ?>

    </div>
    </div>
  </section>

  <div class="container center">
    <div class="p-3 row">

      <div class="col-2">
        <a href="./addcustomer.php" type="button" class="dot fa-solid fa-plus fa-2x"></a>
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
          'searchPlaceholder': 'Search:',
          "sLengthMenu": "",
          "info": "",
          "infoEmpty": "",
        },
        pageLength: 15,
        autoWidth: false,
        columnDefs: [{
            targets: [0, 1, 2, 3, 4],
            className: "dt-head-center"
          },


          {
            target: 3,
            orderable: false,
            targets: 'no-sort'
          },

          {
            target: 4,
            orderable: false,
            targets: 'no-sort'
          },

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
</body>

</html>