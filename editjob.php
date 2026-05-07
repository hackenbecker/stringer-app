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

$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");

// SECURITY Helper: Escape output to prevent XSS
function e($string)
{
  return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// SECURITY: Safely cast the job ID to an integer to prevent SQL Injection
$jobid = 0;
if (isset($_POST['submitclearmessage'])) {
  unset($_SESSION['message']);
  $jobid = (int)($_POST['jobid'] ?? 0);
} elseif (isset($_GET['jobid'])) {
  $jobid = (int)$_GET['jobid'];
}

// Fetch Job Data
$query_Recordset2 = "SELECT *,
    all_stringm.notes as notesm_stock, all_stringc.notes as notesc_stock,
    stringm.string_number as stringm_number, stringm.note as notesm_string,
    stringc.note as notesc_string, stringc.string_number as stringc_number,
    all_stringm.brand as brandm, all_stringc.type as typem,
    all_stringc.brand as brandc, all_stringc.type as typec,
    stringm.stringid as stringid_m, stringc.stringid as stringid_c,
    stringm.empty as emptym, stringc.empty as emptyc,
    sportm.sportid as sportm, sportc.sportid as sportc,
    stringjobs.tension as atension, stringjobs.tensionc as atensionc 
    FROM stringjobs
    LEFT JOIN customer ON customerid = cust_ID 
    LEFT JOIN rackets ON stringjobs.racketid = rackets.racketid 
    LEFT JOIN string AS stringm ON stringjobs.stringid = stringm.stringid 
    LEFT JOIN string AS stringc ON stringjobs.stringidc = stringc.stringid
    LEFT JOIN all_string AS all_stringm ON all_stringm.string_id = stringm.stock_id
    LEFT JOIN all_string AS all_stringc ON all_stringc.string_id = stringc.stock_id
    LEFT JOIN sport AS sportm ON all_stringm.sportid = sportm.sportid
    LEFT JOIN sport AS sportc ON all_stringc.sportid = sportc.sportid
    WHERE job_id = '$jobid'";
$Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));
$row_Recordset2 = mysqli_fetch_assoc($Recordset2);

// Extract sport for filtering dropdowns
$job_sportid = (int)($row_Recordset2['sport'] ?? $row_Recordset2['sportid'] ?? $row_Recordset2['sportm'] ?? 0);

// Fetch Customers
$query_Recordset3 = "SELECT cust_ID, Name FROM customer ORDER BY Name ASC;";
$Recordset3 = mysqli_query($conn, $query_Recordset3) or die(mysqli_error($conn));

// Fetch Rackets
$query_Recordset4 = "SELECT racketid, manuf, model FROM rackets WHERE sport = '$job_sportid' ORDER BY manuf ASC;";
$Recordset4 = mysqli_query($conn, $query_Recordset4) or die(mysqli_error($conn));

// Fetch Strings (Excluding "String Generic")
$query_Recordset7 = "SELECT string.stringid, all_string.brand, all_string.type, string.note 
                     FROM string 
                     LEFT JOIN all_string ON string.stock_id = all_string.string_id 
                     WHERE sportid = '$job_sportid' AND empty = '0' AND brand != 'String Generic' AND type != 'String Generic' 
                     ORDER BY string.stringid ASC;";
$Recordset7 = mysqli_query($conn, $query_Recordset7) or die(mysqli_error($conn));
$totalRows_Recordset7 = mysqli_num_rows($Recordset7);

// PERFORMANCE: Optimized Counting Queries
$query_Recordset9 = "SELECT COUNT(job_id) as total FROM stringjobs";
$totalRows_Recordset9 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset9))['total'] ?? 0;

$query_Recordset10 = "SELECT COUNT(job_id) as total FROM stringjobs WHERE collection_date LIKE '___" . $current_month_numeric . "/" . $current_year . "%'";
$totalRows_Recordset10 = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset10))['total'] ?? 0;

$query_Recordset11 = "SELECT SUM(price) as SUM from stringjobs";
$sum = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset11))['SUM'] ?? 0;

$query_Recordset12 = "SELECT SUM(price) as SUM from stringjobs WHERE paid != '1'";
$sum_owed = mysqli_fetch_assoc(mysqli_query($conn, $query_Recordset12))['SUM'] ?? 0;
$_SESSION['sum_owed'] = $sum_owed;

$imageid = (int)($row_Recordset2['imageid'] ?? 0);

// Settings
$query_Recordset15 = "SELECT value FROM settings WHERE id = '21';";
$Recordset15 = mysqli_query($conn, $query_Recordset15) or die(mysqli_error($conn));
$weight = mysqli_fetch_assoc($Recordset15)['value'] ?? 'lbs';

