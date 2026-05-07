<?php
// SECURITY: Better connection file check
if (!file_exists("./Connections/wcba.php")) {
  header("location:./db-config.php?code=1378907769354882");
  exit;
}
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

// SECURITY Helper: Quick function to escape output and prevent XSS
function e($string)
{
  return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

$current_month_text = date("M");
$current_month_numeric = date("m");
$current_year = date("Y");

// Recordset 1: Main Data (Kept as is, but output will be sanitized)
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
reel_lengthsm.length as lengthm,
reel_lengthsc.length as lengthc,
stringc.note as notesc_string,
stringc.string_number as stringc_number,
stringc.stringid as stringid_c
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
ORDER BY job_id DESC";
$Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);




// Reverting to the original query to ensure we capture all columns accurately
$query_Recordset2 = "SELECT * FROM string 
                     LEFT JOIN all_string ON string.stock_id = all_string.string_id 
                     LEFT JOIN reel_lengths ON reel_lengths.reel_length_id = string.lengthid 
                     ORDER BY string.stringid ASC;";
$Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));







// PERFORMANCE: Recordsets 3-7 changed to COUNT() to save massive amounts of memory
$query_Recordset3 = "SELECT COUNT(*) as total FROM customer;";
$row_Recordset3 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset3));
$totalRows_Recordset3 = $row_Recordset3['total'];

$query_Recordset4 = "SELECT COUNT(*) as total FROM rackets;";
$row_Recordset4 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset4));
$totalRows_Recordset4 = $row_Recordset4['total'];

$query_Recordset5 = "SELECT COUNT(*) as total FROM grip;";
$row_Recordset5 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset5));
$totalRows_Recordset5 = $row_Recordset5['total'];

$query_Recordset6 = "SELECT COUNT(*) as total FROM stringjobs WHERE collection_date LIKE '___" . $current_month_numeric . "/" . $current_year . "%';";
$row_Recordset6 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset6));
$totalRows_Recordset6 = $row_Recordset6['total'];

$query_Recordset7 = "SELECT COUNT(*) as total FROM stringjobs;";
$row_Recordset7 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset7));
$totalRows_Recordset7 = $row_Recordset7['total'];

// Totals
$query_Recordset8 = "SELECT SUM(price) as SUM from stringjobs;";
$row_Recordset8 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset8));
$sum = $row_Recordset8['SUM'] ?? 0;

$query_Recordset9 = "SELECT SUM(price) as SUM from stringjobs WHERE paid != '1';";
$row_Recordset9 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset9));
$sum_owed = $row_Recordset9['SUM'] ?? 0;
$_SESSION['sum_owed'] = $sum_owed;

$query_Recordset15 = "SELECT value FROM settings WHERE id = '21';";
$row_Recordset15 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset15));
$weight = $row_Recordset15['value'] ?? '';


// Build Warning Messages for Low Stock (Using PHP logic safely)
$_SESSION['message'] = '';
while ($row_Recordset2 = mysqli_fetch_assoc($Recordset2)) {
  if (($row_Recordset2['string_number'] > $row_Recordset2['warning_level']) && ($row_Recordset2['empty'] == 0)) {
    $_SESSION['message'] .= "String reel (" . e($row_Recordset2['stringid']) . ") " . e($row_Recordset2['brand']) . " " . e($row_Recordset2['type']) . " is low <br>";
  }
}




?>
<!DOCTYPE html>
<html lang="en">

