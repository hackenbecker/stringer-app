<?php require_once('./Connections/wcba.php');
require_once('./menu.php');
//-------------------------------------------------------------------
// Initialize the session
if (!isset($_SESSION)) {
  session_start();
}
//load all of the DB Queries
$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");
if (isset($_POST['submitclearmessage'])) {
  unset($_SESSION['message']);
}
if (isset($_GET['jobid'])) {
  $jobid = $_GET['jobid'];
}

if (isset($_POST['jobid'])) {
  $jobid = $_POST['jobid'];
}
//-------------------------------------------------------
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
stringjobs.racketid as racketid,
stringjobs.imageid,
stringjobs.stringid as stringid,
stringjobs.stringidc as stringidc,
customer.Name as Name,
customer.Email as Email,
customer.Mobile as Mobile,
sport.sportname as sportname,
rackets.manuf as manuf,
rackets.model as model,
rackets.pattern as pattern,
all_string.notes as notes_stock,
all_stringc.notes as notesc_stock,
string.note as notes_string,
string.string_number as stringm_number,
string.note as notes_string,
stringc.note as notesc_string,
stringc.string_number as stringc_number,
all_string.brand as brandm,
all_string.type as typem,
all_stringc.brand as brandc,
all_stringc.type as typec,
string.stringid as stringid_m,
stringc.stringid as stringid_c,
reel_lengthsm.length as lengthm,
reel_lengthsc.length as lengthc
FROM stringjobs
LEFT JOIN customer ON customerid = cust_ID
LEFT JOIN string ON stringjobs.stringid = string.stringid 
LEFT JOIN string AS stringc ON stringjobs.stringidc = stringc.stringid
LEFT JOIN all_string ON string.stock_id = all_string.string_id
LEFT JOIN all_string AS all_stringc ON stringc.stock_id = all_stringc.string_id
LEFT JOIN reel_lengths AS reel_lengthsm ON reel_lengthsm.reel_length_id = string.lengthid
LEFT JOIN reel_lengths AS reel_lengthsc ON reel_lengthsc.reel_length_id = string.lengthid
LEFT JOIN rackets ON stringjobs.racketid = rackets.racketid 
LEFT JOIN sport ON all_string.sportid = sport.sportid 
WHERE job_id = '" . $jobid . "'";
$Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);
//-------------------------------------------------------
$query_Recordset2 = "SELECT 
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
stringjobs.stringid as stringid,
customer.Name as Name,
customer.Email as Email,
customer.Mobile as Mobile,
sport.sportname as sportname,
rackets.manuf as manuf,
rackets.model as model,
rackets.pattern as pattern,
all_string.brand as brandm,
all_string.type as typem,
all_string.notes as notes_stock,
all_stringc.brand as brandc,
all_stringc.type as typec,
all_stringc.notes as notesc_stock,
string.note as notes_string,
string.string_number as stringm_number,
string.stringid as stringid_m,
stringc.note as notesc_string,
stringc.string_number as stringc_number,
stringc.stringid as stringid_c,
reel_lengthsm.length as lengthm,
reel_lengthsc.length as lengthc
FROM stringjobs 
LEFT JOIN customer ON customerid = cust_ID
LEFT JOIN string ON stringjobs.stringid = string.stringid 
LEFT JOIN string AS stringc ON stringjobs.stringidc = stringc.stringid
LEFT JOIN all_string ON string.stock_id = all_string.string_id
LEFT JOIN all_string AS all_stringc ON stringc.stock_id = all_stringc.string_id
LEFT JOIN reel_lengths AS reel_lengthsm ON reel_lengthsm.reel_length_id = string.lengthid
LEFT JOIN reel_lengths AS reel_lengthsc ON reel_lengthsc.reel_length_id = string.lengthid
LEFT JOIN rackets ON stringjobs.racketid = rackets.racketid 
LEFT JOIN sport ON all_string.sportid = sport.sportid 
WHERE customerid = '" . $row_Recordset1['customerid'] . "' ORDER BY job_id DESC";
$Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));
$row_Recordset2 = mysqli_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);
//-------------------------------------------------------
//-------------------------------------------------------
$query_Recordset3 = "SELECT SUM(price) as SUM from stringjobs WHERE customerid = '" . $row_Recordset1['customerid'] . "'";
$Recordset3 = mysqli_query($conn, $query_Recordset3) or die(mysqli_error($conn));
$row_Recordset3 = mysqli_fetch_assoc($Recordset3);
$sum = $row_Recordset3['SUM'];
//-------------------------------------------------------
$query_Recordset4 = "SELECT SUM(price) as SUM from stringjobs WHERE customerid = '" . $row_Recordset1['customerid'] . "' && paid != '1';";
$Recordset4 = mysqli_query($conn, $query_Recordset4) or die(mysqli_error($conn));
$row_Recordset4 = mysqli_fetch_assoc($Recordset4);
if (!is_null($row_Recordset4['SUM'])) {
  $sum_owed = $row_Recordset4['SUM'];
  $_SESSION['sum_owed'] = $sum_owed;
} else {
  $sum_owed = "0";
}
//-------------------------------------------------------
$query_Recordset6 = "SELECT * FROM stringjobs WHERE collection_date LIKE '___" . $current_month_numeric . "/" . $current_year .  "%' && customerid = '" . $row_Recordset1['customerid'] . "' ORDER BY job_id ASC;";
$Recordset6 = mysqli_query($conn, $query_Recordset6) or die(mysqli_error($conn));
$row_Recordset6 = mysqli_fetch_assoc($Recordset6);
$totalRows_Recordset6 = mysqli_num_rows($Recordset6);
//-------------------------------------------------------
$query_Recordset7 = "SELECT * FROM stringjobs ORDER BY job_id ASC;";
$Recordset7 = mysqli_query($conn, $query_Recordset7) or die(mysqli_error($conn));
$row_Recordset7 = mysqli_fetch_assoc($Recordset7);
$totalRows_Recordset7 = mysqli_num_rows($Recordset7);
//-------------------------------------------------------
$query_Recordset15 = "SELECT * FROM settings WHERE id = '21';";
$Recordset15 = mysqli_query($conn, $query_Recordset15) or die(mysqli_error($conn));
$row_Recordset15 = mysqli_fetch_assoc($Recordset15);
$totalRows_Recordset15 = mysqli_num_rows($Recordset15);
$weight = $row_Recordset15['value'];
if ($weight == "kg") {
  $maxtension = 35;
} else {
  $maxtension = 70;
}
//--------------------------------------------------------
$imageid = $row_Recordset1['imageid'];
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
  <link rel="stylesheet" href="./admin/css/bootstrap-datetimepicker.min.css" type="text/css" media="all" />
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



