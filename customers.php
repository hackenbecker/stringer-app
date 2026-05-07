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

$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");

// Recordset 2: Fetch Customers and their preferences
// FIX: Explicitly alias Name and order by customer.Name to prevent ambiguity
$query_Recordset2 = "SELECT customer.*, customer.Name AS Name, rackets.manuf, rackets.model, all_string.brand, all_string.type, all_string.notes as string_notes 
                     FROM customer 
                     LEFT JOIN rackets ON customer.racketid = rackets.racketid 
                     LEFT JOIN all_string ON customer.pref_string = all_string.string_id 
                     ORDER BY customer.Name ASC";
$Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);

// PERFORMANCE FIX: Solve N+1 Query Problem for Job Counts
$jobCounts = [];
$query_jobCounts = "SELECT customerid, COUNT(job_id) as total_jobs FROM stringjobs GROUP BY customerid";
$result_jobCounts = mysqli_query($conn, $query_jobCounts);
while ($row = mysqli_fetch_assoc($result_jobCounts)) {
  $jobCounts[$row['customerid']] = $row['total_jobs'];
}

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

// Settings
$query_Recordset15 = "SELECT value FROM settings WHERE id = '21';";
$Recordset15 = mysqli_query($conn, $query_Recordset15) or die(mysqli_error($conn));
$weight = mysqli_fetch_assoc($Recordset15)['value'] ?? 'lbs';

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />

  <title>SDBA - Customers</title>
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
    <p class="fxdtextb"><strong>All</strong> Customers</p>

    <?php if ($totalRows_Recordset2 == 0) { ?>
      <h5 class='text-center text-dark' style='margin-top: 200px;'>No Records found</h5>
    <?php } else { ?>
      <table id="tblUser" class="table-striped table-text table table-sm center">
        <thead>
          <tr>
            <th class="text-center">Name</th>
            <th class="text-center d-none d-md-table-cell">Mobile</th>
            <th class="text-center d-none d-md-table-cell">Email</th>
            <th class="text-center">Jobs</th>
            <th></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row_Recordset2 = mysqli_fetch_assoc($Recordset2)) {
            $jobCount = $jobCounts[$row_Recordset2['cust_ID']] ?? 0;
          ?>
            <tr>
              <td class="text-center view-cust-btn modal-text" style="cursor:pointer;"
                data-custid="<?php echo e($row_Recordset2['cust_ID'] ?? ''); ?>"
                data-name="<?php echo e($row_Recordset2['Name'] ?? $row_Recordset2['name'] ?? ''); ?>"
                data-mobile="<?php echo e($row_Recordset2['Mobile'] ?? $row_Recordset2['mobile'] ?? ''); ?>"
                data-email="<?php echo e($row_Recordset2['Email'] ?? $row_Recordset2['email'] ?? ''); ?>"
                data-discount="<?php echo e($row_Recordset2['discount'] ?? ''); ?>"
                data-racket="<?php echo e(trim(($row_Recordset2['manuf'] ?? '') . ' ' . ($row_Recordset2['model'] ?? ''))); ?>"
                data-string="<?php echo e(trim(($row_Recordset2['brand'] ?? '') . ' ' . ($row_Recordset2['type'] ?? '') . ' ' . ($row_Recordset2['string_notes'] ?? ''))); ?>"
                data-tension="<?php echo e($row_Recordset2['tension'] ?? ''); ?>"
                data-pretension="<?php echo e($row_Recordset2['pre_tension'] ?? $row_Recordset2['prestretch'] ?? ''); ?>"
                data-notes="<?php echo e($row_Recordset2['Notes'] ?? $row_Recordset2['notes'] ?? ''); ?>">
                <?php echo e($row_Recordset2['Name'] ?? $row_Recordset2['name'] ?? ''); ?>
              </td>
              <td class="text-center d-none d-md-table-cell"><?php echo e($row_Recordset2['Mobile'] ?? ''); ?></td>
              <td class="text-center d-none d-md-table-cell"><?php echo e($row_Recordset2['Email'] ?? ''); ?></td>
              <td class="text-center"><?php echo $jobCount; ?></td>
              <td class="text-center"><a class="fa-solid fa-pen-to-square modal-text" href="./editcust.php?custid=<?php echo e($row_Recordset2['cust_ID'] ?? ''); ?>"></a></td>
              <td class="text-center">
                <i class="modal-text fa-solid fa-trash-can delete-cust-btn" style="cursor:pointer;"
                  data-custid="<?php echo e($row_Recordset2['cust_ID'] ?? ''); ?>"
                  data-name="<?php echo e($row_Recordset2['Name'] ?? $row_Recordset2['name'] ?? ''); ?>"></i>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } ?>
  </div>

  <div class="container center">
    <div class="p-3 row">
      <div class="col-2"><a href="./addcustomer.php" type="button" class="dot fa-solid fa-plus fa-2x"></a></div>
      <div class="col-2">
        <h3 class="<?php echo !empty($_SESSION['message']) ? 'blinking' : 'dotb'; ?>" title="Warning Messages" data-toggle="modal" data-target="#warningModal"><strong>!</strong></h3>
      </div>
      <div class="col-2">
        <h3 class="dotbt h6 " title="Restrings for <?php echo $current_month_text; ?>"><?php echo $totalRows_Recordset6 ?></h3>
      </div>
      <div class="col-2"><a href="string-jobs.php" class="dotbt h6" title="Total restrings"><?php echo $totalRows_Recordset7 ?></a></div>
      <div class="col-2"><a href="./jobs-unpaid.php" class="dotbt h6" title="Amount Owed"><?php echo $currency . e($sum_owed); ?></a></div>
      <div class="col-2">
        <h7 class="dotbtt h7" title="Total Income"><small><?php echo $currency . e($sum); ?></small></h7>
      </div>
    </div>
  </div>

  <div class="modal fade" id="globalCustViewModal">
    <div class="modal-dialog">
      <div class="modal-content border radius">
        <div class="modal-header modal_header">
          <h5 class="modal-title text-white" id="viewTitle">Viewing Customer</h5>
          <button class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body modal_body">
          <div id="v_name_box" class="mt-2">
            <p class="mb-0" style="font-size:12px">Name:</p><span class="h6" id="v_name"></span>
          </div>
          <div id="v_mobile_box" class="mt-2">
            <p class="mb-0" style="font-size:12px">Mobile:</p><span class="h6" id="v_mobile"></span>
          </div>
          <div id="v_email_box" class="mt-2">
            <p class="mb-0" style="font-size:12px">Email:</p><a id="v_email_link" href="#"><span class="h6" id="v_email"></span></a>
          </div>

          <div class="mt-2">
            <p class="mb-0" style="font-size:12px">Discount:</p><span class="h6"><span id="v_discount"></span>%</span>
          </div>
          <hr>
          <div id="v_racket_box">
            <p class="mb-0" style="font-size:12px">Preferred Racket:</p><span class="h6" id="v_racket"></span>
          </div>
          <div id="v_string_box" class="mt-2">
            <p class="mb-0" style="font-size:12px">Preferred String:</p><span class="h6" id="v_string"></span>
          </div>
          <div id="v_tension_box" class="mt-2">
            <p class="mb-0" style="font-size:12px">Preferred Tension:</p><span class="h6"><span id="v_tension"></span> <?php echo e($weight); ?></span>
          </div>
          <div id="v_preten_box" class="mt-2">
            <p class="mb-0" style="font-size:12px">Pre-Tension:</p><span class="h6"><span id="v_preten"></span>%</span>
          </div>

          <hr id="v_hr_notes">
          <div id="v_notes_box" class="mt-2">
            <p class="mb-0" style="font-size:12px">Notes:</p><span class="h6" id="v_notes"></span>
          </div>

        </div>
        <div class="modal-footer modal_footer">
          <a class="btn modal_button_submit" id="viewEditBtn" href="#">Edit</a>
          <button class="btn modal_button_cancel" data-dismiss="modal"><span>Close</span></button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade text-dark" id="globalCustDelModal">
    <div class="modal-dialog">
      <div class="modal-content border radius">
        <?php if ($_SESSION['level'] == 1) { ?>
          <div class="modal-header modal_header">
            <h5 class="modal-title" id="delCustTitle">You are about to delete Customer</h5>
            <button class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body modal_body">
            <form method="post" action="./db-update.php">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
              <div>Please confirm or cancel!</div>
              <div style="padding-bottom:5px;"></div>
              <input type="hidden" name="refdelcust" id="delCustId" value="">
          </div>
          <div class="modal-footer modal_footer">
            <button type="button" class="btn modal_button_cancel" data-dismiss="modal"><span>Cancel</span></button>
            <input class="btn modal_button_submit" type="submit" name="submit" value="Delete">
            </form>
          </div>
        <?php } else { ?>
          <div class="modal-header modal_header">
            <h5 class="modal-title">You do not have permission</h5>
            <button class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body modal_body text-center">
            <p>Only administrators can delete customers.</p>
          </div>
        <?php } ?>
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
                <a class="btn modal_button_cancel" href="./customers.php">Cancel</a>
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

  <script>
    $('#year').text(new Date().getFullYear());
    $('body').scrollspy({
      target: '#main-nav'
    });

    // Global Customer View Population
    $(document).on("click", ".view-cust-btn", function() {
      $('#viewTitle').text("Viewing " + $(this).data('name'));
      $('#v_name').text($(this).data('name'));
      $('#viewEditBtn').attr('href', './editcust.php?custid=' + $(this).data('custid'));
      $('#v_discount').text($(this).data('discount') || '0');

      var mob = $(this).data('mobile');
      if (mob) {
        $('#v_mobile_box').show();
        $('#v_mobile').text(mob);
      } else {
        $('#v_mobile_box').hide();
      }

      var em = $(this).data('email');
      if (em) {
        $('#v_email_box').show();
        $('#v_email').text(em);
        $('#v_email_link').attr('href', 'mailto:' + em);
      } else {
        $('#v_email_box').hide();
      }

      var rac = $(this).data('racket');
      if (rac) {
        $('#v_racket_box').show();
        $('#v_racket').text(rac);
      } else {
        $('#v_racket_box').hide();
      }

      var str = $(this).data('string');
      if (str) {
        $('#v_string_box').show();
        $('#v_string').text(str);
      } else {
        $('#v_string_box').hide();
      }

      var ten = $(this).data('tension');
      if (ten) {
        $('#v_tension_box').show();
        $('#v_tension').text(ten);
      } else {
        $('#v_tension_box').hide();
      }

      var pre = $(this).data('pretension');
      if (pre) {
        $('#v_preten_box').show();
        $('#v_preten').text(pre);
      } else {
        $('#v_preten_box').hide();
      }

      var not = $(this).data('notes');
      if (not) {
        $('#v_notes_box').show();
        $('#v_hr_notes').show();
        $('#v_notes').text(not);
      } else {
        $('#v_notes_box').hide();
        $('#v_hr_notes').hide();
      }

      $('#globalCustViewModal').modal('show');
    });

    // Global Delete Cust Population
    $(document).on("click", ".delete-cust-btn", function() {
      $('#delCustTitle').text('You are about to delete "' + $(this).data('name') + '"');
      $('#delCustId').val($(this).data('custid'));
      $('#globalCustDelModal').modal('show');
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
          "infoEmpty": ""
        },
        pageLength: 15,
        autoWidth: false,
        columnDefs: [{
            targets: [0, 1, 2, 3, 4, 5],
            className: "dt-head-center"
          },
          {
            targets: [4, 5],
            orderable: false
          }
        ],
        order: [
          [0, 'asc']
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