<head>
  <script>
    const storedTheme = localStorage.getItem('themeSwitch');
    if (storedTheme) {
      document.documentElement.setAttribute('data-theme', storedTheme);
    }
  </script>
  <script type="text/javascript" src="./js/theme.js"></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
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
  <?php echo $main_menus; ?>

  <div class="home-section diva">
    <div class="subheader"></div>
    <p class="fxdtextb"><strong>All</strong> Restrings</p>

    <?php if ($totalRows_Recordset1 == 0) { ?>
      <h5 class='text-center text-dark' style='margin-top: 200px;'>No Records found</h5>
    <?php } else { ?>
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
          <?php while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1)) {
            $is_hybrid = ($row_Recordset1['stringid_c'] != $row_Recordset1['stringid_m']);
          ?>
            <tr>
              <td class="tdm">
                <a class="modal-text" href="./viewjob.php?jobid=<?php echo e($row_Recordset1['job_id']); ?>"><?php echo e($row_Recordset1['job_id']); ?></a>
              </td>
              <td>
                <a class="modal-text" href="./editcust.php?custid=<?php echo e($row_Recordset1['customerid']); ?>">
                  <span><?php echo e(substr($row_Recordset1['Name'], 0, 12)); ?></span>
                </a>
              </td>

              <?php if (!$is_hybrid) { ?>
                <td class="d-none d-md-table-cell modal-text view-string-btn"
                  data-title="Viewing Mains: <?php echo e($row_Recordset1['brandm'] . ' ' . $row_Recordset1['typem'] . ' ' . $row_Recordset1['notes_string']); ?>"
                  data-length="<?php echo e($row_Recordset1['lengthm']); ?>"
                  data-number="<?php echo e($row_Recordset1['stringm_number']); ?>"
                  data-sport="<?php echo e($row_Recordset1['sportname']); ?>"
                  data-hybrid="false">
                  <?php echo e($row_Recordset1['brandm']); ?> &nbsp;<?php echo e($row_Recordset1['typem']); ?>
                </td>
              <?php } else { ?>
                <td class="d-none d-md-table-cell modal-text view-string-btn"
                  data-title="Viewing Mains: <?php echo e($row_Recordset1['brandm'] . ' ' . $row_Recordset1['typem'] . ' ' . $row_Recordset1['notes_string']); ?>"
                  data-length="<?php echo e($row_Recordset1['lengthm']); ?>"
                  data-number="<?php echo e($row_Recordset1['stringm_number']); ?>"
                  data-title-c="Viewing Crosses: <?php echo e($row_Recordset1['brandc'] . ' ' . $row_Recordset1['typec'] . ' ' . $row_Recordset1['notesc_string']); ?>"
                  data-length-c="<?php echo e($row_Recordset1['lengthc']); ?>"
                  data-number-c="<?php echo e($row_Recordset1['stringc_number']); ?>"
                  data-sport="<?php echo e($row_Recordset1['sportname']); ?>"
                  data-hybrid="true">
                  Hybrid click for info
                </td>
              <?php } ?>

              <td class="<?php echo ($row_Recordset1['delivered'] == 0) ? 'text-danger' : ''; ?>">
                <?php echo e($row_Recordset1['collection_date']); ?>
              </td>

              <td>
                <form method="post" action="./db-update.php">
                  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                  <input onChange="this.form.submit()" class="form-inline" type="checkbox" name="deliveredupdate" value="1" <?php if ($row_Recordset1['delivered'] == 1) echo "checked"; ?>>
                  <input type="hidden" name="jobiddeliveredupdate" value="<?php echo e($row_Recordset1['job_id']); ?>">
                </form>
              </td>

              <td class="<?php echo ($row_Recordset1['paid'] == 0) ? 'text-danger' : ''; ?>">
                <?php echo $currency . e($row_Recordset1['price']); ?>
              </td>

              <td>
                <form class="form-inline" method="post" action="./db-update.php">
                  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                  <input onChange="this.form.submit()" type="checkbox" name="paidupdate" value="1" <?php if ($row_Recordset1['paid'] == 1) echo "checked"; ?>>
                  <input type="hidden" name="jobidpaidupdate" value="<?php echo e($row_Recordset1['job_id']); ?>">
                </form>
              </td>

              <td><a class="fa-solid fa-pen-to-square fa-lg modal-text" href="./editjob.php?jobid=<?php echo e($row_Recordset1['job_id']); ?>"></a></td>

              <td class="text-center d-none d-md-table-cell">
                <form method="post" action="./db-update.php">
                  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                  <input type="hidden" name="customerid" value="<?php echo e($row_Recordset1['customerid']); ?>">
                  <input type="hidden" name="weight" value="<?php echo e($weight); ?>">
                  <input type="hidden" name="copytag" value="1">
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

              <td class="d-none d-md-table-cell">
                <i class="modal-text fa-solid fa-trash-can fa-lg delete-btn"
                  data-jobid="<?php echo e($row_Recordset1['job_id']); ?>"
                  data-stringidm="<?php echo e($row_Recordset1['stringidm']); ?>"
                  data-stringidc="<?php echo e(isset($row_Recordset1['stringidc']) ? $row_Recordset1['stringidc'] : ''); ?>"></i>
              </td>
              <td><a class="fa-solid fa-tags fa-lg fa-flip-horizontal modal-text" href="./label.php?jobid=<?php echo e($row_Recordset1['job_id']); ?>"></a></td>
              <td class="text-center d-none d-md-table-cell"><img class="imgsporticon m-0 p-0" src="./img/<?php echo e($row_Recordset1['image']); ?>" width="18" height="18" style="padding:0; margin:0"></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } ?>
  </div>

  <div class="container center">
    <div class="p-3 row">
      <div class="col-2"><a href="./addjob.php" type="button" class="dot fa-solid fa-plus fa-2x"></a></div>
      <div class="col-2">
        <h3 class="<?php echo !empty($_SESSION['message']) ? 'blinking' : 'dotb'; ?>" title="Warning Messages" data-toggle="modal" data-target="#warningModal"><strong>!</strong></h3>
      </div>
      <div class="col-2">
        <div class="dotbt h6" title="Restrings for <?php echo $current_month_text; ?>"><span class=" text-center"><?php echo $totalRows_Recordset6 ?></span></div>
      </div>
      <div class="col-2">
        <div class="dotbt h6" title="Total restrings"><span class="text-center"><?php echo $totalRows_Recordset7 ?></span></div>
      </div>
      <div class="col-2"><a href="./jobs-unpaid.php" class="dotbt h6" title="Amount Owed"><?php echo $currency . $sum_owed ?></a></div>
      <div class="col-2"><a href="#" class="dotbtt h7" title="Total Income"><small><?php echo $currency . $sum ?></small></a></div>
    </div>
  </div>

  <div class="modal fade" id="globalDelModal">
    <div class="modal-dialog">
      <div class="modal-content border radius">
        <div class="modal-header modal_header">
          <h5 class="modal-title" id="delModalTitle">You are about to delete job</h5>
          <button class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body modal_body">
          <form method="post" action="./db-update.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div>Please confirm or cancel!</div>
            <div style="padding-bottom:5px;"></div>
            <input type="hidden" name="refdel" id="delJobId" value="">
            <input type="hidden" name="stringidm" id="delStringIdm" value="">
            <input type="hidden" name="stringidc" id="delStringIdc" value="">
        </div>
        <div class="modal-footer modal_footer">
          <button type="button" class="btn modal_button_cancel" data-dismiss="modal"><span>Cancel</span></button>
          <input class="btn modal_button_submit" type="submit" name="submitdelete" value="Delete">
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="globalStringModal">
    <div class="modal-dialog">
      <div class="modal-content border radius">
        <div class="modal-header modal_header">
          <h5 class="modal-title" id="strModalTitleM"></h5>
          <button class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body modal_body">
          <p class="form-text mb-0" style="font-size:12px">Start Length:</p>
          <span id="strLengthM"></span> <?php echo $units ?? ''; ?>
          <hr>
          <p class="form-text mb-0" style="font-size:12px">Restrings Completed:</p>
          <span id="strNumberM"></span>

          <div id="hybridSection" style="display:none;">
          </div>
          <div class="modal-header modal_header rounded-0">
            <h5 class="modal-title" id="strModalTitleC"></h5>
          </div>
          <div class="modal-body modal_body">
            <p class="form-text mb-0" style="font-size:12px">Start Length:</p>
            <span id="strLengthC"></span> <?php echo $units ?? ''; ?>
            <hr>
            <p class="form-text mb-0" style="font-size:12px">Restrings Completed:</p>
            <span id="strNumberC"></span>
          </div>
          <hr>
          <p class="form-text mb-0" style="font-size:12px">Sport:</p>
          <span id="strSport"></span>
        </div>
        <div class="modal-footer modal_footer">
          <button class="btn modal_button_cancel" data-dismiss="modal"><span>Close</span></button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="warningModal">
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
  <script src="https://cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

  <script>
    $('#year').text(new Date().getFullYear());
    $('body').scrollspy({
      target: '#main-nav'
    });

    // Handle Global Delete Modal Population
    $(document).on("click", ".delete-btn", function() {
      $('#delModalTitle').text('You are about to delete job "' + $(this).data('jobid') + '"');
      $('#delJobId').val($(this).data('jobid'));
      $('#delStringIdm').val($(this).data('stringidm'));
      $('#delStringIdc').val($(this).data('stringidc'));
      $('#globalDelModal').modal('show');
    });

    // Handle Global String View Modal Population
    $(document).on("click", ".view-string-btn", function() {
      $('#strModalTitleM').text($(this).data('title'));
      $('#strLengthM').text($(this).data('length'));
      $('#strNumberM').text($(this).data('number'));
      $('#strSport').text($(this).data('sport'));

      if ($(this).data('hybrid') === true) {
        $('#strModalTitleC').text($(this).data('title-c'));
        $('#strLengthC').text($(this).data('length-c'));
        $('#strNumberC').text($(this).data('number-c'));
        $('#hybridSection').show();
      } else {
        $('#hybridSection').hide();
      }
      $('#globalStringModal').modal('show');
    });

    jQuery(document).ready(function($) {
      $('#tblUser').DataTable({
        stateSave: true,
        pagingType: "simple_numbers",
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
            targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
            className: "dt-head-center"
          },
          {
            target: 3,
            sWidth: '20px',
            orderable: false
          },
          {
            target: 4,
            orderable: false,
            className: 'dt-left',
            sWidth: '20px'
          },
          {
            target: 5,
            sWidth: '0px',
            orderable: false
          },
          {
            target: 6,
            orderable: false,
            className: 'dt-left',
            sWidth: '0px',
            padding: '0'
          },
          {
            targets: [7, 8, 9, 10, 11],
            orderable: false
          }
        ],
      });
    });

    $(function() {
      $('.datepicker').datepicker({
        language: "en",
        autoclose: true,
        format: "dd/mm/yyyy"
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

    var themeSwitch = document.getElementById('themeSwitch');
    if (themeSwitch) {
      var darkThemeSelected = (localStorage.getItem('themeSwitch') === 'dark');
      themeSwitch.checked = darkThemeSelected;

      themeSwitch.addEventListener('change', function(event) {
        if (themeSwitch.checked) {
          document.body.setAttribute('data-theme', 'dark');
          document.getElementById("imglogo").src = "./img/logo-dark.png";
          localStorage.setItem('themeSwitch', 'dark');
        } else {
          document.body.removeAttribute('data-theme');
          document.getElementById("imglogo").src = "./img/logo.png";
          localStorage.removeItem('themeSwitch');
        }
      });
    }

    var imgsrc = localStorage.getItem('themeSwitch');
    var logo = document.getElementById("imglogo");
    if (logo) {
      logo.src = (imgsrc === "dark") ? "./img/logo-dark.png" : "./img/logo.png";
    }
  </script>
</body>

</html>