<body data-spy="scroll" data-target="#main-nav">
  <?php //main nav menu
  echo $main_menus;
  ?>
  <!-- HOME SECTION -->
  <div class="home-section diva">
    <div class="subheader"></div>
    <!--Lets build the table-->
    <p class="fxdtextb"><strong>View</strong> Restring: <?php echo $row_Recordset1['job_id']; ?> </p>


    <div class="container my-3 pb-3 px-3 firstparavp">
      <div class="card cardvp">
        <div class="card-body">
          <?php if (!empty($row_Recordset1['Name'])) { ?>
            <p class="form-text mb-0" style="font-size:12px" style="font-size:12px">Name:</p>
            <span class="h6 form-text-alt"><?php echo $row_Recordset1['Name']; ?></span><?php
                                                                                      }
                                                                                        ?>
          <?php if (!empty($row_Recordset1['Mobile'])) { ?>
            <p class="form-text mb-0" style="font-size:12px" style="font-size:12px">Mobile:</p>
            <span class="h6 form-text-alt"><?php echo $row_Recordset1['Mobile']; ?></span><?php
                                                                                        }
                                                                                          ?>
          <?php if (!empty($row_Recordset1['Email'])) { ?>
            <p class="form-text mb-0" style="font-size:12px">Email:</p>
            <a href="mailto:<?php echo $row_Recordset1['Email']; ?>"><span class="h6 form-text-alt"><?php echo $row_Recordset1['Email']; ?></span></a>
          <?php
          }
          ?>

          <?php if (isset($_SESSION['loggedin'])) { ?>
            <hr>
            <form method="post" action="./db-update.php">

              <input type="hidden" name="customerid" class="txtField" value="<?php echo $row_Recordset1['customerid']; ?>">

              <input type="hidden" name="weight" class="txtField" value="<?php echo $weight; ?>">
              <input type="hidden" name="copytag" class="txtField" value="1">
              <input type="hidden" name="stringid" class="txtField" value="<?php echo $row_Recordset1['stringid']; ?>">
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
              <button type="submit" class="btn btn-sm button-colours" name="submitadd"><i class="fa-solid fa-copy"></i> Copy</button>
            </form>
            <span class="form-text mb-0 mt-3 " style="font-size:12px">Print Label: </span><a class="fa-solid fa-tags fa-lg fa-flip-horizontal form-text-alt" title="print label" href="./label.php?jobid=<?php echo $row_Recordset1['job_id']; ?>"></a>
            <span class="form-text mb-0" style="font-size:12px">Print Invoice: </span><a class="fa-solid fa-tags fa-lg fa-flip-horizontal form-text-alt" title="print Invoice" href="./ex.php?customerid=<?php echo $row_Recordset1['customerid']; ?>&jobid=<?php echo $row_Recordset1['job_id']; ?>"></a>





          <?php } ?>


          <hr>
          <?php if (!empty($row_Recordset1['manuf'])) { ?>
            <p class="form-text mb-0" style="font-size:12px">Racket:</p>
            <span class="h6 form-text-alt"><?php echo $row_Recordset1['manuf'] . " " . $row_Recordset1['model']; ?></span><?php } ?>

          <p class="form-text mb-0" style="font-size:12px">String Mains:</p>
          <span class="h6 form-text-alt"><?php echo $row_Recordset1['brandm'] . " " . $row_Recordset1['typem'] . " " . $row_Recordset1['notes_string']; ?></span>



          <p class="form-text mb-0" style="font-size:12px">String Crosses:</p>
          <?php if (($row_Recordset1['stringidc'] == 0) or ($row_Recordset1['stringidc'] == $row_Recordset1['stringid'])) { ?>

            <span class="h6 form-text-alt">Same as mains.</span>
          <?php } else { ?>
            <span class="h6 form-text-alt"><?php echo $row_Recordset1['brandc'] . " " . $row_Recordset1['typec'] . " " . $row_Recordset1['notesc_string']; ?></span>
          <?php } ?>

          <?php if (!empty($row_Recordset1['atension'])) { ?>
            <p class="form-text mb-0" style="font-size:12px">Tension Mains:</p>
            <span class="h6 form-text-alt">
            <?php if ($weight == "kg") {
              echo (round($row_Recordset1['atension'] * 0.45359237, 1)) . $weight;
            } else {
              echo $row_Recordset1['atension'] . $weight;
            }
          }
            ?>
            <?php if (($row_Recordset1['atension'] != $row_Recordset1['atensionc']) && ($row_Recordset1['atensionc'] != 0)) { ?>
              <p class="form-text mb-0" style="font-size:12px">Tension Crosses:</p>
              <span class="h6 form-text-alt">
                <?php if ($weight == "kg") {
                  echo ($row_Recordset1['atensionc'] * 0.45359237) . $weight;
                } else {
                  echo $row_Recordset1['atensionc'] . $weight;
                } ?>
                <p class="form-text mb-0" style="font-size:12px">Tension Crosses:</p>
                <span class="h6 form-text-alt">Same as mains</span>
              <?php } ?>
              <?php if (!empty($row_Recordset1['pre_tension'])) { ?>
                <p class="form-text mb-0" style="font-size:12px">Pre-Tension:</p>
                <span class="h6 form-text-alt"><?php echo $row_Recordset1['pre_tension'] . "%";
                                              }
                                                ?>
                <hr>
                <div class="row">
                  <div class="col-3">
                    <?php if (!empty($row_Recordset1['price'])) { ?>
                      <p class="form-text mb-0" style="font-size:12px">Price:</p>
                      <span class="h6 form-text-alt"><?php echo "$currency" . $row_Recordset1['price'];
                                                    }
                                                      ?>
                  </div>
                  <div class="col-3">
                    <?php if ($row_Recordset1['paid'] == 0) { ?>
                      <span class="h6 form-text-alt">
                        <p class="form-text mb-0" style="font-size:12px">paid:</p>
                      <?php echo "Not Paid";
                    } else { ?>
                        <span class="h6 form-text-alt">
                          <p class="form-text mb-0" style="font-size:12px">paid:</p>
                        <?php echo "Paid";
                      }
                        ?>
                  </div>
                  <div class="col-3">
                    <?php if ($row_Recordset1['grip_required'] == 1) { ?>
                      <span class="h6 form-text-alt">
                        <p class="form-text mb-0" style="font-size:12px">Grip Required:</p>
                      <?php echo "Yes";
                    } else { ?>
                        <span class="h6 form-text-alt">
                          <p class="form-text mb-0" style="font-size:12px">Grip Required:</p>
                        <?php echo "No";
                      }
                        ?>
                  </div>
                  <div class="col-3">
                    <?php if ($row_Recordset1['delivered'] == 1) { ?>
                      <span class="h6 form-text-alt">
                        <p class="form-text mb-0" style="font-size:12px">Delivered:</p>
                      <?php echo "Yes";
                    } else { ?>
                        <span class="h6 form-text-alt">
                          <p class="form-text mb-0" style="font-size:12px">Delivered:</p>
                        <?php echo "No";
                      }
                        ?>
                  </div>
                </div>
                <hr>
                <?php if (!empty($row_Recordset1['comments'])) { ?>
                  <span class="h6 form-text-alt">
                    <p class="form-text mb-0" style="font-size:12px">Comments:</p>
                  </span>
                <?php echo $row_Recordset1['comments'];
                } ?>
                <?php
                //-------------------------------------------------------
                $query_Recordset5 = "SELECT * from images WHERE id = '" . $imageid . "'";
                $Recordset5 = mysqli_query($conn, $query_Recordset5) or die(mysqli_error($conn));
                $row_Recordset5 = mysqli_fetch_assoc($Recordset5);
                //-------------------------------------------------------
                if (isset($row_Recordset5['image'])) { ?>
                  <?php
                  $imageData = $row_Recordset5['image'];
                  echo '<img class="img-responsive-width" {
            data-toggle="modal" data-target="#exampleModal" src="data:image/jpeg;base64,' . base64_encode($imageData) . '" alt="Uploaded Image" style="max-width: 150px;">'; ?>
                  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <!-- Add image inside the body of modal -->
                        <div class="modal-body">
                          <?php echo '<img data-toggle="modal" data-target="#exampleModal" src="data:image/jpeg;base64,' . base64_encode($imageData) . '" alt="Uploaded Image" style="max-width: 100%;">';  ?>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Close
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php   }
                ?>
        </div>
      </div>
    </div>
    <div class="container my-3 pb-3 px-3">
      <div class="card cardvp">
        <div class="card-body">
          <h5 class="text-dark">All jobs to date <small>(Click No. for more info)</small></h5>
          <table id="tblUser" class="table-striped p-3 table-text table table-sm center">
            <thead>
              <tr>
                <th class="text-center">No.</th>
                <th class="text-center">Racket</th>
                <th class="text-center d-none d-md-table-cell">String Type</th>
                <th class="text-center">Received</th>
                <th class="text-center">Price</th>
              </tr>
            </thead>
            <tbody>
              <?php
              do { ?>
                <tr>
                  <td>
                    <a href="./viewjob.php?jobid=<?php echo $row_Recordset2['job_id']; ?>"><?php echo $row_Recordset2['job_id']; ?></a>
                  </td>
                  <td><?php echo $row_Recordset2['manuf'] . " " . $row_Recordset2['model']; ?></td>
















                  <?php if ($row_Recordset2['stringid_c'] == $row_Recordset2['stringid_m']) { ?>



                    <td class="d-none d-md-table-cell modal-text" data-toggle="modal" data-target="#StringmViewModal<?php echo $row_Recordset2['stringid_m']; ?>"><?php echo $row_Recordset2['brandm'] ?> &nbsp;<?php echo $row_Recordset2['typem']; ?></td>

                    <!-- View String  modal -->
                    <div class="modal  fade" id="StringmViewModal<?php echo $row_Recordset2['stringid_m']; ?>">
                      <div class="modal-dialog">
                        <div class="modal-content  border radius">
                          <div class="modal-header modal_header">
                            <h5 class=" modal-title">Viewing Mains: &nbsp;<?php echo $row_Recordset2['brandm'] ?> &nbsp;<?php echo $row_Recordset2['typem'] . " " . $row_Recordset2['notes_string']; ?></h5>
                            <button class="close" data-dismiss="modal">
                              <span>&times;</span>
                            </button>
                          </div>
                          <div class="modal-body modal_body">
                            <p class="form-text mb-0" style="font-size:12px">Start Length:</p>
                            <?php echo $row_Recordset2['lengthm'] . $units; ?>
                            <hr>
                            <p class="form-text mb-0" style="font-size:12px" style="font-size:12px">Restrings Completed:</p>
                            <?php echo $row_Recordset2['stringm_number']; ?>


                            <hr>
                            <p class="form-text mb-0" style="font-size:12px" style="font-size:12px">Sport:</p>
                            <?php echo $row_Recordset2['sportname']; ?>
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

                  <?php
                  } elseif ($row_Recordset2['stringid_c'] != $row_Recordset2['stringid_m']) { ?>
                    <td class="d-none d-md-table-cell modal-text" data-toggle="modal" data-target="#StringcViewModal<?php echo $row_Recordset2['stringid_m']; ?>">Hybrid click for info<?php echo $row_Recordset2['stringid_m'] . " " . $row_Recordset2['stringid_c']; ?>



                      <!-- View String  modal -->
                      <div class="modal  fade" id="StringcViewModal<?php echo $row_Recordset2['stringid_m']; ?>">
                        <div class="modal-dialog">
                          <div class="modal-content  border radius">
                            <div class="modal-header modal_header">
                              <h5 class=" modal-title">Viewing Mains: &nbsp;<?php echo $row_Recordset2['brandm'] ?> &nbsp;<?php echo $row_Recordset2['typem'] . " " . $row_Recordset2['notes_string']; ?></h5>
                              <button class="close" data-dismiss="modal">
                                <span>&times;</span>
                              </button>
                            </div>
                            <div class="modal-body modal_body">
                              <p class="form-text mb-0" style="font-size:12px">Start Length:</p>
                              <?php echo $row_Recordset2['lengthm'] . $units; ?>
                              <hr>
                              <p class="form-text mb-0" style="font-size:12px" style="font-size:12px">Restrings Completed:</p>
                              <?php echo $row_Recordset2['stringm_number']; ?>

                            </div>
                            <div class="modal-header modal_header rounded-0">

                              <h5 class=" modal-title">Viewing Crosses:&nbsp;<?php echo $row_Recordset2['brandc'] ?> &nbsp;<?php echo $row_Recordset2['typec'] . " " . $row_Recordset2['notesc_string']; ?></h5>
                            </div>
                            <div class="modal-body modal_body">
                              <p class="form-text mb-0" style="font-size:12px">Start Length:</p>
                              <?php echo $row_Recordset2['lengthc'] . $units; ?>
                              <hr>
                              <p class="form-text mb-0" style="font-size:12px" style="font-size:12px">Restrings Completed:</p>
                              <?php echo $row_Recordset2['stringc_number']; ?>
                              <hr>
                              <p class="form-text mb-0" style="font-size:12px" style="font-size:12px">Sport:</p>
                              <?php echo $row_Recordset2['sportname']; ?>
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




                    </td>
                  <?php } else { ?>
                    <td class="d-none d-md-table-cell modal-text">String Unknown</td>
                  <?php } ?>




























                  </td>
                  <?php if ($row_Recordset2['delivered'] == 0) { ?>
                    <td class="text-danger"><?php echo $row_Recordset2['collection_date']; ?></td><?php } else { ?>
                    <td><?php echo $row_Recordset2['collection_date']; ?></td>
                  <?php } ?>
                  <?php if ($row_Recordset2['paid'] == 0) { ?>
                    <td class="text-danger"><?php echo "$currency" . $row_Recordset2['price']; ?></td><?php } else { ?>
                    <td><?php echo "$currency" . $row_Recordset2['price']; ?></td>
                  <?php } ?>
                </tr>
              <?php
              } while ($row_Recordset2 = mysqli_fetch_assoc($Recordset2)); ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  </section>
  <div class="container center">
    <div class="p-3 row">

      <?php if (isset($_SESSION['loggedin'])) { ?>

        <div class="col-2">
          <a href="./addjob.php" type="button" class="dot fa-solid fa-plus fa-2x"></a>
        </div>
      <?php } else { ?>

        <div class="col-2">
          <span type="button" class="dot fa-solid fa-plus fa-2x"></span>
        </div>
      <?php } ?>

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
          <span class="text-center"><?php echo $totalRows_Recordset2 ?></span>
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
                  <a class="btn modal_button_cancel" href="./viewjob.php?jobid=<?php echo $jobid; ?>">Cancel</a>
                </div>
              </div>
              <div class="col-4">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                  <input type="hidden" name="jobid" value="<?php echo $jobid; ?>">
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
    jQuery(document).ready(function($) {
      $('#tblUser').DataTable({
        stateSave: true,
        "bFilter": false,
        "bInfo": false,
        language: {
          'search': '',
          'searchPlaceholder': '',
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