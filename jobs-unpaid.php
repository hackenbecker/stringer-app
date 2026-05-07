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

// SECURITY Helper: Escape output to prevent XSS
function e($string)
{
  return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

$current_month_text = date("M");
$current_month_numeric = date("m");
$current_year = date("Y");

// Fetch Unpaid Jobs
$query_Recordset1 = "SELECT 
    stringjobs.job_id as job_id, stringjobs.customerid as customerid,
    stringjobs.tension as atension, stringjobs.tensionc as atensionc,
    stringjobs.pre_tension as pre_tension, stringjobs.price as price,
    stringjobs.collection_date as collection_date, stringjobs.delivery_date as delivery_date,
    stringjobs.grip_required as grip_required, stringjobs.paid as paid,
    stringjobs.delivered as delivered, stringjobs.free_job as free_job,
    stringjobs.comments as comments, stringjobs.racketid as racketid,
    stringjobs.stringid as stringidm, stringjobs.stringidc as stringidc,
    customer.Name as Name, customer.Email as Email, customer.Mobile as Mobile,
    sport.sportname as sportname, sport.image as image,
    rackets.manuf as manuf, rackets.model as model,
    rackets.pattern as pattern, rackets.sport as sportid,
    all_string.brand as brandm, all_string.type as typem, all_string.notes as notes_stock,
    all_stringc.brand as brandc, all_stringc.type as typec, all_stringc.notes as notesc_stock,
    string.note as notes_string, string.string_number as stringm_number, string.stringid as stringid_m,
    reel_lengthsm.length as lengthm, reel_lengthsc.length as lengthc,
    stringc.note as notesc_string, stringc.string_number as stringc_number, stringc.stringid as stringid_c
    FROM stringjobs 
    LEFT JOIN customer ON customerid = cust_ID
    LEFT JOIN string ON stringjobs.stringid = string.stringid 
    LEFT JOIN string AS stringc ON stringjobs.stringidc = stringc.stringid
    LEFT JOIN all_string ON string.stock_id = all_string.string_id
    LEFT JOIN all_string AS all_stringc ON stringc.stock_id = all_stringc.string_id
    LEFT JOIN reel_lengths AS reel_lengthsm ON reel_lengthsm.reel_length_id = string.lengthid
    LEFT JOIN reel_lengths AS reel_lengthsc ON reel_lengthsc.reel_length_id = string.lengthid
    LEFT JOIN rackets ON stringjobs.racketid = rackets.racketid 
    LEFT JOIN sport ON rackets.sport = sport.sportid
    WHERE paid = '0'
    ORDER BY job_id DESC";
$Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);

// PERFORMANCE: Optimized Counting Queries
$query_Recordset6 = "SELECT COUNT(job_id) as total FROM stringjobs WHERE collection_date LIKE '___" . $current_month_numeric . "/" . $current_year . "%'";
$totalRows_Recordset6 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset6))['total'] ?? 0;

$query_Recordset7 = "SELECT COUNT(job_id) as total FROM stringjobs";
$totalRows_Recordset7 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset7))['total'] ?? 0;

$query_Recordset8 = "SELECT SUM(price) as SUM from stringjobs";
$sum = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset8))['SUM'] ?? 0;

$query_Recordset9 = "SELECT SUM(price) as SUM from stringjobs WHERE paid != '1'";
$sum_owed = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset9))['SUM'] ?? 0;
$_SESSION['sum_owed'] = $sum_owed;

// LOW STRING WARNING LOGIC (Moved here and optimized)
$low_stock_msg = "";
$query_warnings = "SELECT string.stringid, all_string.brand, all_string.type, string.string_number, warning_level 
                   FROM string 
                   LEFT JOIN all_string ON string.stock_id = all_string.string_id 
                   LEFT JOIN reel_lengths ON reel_lengths.reel_length_id = string.lengthid
                   WHERE string.empty = '0'";