if ($weight == "kg") {
  $maxtension = 35;
  $row_Recordset2['atension'] = (round(($row_Recordset2['atension'] ?? 0) * 0.45359237, 1));
  $row_Recordset2['atensionc'] = (round(($row_Recordset2['atensionc'] ?? 0) * 0.45359237, 1));
} else {
  $maxtension = 70;
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
  <link rel="stylesheet" href="./css/bootstrap-datetimepicker.min.css" type="text/css" media="all" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./css/style.css">

  <title>SDBA - Edit Job</title>
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
    <div class="subheader"> </div>
    <p class="fxdtextb"><strong>Edit</strong> Restring: <?php echo e($jobid); ?></p>

    <div class="container my-1 firstparaaltej">
      <div class="container my-1 pb-3 px-1 firstparaej">
        <div class="container px-1 pt-3 form-text">
          <div class="card cardvp" style="margin-top: 75px;">
            <div class="card-body">
              <form method="post" action="./db-update.php" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="row">
                  <div class="col-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="gripreqd" value="1" id="grip" <?php if (($row_Recordset2['grip_required'] ?? 0) == 1) echo " checked"; ?>>
                      <label class="form-check-label form-text" for="grip">Grip</label>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="freerestring" value="1" id="freerestring" <?php if (($row_Recordset2['free_job'] ?? 0) == 1) echo " checked"; ?>>
                      <label class="form-check-label form-text" for="freerestring">Free</label>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="paid" value="1" id="paid" <?php if (($row_Recordset2['paid'] ?? 0) == 1) echo " checked"; ?>>
                      <label class="form-check-label form-text" for="paid">Paid</label>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="delivered" value="1" id="delivered" <?php if (($row_Recordset2['delivered'] ?? 0) == 1) echo " checked"; ?>>
                      <label class="form-check-label form-text" for="delivered">Deliv</label>
                    </div>
                  </div>
                </div>
            </div>
          </div>
        </div>

        <div class="container mt-3 ">
          <div class="row">
            <div class="col-6">
              <input class="form-check-input" type="hidden" name="editflag" value="1">
              <input class="btn button-colours" type="submit" name="submitEditjob" value="Submit">
              <input type="hidden" name="weight" value="<?php echo e($weight); ?>">
            </div>
            <div class="col-6">
              <a class="btn button-colours-alt float-right" href="./string-jobs.php">Cancel</a>
            </div>
          </div>
        </div>

        <div class="container px-1 pt-3 form-text">
          <div class="card cardvp mt-3">
            <div class="card-body">
              <label class="form-text">Customer</label>
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <select class="form-control searchable-dropdown" style="width:100%" name="customerid">
                      <option value="">Please select</option>
                      <?php while ($row_Recordset3 = mysqli_fetch_assoc($Recordset3)) {
                        $selected = ($row_Recordset3['cust_ID'] == $row_Recordset2['customerid']) ? "selected" : "";
                      ?>
                        <option value="<?php echo e($row_Recordset3['cust_ID']); ?>" <?php echo $selected; ?>>
                          <?php echo e($row_Recordset3['Name']); ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>

              <?php if (($row_Recordset2['emptym'] ?? 0) == 1) { ?>
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label class="mt-3 form-text">String Mains (This stock string is set to "Reel Finished")</label>
                      <input type="text" name="empty" placeholder="<?php echo e($row_Recordset2['brandm']) . " " . e($row_Recordset2['typem']) . " " . e($row_Recordset2['notesm_string']); ?>" class="form-control" readonly />
                    </div>
                  </div>
                </div>
              <?php } else { ?>
                <label class="mt-3">String Mains</label>
                <div class="form-group">
                  <div class="row">
                    <div class="col-12">
                      <select class="form-control searchable-dropdown" style="width:100%" name="stringid">
                        <option value="0">Please select</option>
                        <?php if ($totalRows_Recordset7 > 0) {
                          while ($row_Recordset7 = mysqli_fetch_assoc($Recordset7)) {
                            $selected = ($row_Recordset7['stringid'] == $row_Recordset2['stringid_m']) ? "selected" : "";
                        ?>
                            <option value="<?php echo e($row_Recordset7['stringid']); ?>" <?php echo $selected; ?>>
                              <?php echo e($row_Recordset7['brand']) . " " . e($row_Recordset7['type']) . " " . e($row_Recordset7['note']); ?>
                            </option>
                        <?php }
                        } ?>
                      </select>
                      <?php mysqli_data_seek($Recordset7, 0); ?>
                    </div>
                  </div>
                </div>
              <?php } ?>

              <?php if (($row_Recordset2['emptyc'] ?? 0) == 1) { ?>
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label class="mt-3 form-text">String Crosses (This stock string is set to "Reel Finished")</label>
                      <input type="text" name="empty" placeholder="<?php echo e($row_Recordset2['brandc']) . " " . e($row_Recordset2['typec']) . " " . e($row_Recordset2['notesc_string']); ?>" class="form-control" readonly />
                    </div>
                  </div>
                </div>
              <?php } else { ?>
                <label>String Crosses</label>
                <div class="form-group mb-3">
                  <div class="row">
                    <div class="col-12">
                      <select class="form-control searchable-dropdown" style="width:100%" name="stringidc">
                        <option value="0">Same as mains</option>
                        <?php if ($totalRows_Recordset7 > 0) {
                          while ($row_Recordset7 = mysqli_fetch_assoc($Recordset7)) {
                            $selected = ($row_Recordset7['stringid'] == $row_Recordset2['stringid_c']) ? "selected" : "";
                        ?>
                            <option value="<?php echo e($row_Recordset7['stringid']); ?>" <?php echo $selected; ?>>
                              <?php echo e($row_Recordset7['brand']) . " " . e($row_Recordset7['type']) . " " . e($row_Recordset7['note']); ?>
                            </option>
                        <?php }
                        } ?>
                      </select>
                    </div>
                  </div>
                </div>
              <?php } ?>

              <label class="mt-3">Racket (Plus image)</label>
              <div class="form-group">
                <div class="row">
                  <div class="col-12 mb-3">
                    <select class="form-control searchable-dropdown" style="width:100%" name="racketid">
                      <option value="">Please select</option>
                      <?php while ($row_Recordset4 = mysqli_fetch_assoc($Recordset4)) {
                        $selected = ($row_Recordset4['racketid'] == $row_Recordset2['racketid']) ? "selected" : "";
                      ?>
                        <option value="<?php echo e($row_Recordset4['racketid']); ?>" <?php echo $selected; ?>>
                          <?php echo e($row_Recordset4['manuf']) . " " . e($row_Recordset4['model']); ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <?php
                if ($imageid > 0) {
                  $query_Recordset5 = "SELECT image from images WHERE id = '$imageid'";
                  $Recordset5 = mysqli_query($conn, $query_Recordset5);
                  if ($row_Recordset5 = mysqli_fetch_assoc($Recordset5)) {
                    if (!empty($row_Recordset5['image'])) {
                      $imageData = $row_Recordset5['image'];
                ?>
                      <img class="img-responsive-width" data-toggle="modal" data-target="#exampleModal" src="data:image/jpeg;base64,<?php echo base64_encode($imageData); ?>" alt="Uploaded Image" style="max-width: 150px; cursor:pointer;">
                      <a href="./deleteimage.php?imageid=<?php echo $imageid; ?>&jobid=<?php echo e($jobid); ?>" class="text-dark fa-solid fa-trash-can fa-lg ml-2"></a>

                      <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-body">
                              <img src="data:image/jpeg;base64,<?php echo base64_encode($imageData); ?>" alt="Uploaded Image" style="max-width: 100%;">
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                          </div>
                        </div>
                      </div>
                <?php
                    }
                  }
                } ?>

                <div class="row">
                  <div class="col-12">
                    <div class="mt-3 mb-3 custom-file">
                      <input class="custom-file-input" name="image" type="file" accept="image/*" capture="camera">
                      <label class="custom-file-label" for="customFile">Replace current image</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card cardvp mt-3">
            <div class="card-body">
              <div class="px-3 rounded form-text">
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <div class="slidecontainer">
                        <p>Tension Mains (<?php echo e($weight); ?>): <span id="tensionmV"></span></p>
                        <input class="slider" step="0.5" type="range" min="0" max="<?php echo e($maxtension); ?>" value="<?php echo e($row_Recordset2['atension'] ?? 0); ?>" name="tensionm" id="tensionm">
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group">
                      <div class="slidecontainer">
                        <p class="mt-3">Tension Crosses (<?php echo e($weight); ?>): <span id="tensioncV"></span></p>
                        <input class="slider" step="0.5" type="range" min="0" max="<?php echo e($maxtension); ?>" value="<?php echo e($row_Recordset2['atensionc'] ?? 0); ?>" name="tensionc" id="tensionc">
                      </div>
                    </div>
                  </div>

                  <div class="container">
                    <div class="row">
                      <div class="col-12">
                        <div class="form-group">
                          <p class="mt-3">Pre-Stretch:</p>
                          <div class="col-12 btn-group btn-group-toggle" role="group" data-toggle="buttons">
                            <?php
                            $pt_val = $row_Recordset2['pre_tension'] ?? 0;
                            foreach ([0, 5, 10, 15, 20] as $val) {
                              $active = ($pt_val == $val) ? "active" : "";
                              $checked = ($pt_val == $val) ? "checked" : "";
                            ?>
                              <label class="border btn btn-warning <?php echo $active; ?>">
                                <input type="radio" name="preten" value="<?php echo $val; ?>" autocomplete="off" <?php echo $checked; ?>> <?php echo $val; ?>%
                              </label>
                            <?php } ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-3 card cardvp">
            <div class="card-body">
              <div class="px-3 rounded">
                <div class="row form-text">
                  <div class="col-12">
                    <div class="form-group">
                      <label class="mt-3">Date Received</label>
                      <div class="input-group date" id="id_4">
                        <input type="text" value="<?php echo e($row_Recordset2['collection_date']); ?>" name="daterecd" class="form-control" required />
                        <div class="input-group-addon input-group-append">
                          <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row form-text">
                  <div class="col-12">
                    <div class="form-group">
                      <label class="mt-3">Date Required</label>
                      <div class="input-group date" id="id_3">
                        <input type="text" value="<?php echo e($row_Recordset2['delivery_date']); ?>" name="datereqd" class="form-control" required />
                        <div class="input-group-addon input-group-append">
                          <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label class="mt-3 form-text">Set price for job <?php echo e($currency); ?></label>
                      <input type="text" name="setprice" value="<?php echo e($row_Recordset2['price']); ?>" class="form-control" required />
                    </div>
                  </div>
                </div>
                <div class="row form-text">
                  <div class="col-12">
                    <div class="form-group">
                      <label for="comments">Comments</label>
                      <textarea class="form-control" name="comments" id="comments" rows="3"><?php echo e($row_Recordset2['comments']); ?></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <input type="hidden" name="jobid" value="<?php echo e($jobid); ?>">

          <div class="container my-3">
            <div class="row">
              <div class="col-6">
                <input class="btn button-colours" type="submit" name="submitEditjob" value="Submit">
              </div>
              <div class="col-6">
                <a class="btn button-colours-alt float-right" href="./string-jobs.php">Cancel</a>
              </div>
            </div>
          </div>
          </form>
        </div>
      </div>
    </div>

    <div class="container center">
      <div class="p-3 row">
        <div class="col-2"><a href="./addjob.php" type="button" class="dot fa-solid fa-plus fa-2x"></a></div>
        <div class="col-2">
          <h3 class="<?php echo !empty($_SESSION['message']) ? 'blinking' : 'dotb'; ?>" title="Warning Messages" data-toggle="modal" data-target="#warningModal"><strong>!</strong></h3>
        </div>
        <div class="col-2">
          <h3 class="dotbt h6 " title="Restrings for <?php echo $current_month_text; ?>"><?php echo $totalRows_Recordset10 ?></h3>
        </div>
        <div class="col-2"><a href="./jobs-unpaid.php" class="dotbt h6" title="Total restrings"><?php echo $totalRows_Recordset9 ?></a></div>
        <div class="col-2"><a href="./jobs-unpaid.php" class="dotbt h6" title="Amount Owed"><?php echo $currency . e($sum_owed); ?></a></div>
        <div class="col-2"><a href="./jobs-unpaid.php" class="dotbtt h7" title="Total Income"><small><?php echo $currency . e($sum); ?></small></a></div>
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
                  <a class="btn modal_button_cancel" href="./string.php">Cancel</a>
                </div>
                <div class="col-4">
                  <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input class="btn modal_button_submit float-right" type="submit" name="submitclearmessage" value="Clear">
                    <input type="hidden" name="jobid" value="<?php echo e($jobid); ?>">
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
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="./js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="./js/demo.js"></script>

    <script>
      $(document).ready(function() {
        if ($('.searchable-dropdown').length) {
          $('.searchable-dropdown').select2({
            width: '100%',
            placeholder: "Type to search..."
          });
        }
      });

      var sliderm = document.getElementById("tensionm");
      var outputm = document.getElementById("tensionmV");
      if (sliderm && outputm) {
        outputm.innerHTML = sliderm.value;
        sliderm.oninput = function() {
          outputm.innerHTML = this.value;
        }
      }

      var sliderc = document.getElementById("tensionc");
      var outputc = document.getElementById("tensioncV");
      if (sliderc && outputc) {
        outputc.innerHTML = sliderc.value;
        sliderc.oninput = function() {
          outputc.innerHTML = this.value;
        }
      }

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