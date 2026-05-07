<?php
require_once('./Connections/wcba.php');
require_once('./menu.php');

// Initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// SECURITY: Generate a CSRF token if one doesn't exist
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// SECURITY Helper: Escape output to prevent XSS
function e($string)
{
  return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");

if (isset($_POST['submitclearmessage'])) {
  unset($_SESSION['message']);
}

// SECURITY: Strict integer casting to completely prevent SQL injection
$jobid = isset($_GET['jobid']) ? (int)$_GET['jobid'] : (isset($_POST['jobid']) ? (int)$_POST['jobid'] : 0);

// Main Job Record
$query_Recordset1 = "SELECT 
    stringjobs.job_id as job_id, stringjobs.customerid as customerid,
    stringjobs.tension as atension, stringjobs.tensionc as atensionc,
    stringjobs.pre_tension as pre_tension, stringjobs.price as price,
    stringjobs.collection_date as collection_date, stringjobs.delivery_date as delivery_date,
    stringjobs.grip_required as grip_required, stringjobs.paid as paid,
    stringjobs.delivered as delivered, stringjobs.free_job as free_job,
    stringjobs.comments as comments, stringjobs.racketid as racketid,
    stringjobs.imageid, stringjobs.stringid as stringid, stringjobs.stringidc as stringidc,
    customer.Name as Name, customer.Email as Email, customer.Mobile as Mobile,
    sport.sportname as sportname, rackets.manuf as manuf, rackets.model as model, rackets.pattern as pattern,
    all_string.notes as notes_stock, all_stringc.notes as notesc_stock,
    string.note as notes_string, string.string_number as stringm_number,
    stringc.note as notesc_string, stringc.string_number as stringc_number,
    all_string.brand as brandm, all_string.type as typem,
    all_stringc.brand as brandc, all_stringc.type as typec,
    string.stringid as stringid_m, stringc.stringid as stringid_c,
    reel_lengthsm.length as lengthm, reel_lengthsc.length as lengthc
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
    WHERE job_id = '$jobid'";
$Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);

$customerid_safe = (int)($row_Recordset1['customerid'] ?? 0);

// All jobs for this customer
$query_Recordset2 = "SELECT 
    stringjobs.job_id as job_id, stringjobs.customerid as customerid,
    stringjobs.tension as atension, stringjobs.tensionc as atensionc,
    stringjobs.pre_tension as pre_tension, stringjobs.price as price,
    stringjobs.collection_date as collection_date, stringjobs.delivery_date as delivery_date,
    stringjobs.grip_required as grip_required, stringjobs.paid as paid,
    stringjobs.delivered as delivered, stringjobs.free_job as free_job,
    stringjobs.comments as comments, stringjobs.racketid as racketid,
    stringjobs.stringid as stringid, stringjobs.stringidc as stringid_c,
    customer.Name as Name, customer.Email as Email, customer.Mobile as Mobile,
    sport.sportname as sportname, rackets.manuf as manuf, rackets.model as model, rackets.pattern as pattern,
    all_string.brand as brandm, all_string.type as typem, all_string.notes as notes_stock,
    all_stringc.brand as brandc, all_stringc.type as typec, all_stringc.notes as notesc_stock,
    string.note as notes_string, string.string_number as stringm_number, string.stringid as stringid_m,
    stringc.note as notesc_string, stringc.string_number as stringc_number, stringc.stringid as stringid_c2,
    reel_lengthsm.length as lengthm, reel_lengthsc.length as lengthc
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
    WHERE customerid = '$customerid_safe' ORDER BY job_id DESC";
$Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);

// PERFORMANCE: Optimized Counting Queries
$sum = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(price) as SUM from stringjobs WHERE customerid = '$customerid_safe'"))['SUM'] ?? 0;
$sum_owed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(price) as SUM from stringjobs WHERE customerid = '$customerid_safe' AND paid != '1'"))['SUM'] ?? 0;
$_SESSION['sum_owed'] = $sum_owed;

$totalRows_Recordset6 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(job_id) as total FROM stringjobs WHERE collection_date LIKE '___" . $current_month_numeric . "/" . $current_year . "%' AND customerid = '$customerid_safe'"))['total'] ?? 0;
$totalRows_Recordset7 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(job_id) as total FROM stringjobs"))['total'] ?? 0;

$weight = mysqli_fetch_assoc(mysqli_query($conn, "SELECT value FROM settings WHERE id = '21'"))['value'] ?? 'lbs';

