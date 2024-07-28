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
$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");
if (isset($_POST['submitclearmessage'])) {
  unset($_SESSION['message']);
  $_GET['jobid'] = $_POST['jobid'];
}
//load all of the DB Queries
//-------------------------------------------------------
$query_Recordset2 = "SELECT *, stringjobs.tension as atension, stringjobs.tensionc as atensionc 
FROM stringjobs
LEFT JOIN customer ON customerid = cust_ID 
LEFT JOIN string ON stringjobs.stringid = string.stringid 
LEFT JOIN rackets ON stringjobs.racketid = rackets.racketid 
LEFT JOIN all_string ON all_string.string_id = string.stock_id
LEFT JOIN sport ON all_string.sportid = sport.sportid 
WHERE job_id = " . $_GET['jobid'];
$Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));
$row_Recordset2 = mysqli_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);
//-------------------------------------------------------
$query_Recordset3 = "SELECT * FROM customer ORDER BY Name ASC;";
$Recordset3 = mysqli_query($conn, $query_Recordset3) or die(mysqli_error($conn));
$row_Recordset3 = mysqli_fetch_assoc($Recordset3);
$totalRows_Recordset3 = mysqli_num_rows($Recordset3);
//-------------------------------------------------------
$query_Recordset4 = "SELECT * FROM rackets WHERE sport = '" . $row_Recordset2['sportid'] . "' ORDER BY manuf ASC;";
$Recordset4 = mysqli_query($conn, $query_Recordset4) or die(mysqli_error($conn));
$row_Recordset4 = mysqli_fetch_assoc($Recordset4);
$totalRows_Recordset4 = mysqli_num_rows($Recordset4);
//-------------------------------------------------------
$query_Recordset7 = "SELECT * FROM string LEFT JOIN all_string ON string.stock_id = all_string.string_id WHERE sportid = '" . $row_Recordset2['sportid'] . "' AND empty = '0' ORDER BY string.stringid ASC;";
$Recordset7 = mysqli_query($conn, $query_Recordset7) or die(mysqli_error($conn));
$row_Recordset7 = mysqli_fetch_assoc($Recordset7);
$totalRows_Recordset7 = mysqli_num_rows($Recordset7);
//-------------------------------------------------------
$query_Recordset8 = "SELECT * FROM string LEFT JOIN all_string ON string.stock_id = all_string.string_id WHERE sportid = '" . $row_Recordset2['sportid'] . "' AND empty = '0' ORDER BY string.stringid ASC;";
$Recordset8 = mysqli_query($conn, $query_Recordset8) or die(mysqli_error($conn));
$row_Recordset8 = mysqli_fetch_assoc($Recordset8);
$totalRows_Recordset8 = mysqli_num_rows($Recordset8);
//-------------------------------------------------------
$query_Recordset5 = "SELECT * FROM grip;";
$Recordset5 = mysqli_query($conn, $query_Recordset5) or die(mysqli_error($conn));
$row_Recordset5 = mysqli_fetch_assoc($Recordset5);
$totalRows_Recordset5 = mysqli_num_rows($Recordset5);
//-------------------------------------------------------
$query_Recordset9 = "SELECT * FROM stringjobs ORDER BY job_id ASC;";
$Recordset9 = mysqli_query($conn, $query_Recordset9) or die(mysqli_error($conn));
$row_Recordset9 = mysqli_fetch_assoc($Recordset9);
$totalRows_Recordset9 = mysqli_num_rows($Recordset9);
//-------------------------------------------------------
$query_Recordset10 = "SELECT * FROM stringjobs WHERE collection_date LIKE '___" . $current_month_numeric . "/" . $current_year . "%'ORDER BY job_id ASC;";
$Recordset10 = mysqli_query($conn, $query_Recordset10) or die(mysqli_error($conn));
$row_Recordset10 = mysqli_fetch_assoc($Recordset10);
$totalRows_Recordset10 = mysqli_num_rows($Recordset10);
//-------------------------------------------------------
$query_Recordset11 = "SELECT SUM(price) as SUM from stringjobs;";
$Recordset11 = mysqli_query($conn, $query_Recordset11) or die(mysqli_error($conn));
$row_Recordset11 = mysqli_fetch_assoc($Recordset11);
$sum = $row_Recordset11['SUM'];
//-------------------------------------------------------
$query_Recordset12 = "SELECT SUM(price) as SUM from stringjobs WHERE paid != '1';";
$Recordset12 = mysqli_query($conn, $query_Recordset12) or die(mysqli_error($conn));
$row_Recordset12 = mysqli_fetch_assoc($Recordset12);
$sum_owed = $row_Recordset12['SUM'];
$_SESSION['sum_owed'] = $sum_owed;
$imageid = $row_Recordset2['imageid'];
//-------------------------------------------------------
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="./css/bootstrap-datetimepicker.min.css" type="text/css" media="all" />
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
  echo $main_menus; ?>
  <div class="home-section diva">
    <div class="subheader"> </div>
    <p class="fxdtextb"><strong>Edit</strong> Restring: <?php echo $row_Recordset2['job_id']; ?></p>
    <div class="container my-1  firstparaaltej">
      <div class="container  my-1 pb-3 px-1 firstparaej">
        <div class="container  px-1  pt-3 form-text">
          <div class="card cardvp" style="margin-top: 50px;">
            <div class="card-body">
              <!--grip form-->
              <form method="post" action="./db-update.php" enctype="multipart/form-data">
                <div class="row">
                  <div class="col-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="gripreqd" value="1" id="grip" <?php if ($row_Recordset2['grip_required'] == 1) {
                                                                                                            echo " checked";
                                                                                                          } ?>>
                      <label class="form-check-label form-text" for="grip">
                        Grip
                      </label>
                    </div>
                  </div>
                  <!--free job form-->
                  <div class="col-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="freerestring" value="1" id="freerestring" <?php if ($row_Recordset2['free_job'] == 1) {
                                                                                                                        echo " checked";
                                                                                                                      } ?>>
                      <label class="form-check-label form-text" for="freerestring">
                        Free
                      </label>
                    </div>
                  </div>
                  <!--paid form-->
                  <div class="col-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="paid" value="1" id="paid" <?php if ($row_Recordset2['paid'] == 1) {
                                                                                                        echo " checked";
                                                                                                      } ?>>
                      <label class="form-check-label form-text" for="paid">
                        Paid
                      </label>
                    </div>
                  </div>
                  <!--delivered form-->
                  <div class="col-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="delivered" value="1" id="delivered" <?php if ($row_Recordset2['delivered'] == 1) {
                                                                                                                  echo " checked";
                                                                                                                } ?>>
                      <label class="form-check-label form-text" for="paid">
                        Deliv
                      </label>
                    </div>
                  </div>
                </div>
            </div>
          </div>
        </div>
        <div class="container mt-3 ">
          <div class="row">
            <div class="col-6">
              <div>
                <input class="form-check-input" type="hidden" name="editflag" value="1" id="editflag">
                <input class="btn button-colours" type="submit" name="submitEditjob" value="Submit">
              </div>
            </div>
            <div class="col-6">
              <div>
                <a class="btn button-colours-alt float-right" href="./string-jobs.php">Cancel</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Customer section-->
      <div class="container px-1 pt-3 form-text">
        <div class="card cardvp mt-3">
          <div class="card-body">
            <label class="form-text">Customer</label>
            <div class="form-group">
              <div class="row">
                <div class="col-12">
                  <select class="form-control" style="width:100%" name="customerid">
                    <option>Please select</option>
                    <?php do {
                      if ($row_Recordset3['cust_ID'] == $row_Recordset2['customerid']) { ?>
                        <option value="<?php echo $row_Recordset3['cust_ID']; ?>" selected="selected">
                          <?php echo $row_Recordset3['Name']; ?>
                        </option>
                      <?php } else { ?>
                        <option value="<?php echo $row_Recordset3['cust_ID']; ?>">
                          <?php echo $row_Recordset3['Name']; ?>
                        </option>
                      <?php } ?>
                    <?php } while ($row_Recordset3 = mysqli_fetch_assoc($Recordset3));  ?>
                  </select>
                  <?php mysqli_data_seek($Recordset3, 0); ?>
                </div>
              </div>
            </div>
            <!--String form-->
            <label class="mt-3">String Mains</label>
            <div class="form-group">
              <div class="row">
                <div class="col-12">
                  <select class="form-control" style="width:100%" name="stringid">
                    <option value="0">Please select</option>
                    <?php do {
                      if ($row_Recordset7['stringid'] == $row_Recordset2['stringid']) { ?>
                        <option value="<?php echo $row_Recordset7['stringid']; ?>" selected="selected">
                          <?php echo $row_Recordset7['brand'] . " " . $row_Recordset7['type'] . " " . $row_Recordset7['note']; ?>
                        </option>
                      <?php } else { ?>
                        <option value="<?php echo $row_Recordset7['stringid']; ?>">
                          <?php echo $row_Recordset7['brand'] . " " . $row_Recordset7['type'] . " " . $row_Recordset7['note']; ?>
                        </option>
                      <?php } ?>
                    <?php } while ($row_Recordset7 = mysqli_fetch_assoc($Recordset7)); ?>
                  </select>
                  <?php mysqli_data_seek($Recordset7, 0); ?>
                </div>
              </div>
            </div>
            <label>String Crosses</label>
            <div class="form-group mb-3">
              <div class="row">
                <div class="col-12">
                  <select class="form-control" style="width:100%" name="stringidc">
                    <option value="0">Same as mains</option>
                    <?php do {
                      if ($row_Recordset8['stringid'] == $row_Recordset2['stringidc']) { ?>
                        <option value="<?php echo $row_Recordset8['stringid']; ?>" selected="selected">
                          <?php echo $row_Recordset8['brand'] . " " . $row_Recordset8['type'] . " " . $row_Recordset8['note']; ?>
                        </option>
                      <?php } else { ?>
                        <option value="<?php echo $row_Recordset8['stringid']; ?>">
                          <?php echo $row_Recordset8['brand'] . " " . $row_Recordset8['type'] . " " . $row_Recordset8['note']; ?>
                        </option>
                      <?php } ?>
                    <?php } while ($row_Recordset8 = mysqli_fetch_assoc($Recordset7)); ?>
                  </select>
                  <?php mysqli_data_seek($Recordset8, 0); ?>
                </div>
              </div>
              <!--Racket form-->
              <label class="mt-3">Racket (Plus image)</label>
              <div class="form-group">
                <div class="row">
                  <div class="col-12 mb-3">
                    <select class="form-control" style="width:100%" name="racketid">
                      <option>Please select</option>
                      <?php do {
                        if ($row_Recordset4['racketid'] == $row_Recordset2['racketid']) { ?>
                          <option value="<?php echo $row_Recordset4['racketid']; ?>" selected="selected">
                            <?php echo $row_Recordset4['manuf'] . " " . $row_Recordset4['model']; ?>
                          </option>
                        <?php } else { ?>
                          <option value="<?php echo $row_Recordset4['racketid']; ?>">
                            <?php echo $row_Recordset4['manuf'] . " " . $row_Recordset4['model']; ?>
                          </option>
                        <?php } ?>
                      <?php } while ($row_Recordset4 = mysqli_fetch_assoc($Recordset4)); ?>
                    </select>
                    <?php mysqli_data_seek($Recordset4, 0); ?>
                  </div>
                </div>
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
                  <a href="./deleteimage.php?imageid=<?php echo $imageid; ?>&jobid=<?php echo $_GET['jobid']; ?>" class="text-dark fa-solid fa-trash-can fa-lg"></a>
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
                <div class="row">
                  <div class="col-12">
                    <div class="mt-3 mb-3 custom-file">
                      <input class="custom-file-input" name="image" placeholder="Take image" type="file" accept="image/*" capture="camera">
                      <label class="custom-file-label" for="customFile">Replace current image</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--Tension form-->
        <div class="card cardvp mt-3">
          <div class="card-body">
            <div class=" px-3 rounded form-text">
              <div class="row">
                <div class="col-12">
                  <div class="form-group">
                    <div class="slidecontainer">
                      <p>Tension Mains (lbs): <span id="tensionmV"></span></p>
                      <div class="slidecontainer">
                        <input class="slider" type="range" min="0" max="70" value="<?php echo  $row_Recordset2['atension'] ?>" class="slider" name="tensionm" id="tensionm">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <div class="slidecontainer">
                      <p class="mt-3">Tension Crosses (lbs): <span id="tensioncV"></span></p>
                      <input class="slider" type="range" min="0" max="70" value="<?php echo  $row_Recordset2['atensionc'] ?>" class="slider" name="tensionc" id="tensionc">
                    </div>
                  </div>
                </div>
                <!--Pre-Tension form-->
                <div class="container">
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">
                        <p class="mt-3">Pre-Stretch:</p>
                        <div class="col-12 btn-group btn-group-toggle" role="group" data-toggle="buttons">
                          <label class="border btn btn-warning <?php if ($row_Recordset2['pre_tension'] == 0) {
                                                                  echo " active";
                                                                } ?>">
                            <input type="radio" name="preten" id="option1" value="0" autocomplete="off" <?php if ($row_Recordset2['pre_tension'] == 0) {
                                                                                                          echo " checked";
                                                                                                        } ?>> 0%
                          </label>
                          <label class="border btn btn-warning <?php if ($row_Recordset2['pre_tension'] == 5) {
                                                                  echo " active";
                                                                } ?>">
                            <input type="radio" name="preten" id="option2" value="5" autocomplete="off" <?php if ($row_Recordset2['pre_tension'] == 5) {
                                                                                                          echo " checked";
                                                                                                        } ?>> 5%
                          </label>
                          <label class="border btn btn-warning <?php if ($row_Recordset2['pre_tension'] == 10) {
                                                                  echo " active";
                                                                } ?>">
                            <input type="radio" name="preten" id="option3" value="10" autocomplete="off" <?php if ($row_Recordset2['pre_tension'] == 10) {
                                                                                                            echo " checked";
                                                                                                          } ?>> 10%
                          </label>
                          <label class="border btn btn-warning <?php if ($row_Recordset2['pre_tension'] == 15) {
                                                                  echo " active";
                                                                } ?>">
                            <input type="radio" name="preten" id="option4" value="15" autocomplete="off" <?php if ($row_Recordset2['pre_tension'] == 15) {
                                                                                                            echo " checked";
                                                                                                          } ?>> 15%
                          </label>
                          <label class="border btn btn-warning <?php if ($row_Recordset2['pre_tension'] == 20) {
                                                                  echo " active";
                                                                } ?>">
                            <input type="radio" name="preten" id="option5" value="20" autocomplete="off" <?php if ($row_Recordset2['pre_tension'] == 20) {
                                                                                                            echo " checked";
                                                                                                          } ?>> 20%
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--date form-->
        <div class="mt-3 card cardvp">
          <div class="card-body">
            <div class="px-3 rounded">
              <div class="row form-text">
                <div class="col-12">
                  <div class="form-group">
                    <label class="mt-3">Date Received</label>
                    <div class="form-group">
                      <div class="input-group date" id="id_4">
                        <input type="text" value="<?php echo $row_Recordset2['collection_date']; ?>" name="daterecd" class="form-control" required />
                        <div class="input-group-addon input-group-append">
                          <div class="input-group-text">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row form-text">
                <div class="col-12">
                  <div class="form-group">
                    <label class="mt-3">Date Required</label>
                    <div class="form-group">
                      <div class="input-group date" id="id_3">
                        <input type="text" value="<?php echo $row_Recordset2['delivery_date']; ?>" name="datereqd" class="form-control" required />
                        <div class="input-group-addon input-group-append">
                          <div class="input-group-text">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="form-group">
                    <label class="mt-3 form-text">Set price for job <?php echo $currency; ?></label>
                    <div class="form-group">
                      <div class="input-group">
                        <input type="text" name="setprice" value="<?php echo $row_Recordset2['price']; ?>" class="form-control" required />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--comments form-->
              <div class="row form-text">
                <div class="col-12">
                  <div class="form-group">
                    <label for="comments">Comments</label>
                    <textarea class="form-control" name="comments" id="comments" rows="3"><?php echo $row_Recordset2['comments']; ?></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <input type="hidden" name="jobid" class="txtField" value="<?php echo $_GET['jobid']; ?>">
      <div class="container my-3">
        <div class="row">
          <div class="col-6">
            <div>
              <input class="form-check-input" type="hidden" name="editflag" value="1" id="editflag">
              <input class="btn button-colours" type="submit" name="submitEditjob" value="Submit">
            </div>
          </div>
          <div class="col-6">
            <div>
              <a class="btn button-colours-alt float-right" href="./string-jobs.php">Cancel</a>
            </div>
          </div>
        </div>
      </div>
      </form>
    </div>
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
        <h3 class="dotbt h6 " title="Restrings for <?php echo $current_month_text; ?>"><?php echo $totalRows_Recordset10 ?></h3>
      </div>
      <div class="col-2">
        <a href="./jobs-unpaid.php" class="dotbt h6" title="Total restrings"><?php echo $totalRows_Recordset9 ?></a>
      </div>
      <div class="col-2">
        <a href="./jobs-unpaid.php" class="dotbt h6" title="Amount Owed"><?php echo "$currency" . $sum_owed ?></a>
      </div>
      <div class="col-2">
        <a href="./jobs-unpaid.php" class="dotbtt h7" title="Total Income"><small><?php echo "$currency" . $sum ?></small></a>
      </div>
    </div>
  </div>
  <!-- Information modal -->
  <div class="modal  fade text-dark" id="warningModal">
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
                  <a class="btn modal_button_cancel" href="./string.php">Cancel</a>
                </div>
              </div>
              <div class="col-4">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                  <input class="btn modal_button_submit float-right" type="submit" name="submitclearmessage" value="Clear">
                  <input type="hidden" name="jobid" class="txtField" value="<?php echo $_GET['jobid']; ?>">
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
  <script>
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
      var fileName = $(this).val().split("\\").pop();
      $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
  </script>
</body>

</html>