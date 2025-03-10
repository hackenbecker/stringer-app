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
$current_month_text = date("M");
$current_month_numeric = date("m");
$current_year = date("Y");
//load all of the DB Queries
$query_Recordset1 = "SELECT 
stringjobs.job_id as job_id,
stringjobs.customerid as customerid,
stringjobs.tension as atension,
stringjobs.tensionc as atensionc,
stringjobs.pre_tension as pre_tension,
stringjobs.price as price,
stringjobs.collection_date as collection_date,
stringjobs.delivery_date as delivery_date,
stringjobs.grip_required as grip_required,
stringjobs.paid as paid,
stringjobs.delivered as delivered,
stringjobs.free_job as free_job,
stringjobs.comments as comments,
stringjobs.tension as atension,
stringjobs.tensionc as atensionc,
stringjobs.racketid as racketid,
stringjobs.stringid as stringidm,
stringjobs.stringidc as stringidc,
customer.Name as Name,
customer.Email as Email,
customer.Mobile as Mobile,
sport.sportname as sportname,
sport.image as image,
rackets.manuf as manuf,
rackets.model as model,
rackets.pattern as pattern,
rackets.sport as sportid,
all_string.brand as brandm,
all_string.type as typem,
all_string.notes as notes_stock,
all_stringc.brand as brandc,
all_stringc.type as typec,
all_stringc.notes as notesc_stock,
string.note as notes_string,
string.string_number as stringm_number,
string.stringid as stringid_m,
string.note as notes_string,
reel_lengthsm.length as lengthm,
reel_lengthsc.length as lengthc,
stringc.note as notesc_string,
stringc.string_number as stringc_number,
stringc.stringid as stringid_c
FROM stringjobs 
LEFT JOIN customer ON customerid = cust_ID
LEFT JOIN string 
ON stringjobs.stringid = string.stringid 
LEFT JOIN string 
AS stringc 
ON stringjobs.stringidc = stringc.stringid
LEFT JOIN all_string
ON string.stock_id = all_string.string_id
LEFT JOIN all_string 
AS all_stringc 
ON stringc.stock_id = all_stringc.string_id
LEFT JOIN reel_lengths
AS reel_lengthsm
ON reel_lengthsm.reel_length_id = string.lengthid
LEFT JOIN reel_lengths
AS reel_lengthsc
ON reel_lengthsc.reel_length_id = string.lengthid
LEFT JOIN rackets ON stringjobs.racketid = rackets.racketid 
LEFT JOIN sport ON rackets.sport = sport.sportid
WHERE paid = '0'
ORDER BY job_id DESC";
$Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);
//-------------------------------------------------------
$query_Recordset2 = "SELECT * FROM string LEFT JOIN all_string ON string.stock_id = all_string.string_id left join reel_lengths on reel_lengths.reel_length_id = string.lengthid WHERE empty = '0' ORDER BY string.stringid ASC;";
$Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));
$row_Recordset2 = mysqli_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);
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
  <meta name="color-scheme" content="dark light" />
  <meta name="theme-color" media="(prefers-color-scheme: dark)" />
  <meta name="theme-color" media="(prefers-color-scheme: light)" />
</head>