$imageid = (int)($row_Recordset1['imageid'] ?? 0);

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
  <link rel="stylesheet" href="./css/style.css">

  <title>SDBA - View Job</title>
  <link rel="icon" type="image/png" href="./img/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="./img/favicon-16x16.png" sizes="16x16" />
</head>

<body data-spy="scroll" data-target="#main-nav">

  <script>
    if (localStorage.getItem('themeSwitch') === 'dark') {
      document.body.setAttribute('data-theme', 'dark');
    }
  </script>

  <?php echo $main_menus; ?>

  <div class="home-section diva">
    <div class="subheader"></div>
    <p class="fxdtextb"><strong>View</strong> Restring: <?php echo e($jobid); ?> </p>

    <div class="container my-3 pb-3 px-3 firstparavp">
      <div class="card cardvp">
        <div class="card-body">
          <?php if (!empty($row_Recordset1['Name'])) { ?>
            <p class="form-text mb-0" style="font-size:12px">Name:</p>
            <span class="h6 form-text-alt"><?php echo e($row_Recordset1['Name']); ?></span>
          <?php } ?>

          <?php if (!empty($row_Recordset1['Mobile'])) { ?>
            <p class="form-text mb-0 mt-3" style="font-size:12px">Mobile:</p>
            <span class="h6 form-text-alt"><?php echo e($row_Recordset1['Mobile']); ?></span>
          <?php } ?>

          <?php if (!empty($row_Recordset1['Email'])) { ?>
            <p class="form-text mb-0 mt-3" style="font-size:12px">Email:</p>
            <a href="mailto:<?php echo e($row_Recordset1['Email']); ?>"><span class="h6 form-text-alt"><?php echo e($row_Recordset1['Email']); ?></span></a>
          <?php } ?>

          <?php if (isset($_SESSION['loggedin'])) { ?>
            <hr>
            <form method="post" action="./db-update.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
              <input type="hidden" name="customerid" class="txtField" value="<?php echo e($row_Recordset1['customerid']); ?>">
              <input type="hidden" name="weight" class="txtField" value="<?php echo e($weight); ?>">
              <input type="hidden" name="copytag" class="txtField" value="1">
              <input type="hidden" name="stringid" class="txtField" value="<?php echo e($row_Recordset1['stringid']); ?>">
              <input type="hidden" name="stringidc" class="txtField" value="<?php echo e($row_Recordset1['stringidc']); ?>">
              <input type="hidden" name="racketid" class="txtField" value="<?php echo e($row_Recordset1['racketid']); ?>">
              <input type="hidden" name="daterecd" class="txtField" value="<?php echo e($row_Recordset1['collection_date']); ?>">
              <input type="hidden" name="datereqd" class="txtField" value="<?php echo e($row_Recordset1['delivery_date']); ?>">
              <input type="hidden" name="preten" class="txtField" value="<?php echo e($row_Recordset1['pre_tension']); ?>">
              <input type="hidden" name="tensionm" class="txtField" value="<?php echo e($row_Recordset1['atension']); ?>">
              <input type="hidden" name="tensionc" class="txtField" value="<?php echo e($row_Recordset1['atensionc']); ?>">
              <input type="hidden" name="gripreqd" class="txtField" value="<?php echo e($row_Recordset1['grip_required']); ?>">
              <input type="hidden" name="freerestring" class="txtField" value="<?php echo e($row_Recordset1['free_job']); ?>">
              <input type="hidden" name="comments" class="txtField" value="<?php echo e($row_Recordset1['comments']); ?>">
              <button type="submit" class="btn btn-sm button-colours" name="submitadd"><i class="fa-solid fa-copy"></i> Copy</button>
            </form>
            <span class="form-text mb-0 mt-3 " style="font-size:12px">Print Label: </span><a class="fa-solid fa-tags fa-lg fa-flip-horizontal form-text-alt" title="print label" href="./label.php?jobid=<?php echo e($row_Recordset1['job_id']); ?>"></a>
            <span class="form-text mb-0 ml-3" style="font-size:12px">Print Invoice: </span><a class="fa-solid fa-tags fa-lg fa-flip-horizontal form-text-alt" title="print Invoice" href="./ex.php?customerid=<?php echo e($row_Recordset1['customerid']); ?>&jobid=<?php echo e($row_Recordset1['job_id']); ?>"></a>
          <?php } ?>

          <hr>
          <?php if (!empty($row_Recordset1['manuf'])) { ?>
            <p class="form-text mb-0" style="font-size:12px">Racket:</p>
            <span class="h6 form-text-alt"><?php echo e($row_Recordset1['manuf']) . " " . e($row_Recordset1['model']); ?></span>
          <?php } ?>

          <p class="form-text mb-0 mt-3" style="font-size:12px">String Mains:</p>
          <span class="h6 form-text-alt"><?php echo e($row_Recordset1['brandm']) . " " . e($row_Recordset1['typem']) . " " . e($row_Recordset1['notes_string']); ?></span>

          <p class="form-text mb-0 mt-3" style="font-size:12px">String Crosses:</p>
          <?php if (($row_Recordset1['stringidc'] == 0) or ($row_Recordset1['stringidc'] == $row_Recordset1['stringid'])) { ?>
            <span class="h6 form-text-alt">Same as mains.</span>
          <?php } else { ?>
            <span class="h6 form-text-alt"><?php echo e($row_Recordset1['brandc']) . " " . e($row_Recordset1['typec']) . " " . e($row_Recordset1['notesc_string']); ?></span>
          <?php } ?>

          <?php if (!empty($row_Recordset1['atension'])) { ?>
            <p class="form-text mb-0 mt-3" style="font-size:12px">Tension Mains:</p>
            <span class="h6 form-text-alt">
              <?php
              if ($weight == "kg") echo (round($row_Recordset1['atension'] * 0.45359237, 1)) . $weight;
              else echo e($row_Recordset1['atension']) . $weight;
              ?>
            </span>
          <?php } ?>

          <?php if (($row_Recordset1['atension'] != $row_Recordset1['atensionc']) && ($row_Recordset1['atensionc'] != 0)) { ?>
            <p class="form-text mb-0 mt-3" style="font-size:12px">Tension Crosses:</p>
            <span class="h6 form-text-alt">
              <?php
              if ($weight == "kg") echo (round($row_Recordset1['atensionc'] * 0.45359237, 1)) . $weight;
              else echo e($row_Recordset1['atensionc']) . $weight;
              ?>
            </span>
          <?php } elseif (!empty($row_Recordset1['atensionc']) && $row_Recordset1['atensionc'] == 0) { ?>
            <p class="form-text mb-0 mt-3" style="font-size:12px">Tension Crosses:</p>
            <span class="h6 form-text-alt">Same as mains</span>
          <?php } ?>

          <?php if (!empty($row_Recordset1['pre_tension'])) { ?>
            <p class="form-text mb-0 mt-3" style="font-size:12px">Pre-Tension:</p>
            <span class="h6 form-text-alt"><?php echo e($row_Recordset1['pre_tension']) . "%"; ?></span>
          <?php } ?>

          <hr>
          <div class="row">
            <div class="col-3">
              <?php if (!empty($row_Recordset1['price'])) { ?>
                <p class="form-text mb-0" style="font-size:12px">Price:</p>
                <span class="h6 form-text-alt"><?php echo $currency . e($row_Recordset1['price']); ?></span>
              <?php } ?>
            </div>
            <div class="col-3">
              <p class="form-text mb-0" style="font-size:12px">Paid:</p>
              <span class="h6 form-text-alt"><?php echo ($row_Recordset1['paid'] == 0) ? "Not Paid" : "Paid"; ?></span>
            </div>
            <div class="col-3">
              <p class="form-text mb-0" style="font-size:12px">Grip Required:</p>
              <span class="h6 form-text-alt"><?php echo ($row_Recordset1['grip_required'] == 1) ? "Yes" : "No"; ?></span>
            </div>
            <div class="col-3">
              <p class="form-text mb-0" style="font-size:12px">Delivered:</p>
              <span class="h6 form-text-alt"><?php echo ($row_Recordset1['delivered'] == 1) ? "Yes" : "No"; ?></span>
            </div>
          </div>

          <hr>
          <?php if (!empty($row_Recordset1['comments'])) { ?>
            <p class="form-text mb-0" style="font-size:12px">Comments:</p>
            <span class="h6 form-text-alt"><?php echo e($row_Recordset1['comments']); ?></span>
          <?php } ?>

          <?php
          if ($imageid > 0) {
            $query_Recordset5 = "SELECT * from images WHERE id = '$imageid'";
            $Recordset5 = mysqli_query($conn, $query_Recordset5);
            $row_Recordset5 = mysqli_fetch_assoc($Recordset5);
            if (!empty($row_Recordset5['image'])) {
              $imageData = $row_Recordset5['image'];
              echo '<div class="mt-3"><img class="img-responsive-width" data-toggle="modal" data-target="#exampleModal" src="data:image/jpeg;base64,' . base64_encode($imageData) . '" alt="Uploaded Image" style="max-width: 150px; cursor:pointer;"></div>'; ?>

              <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-body">
                      <?php echo '<img src="data:image/jpeg;base64,' . base64_encode($imageData) . '" alt="Uploaded Image" style="max-width: 100%;">';  ?>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>
          <?php
            }
          } ?>
        </div>
      </div>
    </div>

    <div class="container my-3 pb-3 px-3">
      <div class="card cardvp">
        <div class="card-body">
          <h5 class="text-dark">All jobs to date <small>(Click No. for more info)</small></h5>
          <?php if ($totalRows_Recordset2 > 0) { ?>
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
                <?php while ($row_Recordset2 = mysqli_fetch_assoc($Recordset2)) { ?>
                  <tr>
                    <td><a href="./viewjob.php?jobid=<?php echo e($row_Recordset2['job_id']); ?>"><?php echo e($row_Recordset2['job_id']); ?></a></td>
                    <td><?php echo e($row_Recordset2['manuf']) . " " . e($row_Recordset2['model']); ?></td>

                    <?php if ($row_Recordset2['stringid_c2'] == $row_Recordset2['stringid_m'] || empty($row_Recordset2['stringid_c2'])) { ?>
                      <td class="d-none d-md-table-cell modal-text" data-toggle="modal" data-target="#StringmViewModal<?php echo e($row_Recordset2['job_id']); ?>" style="cursor:pointer;"><?php echo e($row_Recordset2['brandm']) ?> &nbsp;<?php echo e($row_Recordset2['typem']); ?></td>

                      <div class="modal fade text-dark" id="StringmViewModal<?php echo e($row_Recordset2['job_id']); ?>">
                        <div class="modal-dialog">
                          <div class="modal-content border radius">
                            <div class="modal-header modal_header">
                              <h5 class="modal-title">Viewing Mains: &nbsp;<?php echo e($row_Recordset2['brandm']) ?> &nbsp;<?php echo e($row_Recordset2['typem']) . " " . e($row_Recordset2['notes_string']); ?></h5>
                              <button class="close" data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <div class="modal-body modal_body">
                              <p class="form-text mb-0" style="font-size:12px">Start Length:</p>
                              <?php echo e($row_Recordset2['lengthm']) . e($units ?? ''); ?>
                              <hr>
                              <p class="form-text mb-0" style="font-size:12px">Restrings Completed:</p>
                              <?php echo e($row_Recordset2['stringm_number']); ?>
                              <hr>
                              <p class="form-text mb-0" style="font-size:12px">Sport:</p>
                              <?php echo e($row_Recordset2['sportname']); ?>
                            </div>
                            <div class="modal-footer modal_footer">
                              <button class="btn modal_button_cancel" data-dismiss="modal"><span>Close</span></button>
                            </div>
                          </div>
                        </div>
                      </div>

                    <?php } else { ?>
                      <td class="d-none d-md-table-cell modal-text" data-toggle="modal" data-target="#StringcViewModal<?php echo e($row_Recordset2['job_id']); ?>" style="cursor:pointer;">Hybrid click for info</td>

                      <div class="modal fade text-dark" id="StringcViewModal<?php echo e($row_Recordset2['job_id']); ?>">
                        <div class="modal-dialog">
                          <div class="modal-content border radius">
                            <div class="modal-header modal_header">
                              <h5 class="modal-title">Viewing Mains: &nbsp;<?php echo e($row_Recordset2['brandm']) ?> &nbsp;<?php echo e($row_Recordset2['typem']) . " " . e($row_Recordset2['notes_string']); ?></h5>
                              <button class="close" data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <div class="modal-body modal_body">
                              <p class="form-text mb-0" style="font-size:12px">Start Length:</p>
                              <?php echo e($row_Recordset2['lengthm']) . e($units ?? ''); ?>
                              <hr>
                              <p class="form-text mb-0" style="font-size:12px">Restrings Completed:</p>
                              <?php echo e($row_Recordset2['stringm_number']); ?>
                            </div>
                            <div class="modal-header modal_header rounded-0">
                              <h5 class="modal-title">Viewing Crosses:&nbsp;<?php echo e($row_Recordset2['brandc']) ?> &nbsp;<?php echo e($row_Recordset2['typec']) . " " . e($row_Recordset2['notesc_string']); ?></h5>
                            </div>
                            <div class="modal-body modal_body">
                              <p class="form-text mb-0" style="font-size:12px">Start Length:</p>
                              <?php echo e($row_Recordset2['lengthc']) . e($units ?? ''); ?>
                              <hr>
                              <p class="form-text mb-0" style="font-size:12px">Restrings Completed:</p>
                              <?php echo e($row_Recordset2['stringc_number']); ?>
                              <hr>
                              <p class="form-text mb-0" style="font-size:12px">Sport:</p>
                              <?php echo e($row_Recordset2['sportname']); ?>
                            </div>
                            <div class="modal-footer modal_footer">
                              <button class="btn modal_button_cancel" data-dismiss="modal"><span>Close</span></button>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php } ?>

                    <td class="<?php echo ($row_Recordset2['delivered'] == 0) ? 'text-danger' : ''; ?>"><?php echo e($row_Recordset2['collection_date']); ?></td>
                    <td class="<?php echo ($row_Recordset2['paid'] == 0) ? 'text-danger' : ''; ?>"><?php echo $currency . e($row_Recordset2['price']); ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          <?php } else { ?>
            <p>No jobs found.</p>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>

  <div class="container center">
    <div class="p-3 row">
      <?php if (isset($_SESSION['loggedin'])) { ?>
        <div class="col-2"><a href="./addjob.php" type="button" class="dot fa-solid fa-plus fa-2x"></a></div>
      <?php } else { ?>
        <div class="col-2"><span type="button" class="dot fa-solid fa-plus fa-2x"></span></div>
      <?php } ?>

      <div class="col-2">
        <h3 class="<?php echo !empty($_SESSION['message']) ? 'blinking' : 'dotb'; ?>" title="Warning Messages" data-toggle="modal" data-target="#warningModal"><strong>!</strong></h3>
      </div>
      <div class="col-2">
        <div class="dotbt h6" title="Restrings for <?php echo $current_month_text; ?>"><span class="text-center"><?php echo $totalRows_Recordset6 ?></span></div>
      </div>
      <div class="col-2">
        <div class="dotbt h6" title="Total restrings"><span class="text-center"><?php echo $totalRows_Recordset7 ?></span></div>
      </div>
      <div class="col-2"><a href="./jobs-unpaid.php" class="dotbt h6" title="Amount Owed"><?php echo $currency . e($sum_owed); ?></a></div>
      <div class="col-2"><a href="#" class="dotbtt h7" title="Total Income"><small><?php echo $currency . e($sum); ?></small></a></div>
    </div>
  </div>

  <div class="modal fade text-dark" id="warningModal">
    <div class="modal-dialog">
      <div class="modal-content border radius">
        <div class="modal-header modal_header">
          <h5 class="modal-title">Information:</h5>
          <button class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body modal_body">
          <div><?php echo $_SESSION['message'] ?? ''; ?></div>
        </div>
        <div class="modal-footer modal_footer">
          <div class="container mt-3" style="margin-top: 120px;">
            <div class="row pt-3">
              <div class="col-8">
                <a class="btn modal_button_cancel" href="./viewjob.php?jobid=<?php echo e($jobid); ?>">Cancel</a>
              </div>
              <div class="col-4">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                  <input type="hidden" name="jobid" value="<?php echo e($jobid); ?>">
                  <input class="btn modal_button_submit float-right" type="submit" name="submitclearmessage" value="Clear">
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

  <script>
    $('#year').text(new Date().getFullYear());
    $('body').scrollspy({
      target: '#main-nav'
    });

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
          "infoEmpty": ""
        },
        pageLength: 15,
        autoWidth: false,
        order: [
          [0, 'desc']
        ]
      });
    });

    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.querySelector(".nav-menu");
    if (hamburger) {
      hamburger.addEventListener("click", () => {
        hamburger.classList.toggle("active");
        navMenu.classList.toggle("active");
      });
    }

    // Clean, unified Theme Switch Logic
    var themeSwitch = document.getElementById('themeSwitch');
    if (themeSwitch) {
      var darkThemeSelected = (localStorage.getItem('themeSwitch') === 'dark');
      themeSwitch.checked = darkThemeSelected;

      themeSwitch.addEventListener('change', function() {
        var logo = document.getElementById("imglogo");
        if (this.checked) {
          document.body.setAttribute('data-theme', 'dark');
          localStorage.setItem('themeSwitch', 'dark');
          if (logo) logo.src = "./img/logo-dark.png";
        } else {
          document.body.removeAttribute('data-theme');
          localStorage.removeItem('themeSwitch');
          if (logo) logo.src = "./img/logo.png";
        }
      });
    }

    // Set logo correctly on initial load
    var imgsrc = localStorage.getItem('themeSwitch');
    var initLogo = document.getElementById("imglogo");
    if (initLogo) {
      initLogo.src = (imgsrc === "dark") ? "./img/logo-dark.png" : "./img/logo.png";
    }
  </script>
</body>

</html>