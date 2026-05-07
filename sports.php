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
if ($_SESSION['level'] != 1) {
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

// Fetch Sports Data
$query_Recordset2 = "SELECT * FROM sport ORDER BY sportname ASC";
$Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);

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
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />

  <title>SDBA - Sports</title>
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

  <section>
    <div class="home-section diva">
      <div class="subheader"></div>
      <p class="fxdtextb"><strong>ALL</strong> Sports</p>

      <div class="container mt-3 pb-3 px-3 firstparavp">
        <a class="btn button button-colours h5" href="./settings.php">Back to settings</a>
      </div>

      <?php if ($totalRows_Recordset2 == 0) {
        echo "<h5 class='text-center text-dark' style='margin-top: 200px;'>No Records found</h5> ";
      } else { ?>
        <table id="tblUser" class="table-striped table-text table table-sm center">
          <thead>
            <tr>
              <th class="text-center">Sport ID.</th>
              <th class="text-center">Name</th>
              <th class="text-center"></th>
              <th class="text-center"></th>
              <th class="text-center"></th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row_Recordset2 = mysqli_fetch_assoc($Recordset2)) { ?>
              <tr>
                <td class="text-center pl-3"><?php echo e($row_Recordset2['sportid']); ?></td>
                <td class="text-center pl-3"><?php echo e($row_Recordset2['sportname']); ?></td>
                <td class="text-center"><i class="fa-solid fa-pen-to-square modal-text" data-toggle="modal" data-target="#EditSport<?php echo e($row_Recordset2['sportid']); ?>" style="cursor:pointer;"></i></td>
                <td class="text-center"><i class="fa-solid fa-trash-can modal-text" data-toggle="modal" data-target="#delModal<?php echo e($row_Recordset2['sportid']); ?>" style="cursor:pointer;"></i></td>
                <td class="text-center"><img class="imgsporticon m-0 p-0" src="./img/<?php echo e($row_Recordset2['image'] ?? ''); ?>" width="18" height="18" style="padding:0; margin:0"></td>
              </tr>

              <div class="modal fade text-dark" id="EditSport<?php echo e($row_Recordset2['sportid']); ?>">
                <div class="modal-dialog">
                  <div class="modal-content border radius">
                    <div class="modal-header modal_header">
                      <h5 class="modal-title">Edit Sport "<?php echo e($row_Recordset2['sportname']); ?>"</h5>
                      <button class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form method="post" action="./db-update.php" enctype="multipart/form-data">
                      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                      <div class="modal-body modal_body">
                        <div class="form-group">
                          <label for="name">Sport Name</label>
                          <input class="form-control" type="text" name="sportname" value="<?php echo e($row_Recordset2['sportname']); ?>" required>
                        </div>
                        <input type="hidden" name="refedit" value="<?php echo e($row_Recordset2['sportid']); ?>">
                        <div class="mt-3 mb-3 custom-file">
                          <input class="custom-file-input" name="image" type="file" accept="image/*">
                          <label class="custom-file-label" for="image">Add sport icon</label>
                        </div>
                      </div>
                      <div class="modal-footer modal_footer">
                        <button type="button" class="btn modal_button_cancel" data-dismiss="modal"><span>Cancel</span></button>
                        <input class="btn modal_button_submit" type="submit" name="submiteditsport" value="Submit">
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <div class="modal fade text-dark" id="delModal<?php echo e($row_Recordset2['sportid']); ?>">
                <div class="modal-dialog">
                  <div class="modal-content border radius">
                    <div class="modal-header modal_header">
                      <h5 class="modal-title">Delete sport "<?php echo e($row_Recordset2['sportname']); ?>"</h5>
                      <button class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body modal_body">
                      <form method="post" action="./db-update.php">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div>Please confirm or cancel!</div>
                        <div style="padding-bottom:5px;"></div>
                        <input type="hidden" name="refdelsport" class="txtField" value="<?php echo e($row_Recordset2['sportid']); ?>">
                    </div>
                    <div class="modal-footer modal_footer">
                      <button type="button" class="btn modal_button_cancel" data-dismiss="modal"><span>Cancel</span></button>
                      <input class="btn modal_button_submit" type="submit" name="submit" value="Delete">
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?>
          </tbody>
        </table>
      <?php } ?>
    </div>
  </section>

  <div class="container center">
    <div class="p-3 row">
      <div class="col-2"><a href="#" type="button" class="dot fa-solid fa-plus fa-2x" data-toggle="modal" data-target="#AddSport"></a></div>
      <div class="col-2">
        <h3 class="<?php echo !empty($_SESSION['message']) ? 'blinking' : 'dotb'; ?>" title="Warning Messages" data-toggle="modal" data-target="#warningModal"><strong>!</strong></h3>
      </div>
      <div class="col-2">
        <h3 class="dotbt h6 " title="Restrings for <?php echo $current_month_text; ?>"><?php echo $totalRows_Recordset6 ?></h3>
      </div>
      <div class="col-2"><a href="#" class="dotbt h6" title="Total restrings"><?php echo $totalRows_Recordset7 ?></a></div>
      <div class="col-2"><a href="./jobs-unpaid.php" class="dotbt h6" title="Amount Owed"><?php echo $currency . e($sum_owed); ?></a></div>
      <div class="col-2"><a href="#" class="dotbtt h7" title="Total Income"><small><?php echo $currency . e($sum); ?></small></a></div>
    </div>
  </div>

  <div class="modal fade text-dark" id="AddSport">
    <div class="modal-dialog">
      <div class="modal-content border radius">
        <div class="modal-header modal_header">
          <h5 class="modal-title">You are adding a new sport</h5>
          <button class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <form method="post" action="./db-update.php" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
          <div class="modal-body modal_body">
            <div class="form-group">
              <label for="name">Sport Name</label>
              <input class="form-control" type="text" placeholder="Enter Sport Name" name="sportname" required>
            </div>
            <div class="mt-3 mb-3 custom-file">
              <input class="custom-file-input" name="image" type="file" accept="image/*">
              <label class="custom-file-label" for="image">Add sport icon</label>
            </div>
          </div>
          <div class="modal-footer modal_footer">
            <button type="button" class="btn modal_button_cancel" data-dismiss="modal"><span>Cancel</span></button>
            <input class="btn modal_button_submit" type="submit" name="submitaddsport" value="Submit">
          </div>
        </form>
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
                <a class="btn modal_button_cancel" href="./sports.php">Cancel</a>
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
            targets: [0, 1, 2, 3, 4],
            className: "dt-head-center"
          },
          {
            targets: [2, 3, 4],
            orderable: false
          }
        ],
        order: [
          [1, 'asc']
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

    $(".custom-file-input").on("change", function() {
      var fileName = $(this).val().split("\\").pop();
      $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

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