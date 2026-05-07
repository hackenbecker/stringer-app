<?php
if (((filesize("./Connections/wcba.php")) == 0) or (!file_exists("./Connections/wcba.php"))) {
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

// SECURITY Helper: Escape output to prevent XSS
function e($string)
{
  return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

$stringid = isset($_GET['stringid']) ? (int)$_GET['stringid'] : 0;
$sportid = isset($_GET['sportid']) ? (int)$_GET['sportid'] : 0;
$current_month_numeric = date("m");
$current_year = date("Y");
$current_month_text = date("M");

// --- Recordset 1: Main Jobs Query (Prepared) ---
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
    rackets.manuf as manuf, rackets.model as model, rackets.pattern as pattern, rackets.sport as sportid,
    all_string.brand as brandm, all_string.type as typem, all_string.notes as notes_stock,
    all_stringc.brand as brandc, all_stringc.type as typec, all_stringc.notes as notesc_stock,
    string.note as notes_string, string.string_number as stringm_number, string.stringid as stringid_m,
    reel_lengthsm.length as lengthm, reel_lengthsc.length as lengthc,
    stringc.note as notesc_string, stringc.string_number as stringc_number, stringc.stringid as stringid_c
    FROM stringjobs 
    LEFT JOIN customer ON stringjobs.customerid = customer.cust_ID
    LEFT JOIN string ON stringjobs.stringid = string.stringid 
    LEFT JOIN string AS stringc ON stringjobs.stringidc = stringc.stringid
    LEFT JOIN all_string ON string.stock_id = all_string.string_id
    LEFT JOIN all_string AS all_stringc ON stringc.stock_id = all_stringc.string_id
    LEFT JOIN reel_lengths AS reel_lengthsm ON reel_lengthsm.reel_length_id = string.lengthid
    LEFT JOIN reel_lengths AS reel_lengthsc ON reel_lengthsc.reel_length_id = stringc.lengthid
    LEFT JOIN rackets ON stringjobs.racketid = rackets.racketid 
    LEFT JOIN sport ON rackets.sport = sport.sportid
    WHERE (stringjobs.stringid = ? OR stringjobs.stringidc = ?) AND rackets.sport = ?
    ORDER BY job_id DESC";
$stmt1 = mysqli_prepare($conn, $query_Recordset1);
mysqli_stmt_bind_param($stmt1, "iii", $stringid, $stringid, $sportid);
mysqli_stmt_execute($stmt1);
$Recordset1 = mysqli_stmt_get_result($stmt1);
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

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />

  <title>SDBA - String Jobs</title>
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
    <p class="fxdtextb"><strong>All</strong> Restrings for String ID: <?php echo e($stringid); ?></p>

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
          <?php while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1)) { ?>
            <tr>
              <td class="tdm"><a class="modal-text" href="./viewjob.php?jobid=<?php echo e($row_Recordset1['job_id']); ?>"><?php echo e($row_Recordset1['job_id']); ?></a></td>
              <td><a class="modal-text" href="./editcust.php?custid=<?php echo e($row_Recordset1['customerid']); ?>"><span><?php echo e(substr($row_Recordset1['Name'], 0, 12)); ?></span></a></td>

              <td class="text-center d-none d-md-table-cell modal-text" data-toggle="modal" data-target="#StringViewModal<?php echo e($row_Recordset1['job_id']); ?>" style="cursor:pointer;">
                <?php
                if ($row_Recordset1['stringidm'] == $row_Recordset1['stringidc'] || $row_Recordset1['stringidc'] == 0) {
                  echo e($row_Recordset1['brandm']) . " " . e($row_Recordset1['typem']);
                } else {
                  echo "Hybrid (Click Info)";
                }
                ?>
              </td>

              <div class="modal fade text-dark" id="StringViewModal<?php echo e($row_Recordset1['job_id']); ?>">
                <div class="modal-dialog">
                  <div class="modal-content border radius">
                    <div class="modal-header modal_header">
                      <h5 class="modal-title">Mains: <?php echo e($row_Recordset1['brandm']) . " " . e($row_Recordset1['typem']); ?></h5>
                      <button class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body modal_body">
                      <p>Start Length: <?php echo e($row_Recordset1['lengthm']) . e($units ?? ''); ?></p>
                      <p>Jobs from Reel: <?php echo e($row_Recordset1['stringm_number']); ?></p>
                      <?php if ($row_Recordset1['stringidc'] != 0 && $row_Recordset1['stringidc'] != $row_Recordset1['stringidm']) { ?>
                        <hr>
                        <h5>Crosses: <?php echo e($row_Recordset1['brandc']) . " " . e($row_Recordset1['typec']); ?></h5>
                        <p>Start Length: <?php echo e($row_Recordset1['lengthc']) . e($units ?? ''); ?></p>
                        <p>Jobs from Reel: <?php echo e($row_Recordset1['stringc_number']); ?></p>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>

              <td><?php echo e($row_Recordset1['collection_date']); ?></td>
              <td>
                <form method="post" action="./db-update.php">
                  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                  <input onChange="this.form.submit()" type="checkbox" name="deliveredupdate" value="1" <?php if ($row_Recordset1['delivered'] == 1) echo "checked"; ?>>
                  <input type="hidden" name="jobiddeliveredupdate" value="<?php echo e($row_Recordset1['job_id']); ?>">
                </form>
              </td>
              <td><?php echo $currency . e($row_Recordset1['price']); ?></td>
              <td>
                <form method="post" action="./db-update.php">
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
                  <input type="hidden" name="stringid" value="<?php echo e($row_Recordset1['stringidm']); ?>">
                  <button type="submit" name="submitadd" class="button-colours-rollover" style="background:transparent; border:none; padding:0"><i class="fa-solid fa-copy fa-lg"></i></button>
                </form>
              </td>
              <td class="d-none d-md-table-cell"><i class="fa-solid fa-trash-can fa-lg modal-text" data-toggle="modal" data-target="#delModal<?php echo e($row_Recordset1['job_id']); ?>" style="cursor:pointer;"></i></td>
              <td><a class="fa-solid fa-tags fa-lg fa-flip-horizontal modal-text" href="./label.php?jobid=<?php echo e($row_Recordset1['job_id']); ?>"></a></td>
              <td class="text-center d-none d-md-table-cell"><img class="imgsporticon m-0 p-0" src="./img/<?php echo e($row_Recordset1['image'] ?? ''); ?>" width="18" height="18" style="padding:0; margin:0"></td>
            </tr>

            <div class="modal fade text-dark" id="delModal<?php echo e($row_Recordset1['job_id']); ?>">
              <div class="modal-dialog">
                <div class="modal-content border radius">
                  <form method="post" action="./db-update.php">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="modal-header modal_header">
                      <h5 class="modal-title">Confirm Delete Job #<?php echo e($row_Recordset1['job_id']); ?></h5>
                      <button class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body modal_body">
                      <div>Please confirm or cancel!</div>
                      <div style="padding-bottom:5px;"></div>
                    </div>
                    <div class="modal-footer modal_footer">
                      <input type="hidden" name="refdel" value="<?php echo e($row_Recordset1['job_id']); ?>">
                      <button type="button" class="btn modal_button_cancel" data-dismiss="modal">Cancel</button>
                      <input type="submit" name="submitdelete" class="btn modal_button_submit" value="Delete">
                    </div>
                  </form>
                </div>
              </div>
            </div>

          <?php } ?>
        </tbody>
      </table>
    <?php } ?>
  </div>

  <div class="container center">
    <div class="p-3 row">
      <div class="col-2"><a href="./addjob.php" class="dot fa-solid fa-plus fa-2x"></a></div>
      <div class="col-2">
        <h3 class="<?php echo !empty($_SESSION['message']) ? 'blinking' : 'dotb'; ?>" data-toggle="modal" data-target="#warningModal"><strong>!</strong></h3>
      </div>
      <div class="col-2">
        <div class="dotbt h6"><span><?php echo $totalRows_Recordset6 ?></span></div>
      </div>
      <div class="col-2">
        <div class="dotbt h6"><span><?php echo $totalRows_Recordset7 ?></span></div>
      </div>
      <div class="col-2"><a href="./jobs-unpaid.php" class="dotbt h6"><?php echo $currency . e($sum_owed); ?></a></div>
      <div class="col-2">
        <div class="dotbtt h7"><small><?php echo $currency . e($sum); ?></small></div>
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
                <a class="btn modal_button_cancel" href="#">Cancel</a>
              </div>
              <div class="col-4">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?stringid=<?php echo e($stringid); ?>&sportid=<?php echo e($sportid); ?>">
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
  <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

  <script>
    $('#year').text(new Date().getFullYear());
    $('body').scrollspy({
      target: '#main-nav'
    });

    $(document).ready(function() {
      $('#tblUser').DataTable({
        stateSave: true,
        pageLength: 15,
        language: {
          'search': '',
          'searchPlaceholder': 'Search:',
          "sLengthMenu": "",
          "info": "",
          "infoEmpty": ""
        },
        order: [
          [0, 'desc']
        ],
        columnDefs: [{
          targets: 'no-sort',
          orderable: false
        }]
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