<body data-spy="scroll" data-target="#main-nav">
  <?php //main nav menu
  echo $main_menus;
  ?>
  <!-- HOME SECTION -->
  <div class="home-section diva">
    <div class="subheader"></div>
    <!--Lets build the table-->
    <p class="fxdtextb"><strong>UNPAID</strong> Restrings</p>
    <?php if ($totalRows_Recordset1 == 0) {
      echo "<h5 class='text-center text-dark' style='margin-top: 200px;'>No Records found</h5> ";
    } else { ?>


      <table id="tblUser" class="table-striped table-text table table-sm center">
        <thead>
          <tr>
            <th>No.</th>
            <th>Name</th>
            <th class="text-center d-none d-md-table-cell">String Type</th>
            <th>Received</th>
            <th><i class="fa-solid fa-truck"></i></th>
            <th>Price</th>
            <th><i class="fa-solid fa-hand-holding-dollar"></i></th>
            <th></th>
            <th class="text-center d-none d-md-table-cell"></th>
            <th class="text-center d-none d-md-table-cell"></th>
            <th></th>
            <th class="text-center d-none d-md-table-cell"></th>
          </tr>
        </thead>
        <tbody>
          <?php
          do { ?>
            <tr>
              <td class="tdm">
                <a class="modal-text" href="./viewjob.php?jobid=<?php echo $row_Recordset1['job_id']; ?>"><?php echo $row_Recordset1['job_id']; ?></a>

              </td>
              <td><a class="modal-text" href="./editcust.php?custid=<?php echo $row_Recordset1['customerid']; ?>"><span><?php echo substr($row_Recordset1['Name'], 0, 12); ?></span></a></td>

              <?php if (($row_Recordset1['stringid_c'] == $row_Recordset1['stringid_m']) or ($row_Recordset1['stringid_c'] == 0)) { ?>


                <?php if ($row_Recordset1['stringm_number'] == 1) { ?>
                  <td class="text-primary d-none d-md-table-cell modal-text" data-toggle="modal" data-target="#StringViewModal<?php echo $row_Recordset1['stringid_m']; ?>"><?php echo $row_Recordset1['brandm'] ?> &nbsp;<?php echo $row_Recordset1['typem']; ?></td>
                <?php } else { ?>
                  <td class="d-none d-md-table-cell modal-text" data-toggle="modal" data-target="#StringViewModal<?php echo $row_Recordset1['stringid_m']; ?>"><?php echo $row_Recordset1['brandm'] ?> &nbsp;<?php echo $row_Recordset1['typem']; ?></td>

                <?php }
              } elseif (($row_Recordset1['stringid_c'] != $row_Recordset1['stringid_m']) && ($row_Recordset1['stringid_c'] != 0)) { ?>
                <td class="d-none d-md-table-cell modal-text" data-toggle="modal" data-target="#StringViewModal<?php echo $row_Recordset1['stringid_m']; ?>">Hybrid click for info</td>
              <?php } else { ?>
                <td class="d-none d-md-table-cell modal-text">String Unknown</td>
              <?php } ?>
              <!-- View String  modal -->
              <div class="modal  fade" id="StringViewModal<?php echo $row_Recordset1['stringid_m']; ?>">
                <div class="modal-dialog">
                  <div class="modal-content  border radius">
                    <div class="modal-header modal_header">
                      <h5 class=" modal-title">Viewing Mains: &nbsp;<?php echo $row_Recordset1['brandm'] ?> &nbsp;<?php echo $row_Recordset1['typem'] . " " . $row_Recordset1['notes_string']; ?></h5>
                      <button class="close" data-dismiss="modal">
                        <span>&times;</span>
                      </button>
                    </div>
                    <div class="modal-body modal_body">
                      <p class="form-text mb-0" style="font-size:12px">Start Length:</p>
                      <?php echo $row_Recordset1['lengthm'] . $units; ?>
                      <hr>
                      <p class="form-text mb-0" style="font-size:12px" style="font-size:12px">Restrings Completed:</p>
                      <?php echo $row_Recordset1['stringm_number']; ?>
                      <?php if ($row_Recordset1['stringid_c'] != $row_Recordset1['stringid_m'] && (!is_null($row_Recordset1['stringid_c']))) { ?>
                    </div>
                    <div class="modal-header modal_header rounded-0">
                      <h5 class=" modal-title">Viewing Crosses:&nbsp;<?php echo $row_Recordset1['brandc'] ?> &nbsp;<?php echo $row_Recordset1['typec'] . " " . $row_Recordset1['notesc_string']; ?></h5>
                    </div>
                    <div class="modal-body modal_body">
                      <p class="form-text mb-0" style="font-size:12px">Start Length:</p>
                      <?php echo $row_Recordset1['lengthc'] . $units; ?>
                      <hr>
                      <p class="form-text mb-0" style="font-size:12px" style="font-size:12px">Restrings Completed:</p>
                      <?php echo $row_Recordset1['stringc_number']; ?>
                    <?php } ?>
                    <hr>
                    <p class="form-text mb-0" style="font-size:12px" style="font-size:12px">Sport:</p>
                    <?php echo $row_Recordset1['sportname']; ?>
                    </div>
                    <div class="modal-footer modal_footer">
                      <button class="btn modal_button_cancel" data-dismiss="modal">
                        <span>Close</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- end of view string modal-->


              <?php if ($row_Recordset1['delivered'] == 0) { ?>
                <td class="text-danger"><?php echo $row_Recordset1['collection_date']; ?>
                </td><?php } else { ?>
                <td><?php echo $row_Recordset1['collection_date']; ?></td>
              <?php } ?>
              <td>
                <form method="post" action="./db-update.php">
                  <input onChange="this.form.submit()" class="form-inline" type="checkbox" name="deliveredupdate" value="1" id="delivered" <?php if ($row_Recordset1['delivered'] == 1) {
                                                                                                                                              echo " checked";
                                                                                                                                            } ?>>
                  <input type="hidden" name="jobiddeliveredupdate" class="txtField" value="<?php echo $row_Recordset1['job_id']; ?>">
                </form>
              </td>


              <?php if ($row_Recordset1['paid'] == 0) { ?>
                <td class="text-danger"><?php echo $currency . $row_Recordset1['price']; ?>
                </td><?php } else { ?>
                <td><?php echo $currency . $row_Recordset1['price']; ?>
                </td>
              <?php } ?>
              <td>
                <form class="form-inline" method="post" action="./db-update.php">
                  <input onChange="this.form.submit()" type="checkbox" name="paidupdate" value="1" id="paid" <?php if ($row_Recordset1['paid'] == 1) {
                                                                                                                echo " checked";
                                                                                                              } ?>>
                  <input type="hidden" name="jobidpaidupdate" class="txtField" value="<?php echo $row_Recordset1['job_id']; ?>">

                </form>
              </td>
              <td><a class="fa-solid fa-pen-to-square fa-lg modal-text" href="./editjob.php?jobid=<?php echo $row_Recordset1['job_id']; ?>"></a></td>
              <td class="text-center d-none d-md-table-cell">
                <form method="post" action="./db-update.php">
                  <input type="hidden" name="customerid" class="txtField" value="<?php echo $row_Recordset1['customerid']; ?>">
                  <input type="hidden" name="stringid" class="txtField" value="<?php echo $row_Recordset1['stringidm']; ?>">
                  <input type="hidden" name="stringidc" class="txtField" value="<?php echo $row_Recordset1['stringidc']; ?>">
                  <input type="hidden" name="racketid" class="txtField" value="<?php echo $row_Recordset1['racketid']; ?>">
                  <input type="hidden" name="daterecd" class="txtField" value="<?php echo $row_Recordset1['collection_date']; ?>">
                  <input type="hidden" name="datereqd" class="txtField" value="<?php echo $row_Recordset1['delivery_date']; ?>">
                  <input type="hidden" name="preten" class="txtField" value="<?php echo $row_Recordset1['pre_tension']; ?>">
                  <input type="hidden" name="tensionm" class="txtField" value="<?php echo $row_Recordset1['atension']; ?>">
                  <input type="hidden" name="tensionc" class="txtField" value="<?php echo $row_Recordset1['atensionc']; ?>">
                  <input type="hidden" name="gripreqd" class="txtField" value="<?php echo $row_Recordset1['grip_required']; ?>">
                  <input type="hidden" name="freerestring" class="txtField" value="<?php echo $row_Recordset1['free_job']; ?>">
                  <input type="hidden" name="comments" class="txtField" value="<?php echo $row_Recordset1['comments']; ?>">
                  <button type="submit" style="background-color: transparent;border:0;padding:0px;" class="button-colours-rollover" name="submitadd"><i title="copy" class="fa-solid fa-copy fa-lg"></i></button>
                </form>
              </td>

              <td class="d-none d-md-table-cell"><i class="modal-text fa-solid fa-trash-can fa-lg" data-toggle="modal" data-target="#delModal<?php echo $row_Recordset1['job_id']; ?>"></i></td>
              <td><a class="fa-solid fa-tags fa-lg fa-flip-horizontal modal-text" href="./label.php?jobid=<?php echo $row_Recordset1['job_id']; ?>"></a></td>
              <td class="text-center d-none d-md-table-cell"><img class="imgsporticon m-0 p-0" src="./img/<?php echo $row_Recordset1['image']; ?>" width="18" height="18" style="padding:0; margin:0"></td>
            </tr>
            <!-- delete  modal -->
            <div class=" modal fade" id="delModal<?php echo $row_Recordset1['job_id']; ?>">
              <div class="modal-dialog">
                <div class="modal-content  border radius">
                  <div class="modal-header modal_header">
                    <h5 class=" modal-title">You are about to delete &nbsp;"<?php echo $row_Recordset1['job_id']; ?>"</h5>
                    <button class="close" data-dismiss="modal">
                      <span>&times;</span>
                    </button>
                  </div>
                  <div class="modal-body modal_body">
                    <form method="post" action="./db-update.php">
                      <div>Please confirm or cancel!
                      </div>
                      <div style="padding-bottom:5px;">
                      </div>
                      <input type="hidden" name="refdel" class="txtField" value="<?php echo $row_Recordset1['job_id']; ?>">
                      <input type="hidden" name="stringidm" class="txtField" value="<?php echo $row_Recordset1['stringidm']; ?>">
                      <?php if (isset($row_Recordset1['stringidc'])) { ?>
                        <input type="hidden" name="stringidc" class="txtField" value="<?php echo $row_Recordset1['stringidc']; ?>">
                      <?php } ?>
                  </div>
                  <div class="modal-footer modal_footer">
                    <button class="btn modal_button_cancel" data-dismiss="modal">
                      <span>Cancel</span>
                    </button>
                    <input class="btn modal_button_submit" type="submit" name="submitdelete" value="Delete">
                    </form>
                  </div>
                </div>
              </div>
            </div>
          <?php
          } while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1)); ?>
        </tbody>
      </table>
    <?php } ?>
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
        <div class="dotbt h6" title="Restrings for <?php echo $current_month_text; ?>">
          <span class=" text-center"><?php echo $totalRows_Recordset6 ?></span>
          <!--<span class="hover-text text-center"><small><?php echo $current_month_text; ?><br>Jobs</small></span>-->
        </div>
      </div>
      <div class="col-2">
        <div class="dotbt h6" title="Total restrings">
          <span class="text-center"><?php echo $totalRows_Recordset7 ?></span>
          <!--<span class="hover-text text-center"><small>Total<br>Jobs</small></span>-->
        </div>
      </div>
      <div class="col-2">
        <a href="./jobs-unpaid.php" class="dotbt h6" title="Amount Owed"><?php echo $currency . $sum_owed ?></a>
      </div>
      <div class="col-2">
        <a href="#" class="dotbtt h7" title="Total Income"><small><?php echo $currency . $sum ?></small></a>
      </div>
    </div>
  </div>
  <!-- Information modal -->
  <div class="modal  fade " id="warningModal">
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
                  <a class="btn modal_button_cancel" href="./string-jobs.php">Cancel</a>
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
  <?php
  $_SESSION['message'] = '';
  if (isset($row_Recordset2['string_number'])) {
    do {
      if (($row_Recordset2['string_number'] > $row_Recordset2['warning_level']) and ($row_Recordset2['empty'] == 0)) {
        $_SESSION['message'] .= "String reel (" . $row_Recordset2['stringid'] . ") " . $row_Recordset2['brand'] . " " . $row_Recordset2['type'] . " is low <br>";
      }
    } while ($row_Recordset2 = mysqli_fetch_assoc($Recordset2));
  }
  ?>
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
        stateSave: true,
        pagingType: "simple_numbers_no_ellipses",
        language: {
          'search': '',
          'searchPlaceholder': 'Search:',
          "sLengthMenu": "",
          "info": "",
          "infoEmpty": "",
          "max-width": "none !important",
          "border-collapse": "collapse !important",
          "border - spacing": "0"
        },

        pageLength: 15,
        autoWidth: false,
        order: [
          [0, 'desc']
        ],
        columnDefs: [{
            targets: [0, 1, 2, 3, 4, 5, 6, 7, 8],
            className: "dt-head-center"
          },
          {
            target: 3,
            sWidth: '20px'
          },
          {
            target: 4,
            orderable: false,
            targets: 'no-sort',
            className: 'dt-left',
            sWidth: '20px'
          },
          {
            target: 5,
            sWidth: '0px',
            orderable: false,
            targets: 'no-sort'
          },
          {
            target: 6,
            orderable: false,
            targets: 'no-sort',
            className: 'dt-left',
            sWidth: '0px',
            padding: '0'
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
          },
          {
            target: 9,
            orderable: false,
            targets: 'no-sort'
          },
          {
            target: 10,
            orderable: false,
            targets: 'no-sort'
          },
          {
            target: 11,
            orderable: false,
            targets: 'no-sort'
          },
        ],
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