$res_warnings = mysqli_query($conn, $query_warnings);
while ($r_warn = mysqli_fetch_assoc($res_warnings)) {
  if ($r_warn['string_number'] > $r_warn['warning_level']) {
    $low_stock_msg .= "String reel (" . e($r_warn['stringid']) . ") " . e($r_warn['brand']) . " " . e($r_warn['type']) . " is low <br>";
  }
}
if (!empty($low_stock_msg)) {
  if (!isset($_SESSION['message'])) $_SESSION['message'] = "";
  if (strpos($_SESSION['message'], "is low") === false) {
    $_SESSION['message'] .= $low_stock_msg;
  }
}
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

  <title>SDBA - Unpaid Jobs</title>
  <link rel="icon" type="image/png" href="./img/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="./img/favicon-16x16.png" sizes="16x16" />
  <meta name="color-scheme" content="dark light" />
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
    <p class="fxdtextb"><strong>UNPAID</strong> Restrings</p>

    <?php if ($totalRows_Recordset1 == 0) {
      echo "<h5 class='text-center text-dark' style='margin-top: 200px;'>No Records found</h5> ";
    } else { ?>

      <div class="container px-3 pb-3 firstparavp table-responsive" style="margin-top: 120px;">
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
            <?php while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1)) { ?>
              <tr>
                <td class="tdm">
                  <a class="modal-text" href="./viewjob.php?jobid=<?php echo e($row_Recordset1['job_id']); ?>"><?php echo e($row_Recordset1['job_id']); ?></a>
                </td>
                <td>
                  <a class="modal-text" href="./editcust.php?custid=<?php echo e($row_Recordset1['customerid']); ?>">
                    <span><?php echo e(substr($row_Recordset1['Name'], 0, 12)); ?></span>
                  </a>
                </td>

                <?php if (($row_Recordset1['stringid_c'] == $row_Recordset1['stringid_m']) || ($row_Recordset1['stringid_c'] == 0)) { ?>
                  <td class="<?php echo ($row_Recordset1['stringm_number'] == 1) ? 'text-primary' : ''; ?> d-none d-md-table-cell modal-text" data-toggle="modal" data-target="#StringViewModal<?php echo e($row_Recordset1['stringid_m']); ?>" style="cursor:pointer;">
                    <?php echo e($row_Recordset1['brandm']) ?> &nbsp;<?php echo e($row_Recordset1['typem']); ?>
                  </td>
                <?php } elseif (($row_Recordset1['stringid_c'] != $row_Recordset1['stringid_m']) && ($row_Recordset1['stringid_c'] != 0)) { ?>
                  <td class="d-none d-md-table-cell modal-text" data-toggle="modal" data-target="#StringViewModal<?php echo e($row_Recordset1['stringid_m']); ?>" style="cursor:pointer;">
                    Hybrid click for info
                  </td>
                <?php } else { ?>
                  <td class="d-none d-md-table-cell modal-text">String Unknown</td>
                <?php } ?>

                <div class="modal fade" id="StringViewModal<?php echo e($row_Recordset1['stringid_m']); ?>">
                  <div class="modal-dialog">
                    <div class="modal-content border radius">
                      <div class="modal-header modal_header">
                        <h5 class="modal-title">Viewing Mains: &nbsp;<?php echo e($row_Recordset1['brandm'] . ' ' . $row_Recordset1['typem'] . ' ' . $row_Recordset1['notes_string']); ?></h5>
                        <button class="close" data-dismiss="modal"><span>&times;</span></button>
                      </div>
                      <div class="modal-body modal_body text-dark">
                        <p class="form-text mb-0" style="font-size:12px">Start Length:</p>
                        <?php echo e($row_Recordset1['lengthm']) . e($units); ?>
                        <hr>
                        <p class="form-text mb-0" style="font-size:12px">Restrings Completed:</p>
                        <?php echo e($row_Recordset1['stringm_number']); ?>

                        <?php if ($row_Recordset1['stringid_c'] != $row_Recordset1['stringid_m'] && !is_null($row_Recordset1['stringid_c'])) { ?>
                      </div>
                      <div class="modal-header modal_header rounded-0">
                        <h5 class="modal-title">Viewing Crosses:&nbsp;<?php echo e($row_Recordset1['brandc'] . ' ' . $row_Recordset1['typec'] . ' ' . $row_Recordset1['notesc_string']); ?></h5>
                      </div>
                      <div class="modal-body modal_body text-dark">
                        <p class="form-text mb-0" style="font-size:12px">Start Length:</p>
                        <?php echo e($row_Recordset1['lengthc']) . e($units); ?>
                        <hr>
                        <p class="form-text mb-0" style="font-size:12px">Restrings Completed:</p>
                        <?php echo e($row_Recordset1['stringc_number']); ?>
                      <?php } ?>
                      <hr>
                      <p class="form-text mb-0" style="font-size:12px">Sport:</p>
                      <?php echo e($row_Recordset1['sportname']); ?>
                      </div>
                      <div class="modal-footer modal_footer">
                        <button class="btn modal_button_cancel" data-dismiss="modal"><span>Close</span></button>
                      </div>
                    </div>
                  </div>
                </div>

                <td class="<?php echo ($row_Recordset1['delivered'] == 0) ? 'text-danger' : ''; ?>">
                  <?php echo e($row_Recordset1['collection_date']); ?>
                </td>

                <td>
                  <form method="post" action="./db-update.php">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input onChange="this.form.submit()" class="form-inline" type="checkbox" name="deliveredupdate" value="1" <?php if ($row_Recordset1['delivered'] == 1) echo " checked"; ?>>
                    <input type="hidden" name="jobiddeliveredupdate" class="txtField" value="<?php echo e($row_Recordset1['job_id']); ?>">
                  </form>
                </td>

                <td class="<?php echo ($row_Recordset1['paid'] == 0) ? 'text-danger' : ''; ?>">
                  <?php echo e($currency . $row_Recordset1['price']); ?>
                </td>

                <td>
                  <form class="form-inline" method="post" action="./db-update.php">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input onChange="this.form.submit()" type="checkbox" name="paidupdate" value="1" <?php if ($row_Recordset1['paid'] == 1) echo " checked"; ?>>
                    <input type="hidden" name="jobidpaidupdate" class="txtField" value="<?php echo e($row_Recordset1['job_id']); ?>">
                  </form>
                </td>

                <td><a class="fa-solid fa-pen-to-square fa-lg modal-text" href="./editjob.php?jobid=<?php echo e($row_Recordset1['job_id']); ?>"></a></td>

                <td class="text-center d-none d-md-table-cell">
                  <form method="post" action="./db-update.php">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="customerid" value="<?php echo e($row_Recordset1['customerid']); ?>">
                    <input type="hidden" name="stringid" value="<?php echo e($row_Recordset1['stringidm']); ?>">
                    <input type="hidden" name="stringidc" value="<?php echo e($row_Recordset1['stringidc']); ?>">
                    <input type="hidden" name="racketid" value="<?php echo e($row_Recordset1['racketid']); ?>">
                    <input type="hidden" name="daterecd" value="<?php echo e($row_Recordset1['collection_date']); ?>">
                    <input type="hidden" name="datereqd" value="<?php echo e($row_Recordset1['delivery_date']); ?>">
                    <input type="hidden" name="preten" value="<?php echo e($row_Recordset1['pre_tension']); ?>">
                    <input type="hidden" name="tensionm" value="<?php echo e($row_Recordset1['atension']); ?>">
                    <input type="hidden" name="tensionc" value="<?php echo e($row_Recordset1['atensionc']); ?>">
                    <input type="hidden" name="gripreqd" value="<?php echo e($row_Recordset1['grip_required']); ?>">
                    <input type="hidden" name="freerestring" value="<?php echo e($row_Recordset1['free_job']); ?>">
                    <input type="hidden" name="comments" value="<?php echo e($row_Recordset1['comments']); ?>">
                    <button type="submit" style="background-color: transparent;border:0;padding:0px;" class="button-colours-rollover" name="submitadd"><i title="copy" class="fa-solid fa-copy fa-lg"></i></button>
                  </form>
                </td>

                <td class="d-none d-md-table-cell"><i class="modal-text fa-solid fa-trash-can fa-lg" data-toggle="modal" data-target="#delModal<?php echo e($row_Recordset1['job_id']); ?>" style="cursor:pointer;"></i></td>
                <td><a class="fa-solid fa-tags fa-lg fa-flip-horizontal modal-text" href="./label.php?jobid=<?php echo e($row_Recordset1['job_id']); ?>"></a></td>
                <td class="text-center d-none d-md-table-cell"><img class="imgsporticon m-0 p-0" src="./img/<?php echo e($row_Recordset1['image']); ?>" width="18" height="18" style="padding:0; margin:0"></td>
              </tr>

              <div class="modal fade text-dark" id="delModal<?php echo e($row_Recordset1['job_id']); ?>">
                <div class="modal-dialog">
                  <div class="modal-content border radius">
                    <div class="modal-header modal_header">
                      <h5 class="modal-title">You are about to delete Job &nbsp;"<?php echo e($row_Recordset1['job_id']); ?>"</h5>
                      <button class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body modal_body">
                      <form method="post" action="./db-update.php">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div>Please confirm or cancel!</div>
                        <div style="padding-bottom:5px;"></div>
                        <input type="hidden" name="refdel" value="<?php echo e($row_Recordset1['job_id']); ?>">
                        <input type="hidden" name="stringidm" value="<?php echo e($row_Recordset1['stringidm']); ?>">
                        <?php if (isset($row_Recordset1['stringidc'])) { ?>
                          <input type="hidden" name="stringidc" value="<?php echo e($row_Recordset1['stringidc']); ?>">
                        <?php } ?>
                    </div>
                    <div class="modal-footer modal_footer">
                      <button class="btn modal_button_cancel" data-dismiss="modal"><span>Cancel</span></button>
                      <input class="btn modal_button_submit" type="submit" name="submitdelete" value="Delete">
                      </form>
                    </div>
                  </div>
                </div>
              </div>

            <?php } ?>
          </tbody>
        </table>
      </div>
    <?php } ?>
  </div>

  <div class="container center">
    <div class="p-3 row">
      <div class="col-2"><a href="./addjob.php" type="button" class="dot fa-solid fa-plus fa-2x"></a></div>
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
          <div class="container mt-3">
            <div class="row pt-3">
              <div class="col-8">
                <a class="btn modal_button_cancel" href="./string-jobs.php">Cancel</a>
              </div>
              <div class="col-4">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>

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
        language: {
          'search': '',
          'searchPlaceholder': 'Search:',
          "sLengthMenu": "",
          "info": "",
          "infoEmpty": "",
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
            targets: [4, 5, 6, 7, 8, 9, 10, 11],
            orderable: false
          }
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

    var imgsrc = localStorage.getItem('themeSwitch');
    var initLogo = document.getElementById("imglogo");
    if (initLogo) {
      initLogo.src = (imgsrc === "dark") ? "./img/logo-dark.png" : "./img/logo.png";
    }
  </script>
</body>

</html>