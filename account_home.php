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
if (isset($_POST['submitclearmessage'])) {
  unset($_SESSION['message']);
}

// SECURITY Helper: Escape output to prevent XSS
function e($string)
{
  return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");

// Recordset 1: String Jobs by this user
// Using Prepared-statement style casting for security
$userId = (int)$_SESSION['id'];

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
WHERE addedby = '$userId' ORDER BY job_id DESC";
$Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);

// Recordset 2: Account Details
$sql = "SELECT * FROM accounts WHERE id = '$userId'";
$Recordset2 = mysqli_query($conn, $sql) or die(mysqli_error($conn));

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

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css">
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
  <title>SDBA - Account Home</title>
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
    <p class="fxdtextb"><strong>Account: </strong><?php echo e($_SESSION['name']); ?></p>
    <div class="container mt-3 pb-3 px-3 ">&nbsp;</div>

    <table id="tblUser" class="table-striped table-text table table-sm center">
      <thead>
        <tr>
          <th>Username</th>
          <th class="d-none d-lg-table-cell text-center">Email</th>
          <th class="text-center">Access Level</th>
          <th class="d-none d-lg-table-cell text-center">Active</th>
          <th class="d-none d-md-table-cell text-center">Password</th>
          <th class="text-center">Edit</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row_Recordset2 = mysqli_fetch_assoc($Recordset2)) { ?>
          <tr>
            <td class="pl-3"><?php echo e($row_Recordset2['username']); ?></td>
            <td class="d-none d-lg-table-cell pl-3 text-center"><?php echo e($row_Recordset2['email']); ?></td>
            <td class="pl-3 text-center"><?php echo e($row_Recordset2['level']); ?></td>
            <td class="d-none d-lg-table-cell text-center">
              <?php if ($row_Recordset2['active'] == '1') { ?>
                <i class="text-success fa-solid fa-check"></i>
              <?php } else { ?>
                <i class="text-danger fa-solid fa-xmark"></i>
              <?php } ?>
            </td>
            <td class="d-none d-md-table-cell text-center">
              <small class="p-1 modal_button_submit rounded m-1" data-toggle="modal" data-target="#UserPass<?php echo $row_Recordset2['id']; ?>" style="cursor:pointer;">Reset Password</small>
            </td>
            <td class="text-center"><i class="fa-solid fa-pen-to-square" data-toggle="modal" data-target="#UserEdit<?php echo $row_Recordset2['id']; ?>" style="cursor:pointer;"></i></td>
          </tr>

          <div class="modal fade text-dark" id="UserEdit<?php echo $row_Recordset2['id']; ?>">
            <div class="modal-dialog">
              <div class="modal-content border radius">
                <div class="modal-header modal_header">
                  <h5 class="modal-title">You are editing "<?php echo e($row_Recordset2['username']); ?>"</h5>
                  <button class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body modal_body">
                  <form method="post" action="site-users-db.php">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="refedit" class="txtField" value="<?php echo e($row_Recordset2['id']); ?>">
                    <div class="form-group">
                      <label for="name">User Name</label>
                      <input class="form-control" id="name" type="text" name="username" value="<?php echo e($row_Recordset2['username']); ?>">
                      <label class="pt-3" for="email">Email Address</label>
                      <input class="form-control" id="email" type="text" name="email" value="<?php echo e($row_Recordset2['email']); ?>">
                    </div>
                    <input type="hidden" name="level" value="<?php echo e($_SESSION['level']); ?>">
                    <input type="hidden" name="active" value="1">
                    <input type="hidden" name="marker" value="1">
                </div>
                <div class="modal-footer modal_footer">
                  <button type="button" class="btn modal_button_cancel" data-dismiss="modal"><span>Cancel</span></button>
                  <input class="btn modal_button_submit" type="submit" name="submitEdit" value="Submit">
                </div>
                </form>
              </div>
            </div>
          </div>

          <div class="modal fade text-dark" id="UserPass<?php echo $row_Recordset2['id']; ?>">
            <div class="modal-dialog">
              <div class="modal-content border radius">
                <div class="modal-header modal_header">
                  <h5 class="modal-title">Reset password for "<?php echo e($row_Recordset2['username']); ?>"</h5>
                  <button class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body modal_body">
                  <form method="post" action="site-users-db.php">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="refedit" value="<?php echo e($row_Recordset2['id']); ?>">
                    <div class="form-group">
                      <label for="name1">Password:</label>
                      <input class="form-control" id="name1" type="password" name="password1" placeholder="Type password">
                      <label class="mt-2" for="name2">Repeat Password:</label>
                      <input class="form-control" id="name2" type="password" name="password2" placeholder="Type password">
                      <p class="pt-2 text-dark" style="font-size:0.85em;">Password 8 characters minimum.<br>
                        At least one uppercase letter.<br>
                        At least one lowercase letter.<br>
                        At least one digit.<br>
                        At least one special character.</p>
                    </div>
                </div>
                <div class="modal-footer modal_footer">
                  <button type="button" class="btn modal_button_cancel" data-dismiss="modal"><span>Cancel</span></button>
                  <input class="btn modal_button_submit" type="submit" name="submitPass" value="Submit">
                </div>
                </form>
              </div>
            </div>
          </div>
        <?php } ?>
      </tbody>
    </table>

    <?php if ($totalRows_Recordset1 == 0) { ?>
      <h5 class='text-center text-dark' style='margin-top: 200px;'>No Records found</h5>
    <?php } else { ?>
      <table id="tblUser1" class="table-striped table-text table table-sm center" style="padding-top: 0; margin-top: 0">
        <thead>
          <tr>
            <th colspan="9">
              <div class="p-2 h4">Jobs added by <?php echo e($_SESSION['name']); ?></div>
            </th>
          </tr>
          <tr>
            <th>No.</th>
            <th>Name</th>
            <th class="text-center d-none d-md-table-cell">String Type</th>
            <th>Received</th>
            <th>Price</th>
            <th></th>
            <th class="text-center d-none d-md-table-cell"></th>
            <th></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1)) {
            $is_hybrid = (!empty($row_Recordset1['stringid_c']) && $row_Recordset1['stringid_c'] != $row_Recordset1['stringid_m']);
          ?>
            <tr>
              <td class="tdm">
                <a href="./viewjob.php?jobid=<?php echo e($row_Recordset1['job_id']); ?>"><?php echo e($row_Recordset1['job_id']); ?></a>
              </td>
              <td><a href="./editcust.php?custid=<?php echo e($row_Recordset1['customerid']); ?>"><span><?php echo e(substr($row_Recordset1['Name'], 0, 12)); ?></span></a></td>

              <?php if (empty($row_Recordset1['stringidm'])) { ?>
                <td class="d-none d-md-table-cell">String Unknown</td>
              <?php } elseif (!$is_hybrid) { ?>
                <td class="d-none d-md-table-cell modal-text view-string-btn"
                  data-title="Viewing Mains: <?php echo e($row_Recordset1['brandm'] . ' ' . $row_Recordset1['typem']); ?>"
                  data-length="<?php echo e($row_Recordset1['lengthm']); ?>"
                  data-number="<?php echo e($row_Recordset1['stringm_number']); ?>"
                  data-sport="<?php echo e($row_Recordset1['sportname']); ?>"
                  data-hybrid="false"
                  style="cursor:pointer;">
                  <?php echo e($row_Recordset1['brandm']) ?> &nbsp;<?php echo e($row_Recordset1['typem']); ?>
                </td>
              <?php } else { ?>
                <td class="d-none d-md-table-cell modal-text view-string-btn"
                  data-title="Viewing Mains: <?php echo e($row_Recordset1['brandm'] . ' ' . $row_Recordset1['typem']); ?>"
                  data-length="<?php echo e($row_Recordset1['lengthm']); ?>"
                  data-number="<?php echo e($row_Recordset1['stringm_number']); ?>"
                  data-title-c="Viewing Crosses: <?php echo e($row_Recordset1['brandc'] . ' ' . $row_Recordset1['typec']); ?>"
                  data-length-c="<?php echo e($row_Recordset1['lengthc']); ?>"
                  data-number-c="<?php echo e($row_Recordset1['stringc_number']); ?>"
                  data-sport="<?php echo e($row_Recordset1['sportname']); ?>"
                  data-hybrid="true"
                  style="cursor:pointer;">
                  Hybrid click for info
                </td>
              <?php } ?>

              <td class="<?php echo ($row_Recordset1['delivered'] == 0) ? 'text-danger' : ''; ?>"><?php echo e($row_Recordset1['collection_date']); ?></td>
              <td class="<?php echo ($row_Recordset1['paid'] == 0) ? 'text-danger' : ''; ?>"><?php echo e($currency) . e($row_Recordset1['price']); ?></td>

              <td><a class="modal-text fa-solid fa-pen-to-square fa-lg" href="./editjob.php?jobid=<?php echo e($row_Recordset1['job_id']); ?>"></a></td>

              <td class="d-none d-md-table-cell">
                <i class="modal-text fa-solid fa-trash-can fa-lg delete-btn"
                  data-jobid="<?php echo e($row_Recordset1['job_id']); ?>"
                  data-stringidm="<?php echo e($row_Recordset1['stringidm']); ?>"
                  data-stringidc="<?php echo e($row_Recordset1['stringidc'] ?? ''); ?>"
                  style="cursor:pointer;"></i>
              </td>
              <td><a class="fa-solid fa-tags fa-lg fa-flip-horizontal" href="./label.php?jobid=<?php echo e($row_Recordset1['job_id']); ?>"></a></td>
              <td><img class="imgsporticon m-0 p-0" src="./img/<?php echo e($row_Recordset1['image']); ?>" width="18" height="18" style="padding:0; margin:0"></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } ?>
  </div>

  <div class="container center">
    <div class="p-3 row">
      <div class="col-2"><a href="#" type="button" class="dot fa-solid fa-plus fa-2x" data-toggle="modal" data-target="#AddUser"></a></div>
      <div class="col-2">
        <h3 class="<?php echo !empty($_SESSION['message']) ? 'blinking' : 'dotb'; ?>" title="Warning Messages" data-toggle="modal" data-target="#warningModal"><strong>!</strong></h3>
      </div>
      <div class="col-2">
        <h3 class="dotbt h6 " title="Restrings for <?php echo $current_month_text; ?>"><?php echo $totalRows_Recordset6 ?></h3>
      </div>
      <div class="col-2"><a href="#" class="dotbt h6" title="Total restrings"><?php echo $totalRows_Recordset7 ?></a></div>
      <div class="col-2"><a href="./jobs-unpaid.php" class="dotbt h6" title="Amount Owed"><?php echo e($currency) . e($sum_owed); ?></a></div>
      <div class="col-2"><a href="#" class="dotbtt h7" title="Total Income"><small><?php echo e($currency) . e($sum); ?></small></a></div>
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
          <button type="button" class="btn modal_button_cancel" data-dismiss="modal"><span>Close</span></button>
        </div>
      </div>
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
                <a class="btn modal_button_cancel" href="./account_home.php">Cancel</a>
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

  <div class="modal fade text-dark" id="AddUser">
    <div class="modal-dialog">
      <div class="modal-content border radius">
        <div class="modal-header modal_header">
          <h5 class="modal-title">You are adding a new user</h5>
          <button class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <form method="post" action="site-users-db.php">
          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
          <div class="modal-body modal_body">
            <div class="form-group">
              <label for="name">User Name</label>
              <input class="form-control" id="name" type="text" placeholder="Enter Username" name="username">
              <label class="pt-3" for="email">Email Address</label>
              <input class="form-control" id="email" placeholder="Enter Email" name="email">
            </div>
            <input type="hidden" name="active" value="1">
            <label for="level">Access Level</label>
            <select class="form-control" id="level" name="level" style='font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12pt; width:80%'>
              <option value="1">1 (Super User)</option>
              <option value="2">2 (Add jobs only)</option>
            </select>
          </div>
          <div class="modal-footer modal_footer">
            <button type="button" class="btn modal_button_cancel" data-dismiss="modal"><span>Cancel</span></button>
            <input class="btn modal_button_submit" type="submit" name="submitAdd" value="Submit">
          </div>
        </form>
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

    // Handle Global Delete Modal
    $(document).on("click", ".delete-btn", function() {
      $('#delModalTitle').text('You are about to delete job "' + $(this).data('jobid') + '"');
      $('#delJobId').val($(this).data('jobid'));
      $('#delStringIdm').val($(this).data('stringidm'));
      $('#delStringIdc').val($(this).data('stringidc'));
      $('#globalDelModal').modal('show');
    });

    // Handle Global String View Modal
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

    // Initialize DataTables
    jQuery(document).ready(function($) {
      if ($('#tblUser').length) {
        $('#tblUser').DataTable({
          stateSave: true,
          searching: false,
          paging: false,
          language: {
            'search': '',
            'searchPlaceholder': 'Search:'
          },
          autoWidth: false,
          columnDefs: [{
            targets: [0, 1, 2, 3, 4, 5],
            className: "dt-head-center",
            orderable: false
          }],
          order: [
            [0, 'desc']
          ]
        });
      }

      if ($('#tblUser1').length) {
        $('#tblUser1').DataTable({
          stateSave: true,
          pagingType: "simple_numbers",
          language: {
            'search': '',
            'searchPlaceholder': 'Search Jobs:',
            "sLengthMenu": "",
            "info": "",
            "infoEmpty": ""
          },
          pageLength: 8,
          autoWidth: false,
          columnDefs: [{
              targets: [0, 1, 2, 3, 4, 5, 6, 7, 8],
              className: "dt-head-center"
            },
            {
              targets: [5, 6, 7, 8],
              orderable: false
            }
          ],
          order: [
            [0, 'desc']
          ]
        });
      }
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