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
if (isset($_POST['submitclearmessage'])) {
  unset($_SESSION['message']);
}
$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");
//load all of the DB Queries

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
stringjobs.tension as atension,
stringjobs.tensionc as atensionc,
stringjobs.racketid as racketid,
stringjobs.stringid as stringidm,
stringjobs.stringidc as stringidc,

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
string.note as notes_string,

stringc.note as notesc_string,
stringc.string_number as stringc_number,
stringc.stringid as stringid_c,

reel_lengthsm.length as lengthm,
reel_lengthsc.length as lengthc

FROM stringjobs 
LEFT JOIN customer ON customerid = cust_ID

LEFT JOIN string 
ON stringjobs.stringid = string.stringid 

LEFT JOIN string 
AS stringc 
ON stringjobs.stringidc = stringc.stringid

LEFT JOIN all_string
ON string.stock_id = all_string.string_id

LEFT JOIN all_string 
AS all_stringc 
ON stringc.stock_id = all_stringc.string_id

LEFT JOIN reel_lengths
AS reel_lengthsm
ON reel_lengthsm.reel_length_id = string.lengthid

LEFT JOIN reel_lengths
AS reel_lengthsc
ON reel_lengthsc.reel_length_id = string.lengthid

LEFT JOIN rackets ON stringjobs.racketid = rackets.racketid 
LEFT JOIN sport ON all_string.sportid = sport.sportid
WHERE addedby = '" . $_SESSION['id'] . "' ORDER BY job_id DESC";

$Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);
//-------------------------------------------------------
//load all of the DB Queries
$sql = "SELECT * FROM accounts WHERE id = " . $_SESSION['id'];
$Recordset2 = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$row_Recordset2 = mysqli_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);
//-------------------------------------------------------
$query_Recordset6 = "SELECT * FROM stringjobs WHERE collection_date LIKE '___" . $current_month_numeric . "/" . $current_year . "%'ORDER BY job_id ASC;";
$Recordset6 = mysqli_query($conn, $query_Recordset6) or die(mysqli_error($conn));
$row_Recordset6 = mysqli_fetch_assoc($Recordset6);
$totalRows_Recordset6 = mysqli_num_rows($Recordset6);
//-------------------------------------------------------
$query_Recordset7 = "SELECT * FROM stringjobs ORDER BY job_id ASC;";
$Recordset7 = mysqli_query($conn, $query_Recordset7) or die(mysqli_error($conn));
$row_Recordset7 = mysqli_fetch_assoc($Recordset7);
$totalRows_Recordset7 = mysqli_num_rows($Recordset7);
//-------------------------------------------------------
$query_Recordset8 = "SELECT SUM(price) as SUM from stringjobs;";
$Recordset8 = mysqli_query($conn, $query_Recordset8) or die(mysqli_error($conn));
$row_Recordset8 = mysqli_fetch_assoc($Recordset8);
$sum = $row_Recordset8['SUM'];
//-------------------------------------------------------
$query_Recordset9 = "SELECT SUM(price) as SUM from stringjobs WHERE paid != '1';";
$Recordset9 = mysqli_query($conn, $query_Recordset9) or die(mysqli_error($conn));
$row_Recordset9 = mysqli_fetch_assoc($Recordset9);
$sum_owed = $row_Recordset9['SUM'];
$_SESSION['sum_owed'] = $sum_owed;
//-------------------------------------------------------

//lets check how many string jobs are left on the reel
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <!-- bootstrap -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <!-- datepicker styles -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css">
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
  <title>SDBA</title>

  <link rel="icon" type="image/png" href="./img/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="./img/favicon-16x16.png" sizes="16x16" />
</head>

<body data-spy="scroll" data-target="#main-nav">
  <?php //main nav menu

  echo $main_menus;
  ?>

  <!-- HOME SECTION -->
  <>
    <div class="home-section diva">
      <div class="subheader"></div>
      <!--Lets build the table-->
      <p class="fxdtextb"><strong>Account</strong> Home: <?php echo $_SESSION['name']; ?></php>
      </p>
      <table id="tblUser" class="table-text table-hover table table-sm center">
        <thead>
          <tr>
            <th>
              Username
            </th>

            <th class="d-none d-lg-table-cell" style="text-align: center">
              Email
            </th>
            <th style="text-align: center">
              Access Level
            </th>
            <th class="d-none d-lg-table-cell" style="text-align: center">
              Active
            </th>
            <th class="d-none d-md-table-cell" style="text-align: center">
              Password
            </th>
            <th style="text-align: center">
              Edit
            </th>


          </tr>
        </thead>
        <tbody>
          <?php
          do { ?>
            <tr>
              <td class="pl-3"><?php echo $row_Recordset2['username']; ?></td>
              <td class="d-none d-lg-table-cell pl-3" style="text-align: center"><?php echo $row_Recordset2['email']; ?></td>
              <td class="pl-3" style="text-align: center"><?php echo $row_Recordset2['level']; ?></td>

              <td class="d-none d-lg-table-cell" style="text-align: center">
                <?php
                if ($row_Recordset2['active'] == '1') { ?>
                  <i class="text-success fa-solid fa-check"></i>
                <?php } else { ?>
                  <i class="text-danger fa-solid fa-xmark"></i><?php } ?>
              </td>
              <td class="d-none d-md-table-cell" style="text-align: center">
                <small class="p-1 modal_button_submit rounded m-1" data-toggle="modal" data-target="#UserPass<?php echo $row_Recordset2['id']; ?>">Reset Password</small>
              </td>
              <td style="text-align: center"><i class=" fa-solid fa-pen-to-square" data-toggle="modal" data-target="#UserEdit<?php echo $row_Recordset2['id']; ?>"></i></td>
            </tr>

            <!-- EDIT MODAL -->
            <div class="modal  fade text-dark" id="UserEdit<?php echo $row_Recordset2['id']; ?>">
              <div class="modal-dialog">
                <div class="modal-content  border radius">
                  <div class="modal-header modal_header">
                    <h5 class=" modal-title">You are editing &nbsp;"<?php echo $row_Recordset2['username']; ?>"</h5>
                    <button class="close" data-dismiss="modal">
                      <span>&times;</span>
                    </button>
                  </div>
                  <div class="modal-body  modal_body">
                    <form method="post" action="site-users-db.php">
                      <div><?php if (isset($message)) {
                              echo $message;
                            } ?>
                      </div>
                      <div style="padding-bottom:5px;">
                      </div>

                      <input type="hidden" name="refedit" class="txtField" value="<?php echo $row_Recordset2['id']; ?>">

                      <div class="form-group">
                        <label for="name">User Name</label>
                        <input class="form-control" id="name" type="text" name="username" value="<?php echo $row_Recordset2['username']; ?>">
                        <label class="pt-3" for="email">Email Address</label>

                        <input class="form-control" id="email" type="text" name="email" value="<?php echo $row_Recordset2['email']; ?>">
                      </div>
                      <input type="hidden" name="active" value="0">
                      <?php
                      if ($row_Recordset2['active'] == '1') {
                        $checked = "checked";
                      } else {
                        $checked = "unchecked";
                      } ?>


                      <input type="hidden" name="level" class="txtField" value="<?php echo $_SESSION['level']; ?>">

                      <input type="hidden" name="active" class="txtField" value="1">
                      <input type="hidden" name="marker" class="txtField" value="1">



                  </div>
                  <div class="modal-footer modal_footer">
                    <button class="btn modal_button_cancel" data-dismiss="modal">
                      <span>Cancel</span>
                    </button>
                    <input class="btn modal_button_submit" type="submit" name="submitEdit" value="Submit">
                  </div>
                  </form>
                </div>
              </div>
            </div>


            <!-- Password MODAL -->
            <div class="modal  fade text-dark" id="UserPass<?php echo $row_Recordset2['id']; ?>">
              <div class="modal-dialog">
                <div class="modal-content  border radius">
                  <div class="modal-header modal_header">
                    <h5 class=" modal-title">You are resetting the password for &nbsp;"<?php echo $row_Recordset2['username']; ?>"</h5>
                    <button class="close" data-dismiss="modal">
                      <span>&times;</span>
                    </button>
                  </div>
                  <div class="modal-body  modal_body">
                    <form method="post" action="site-users-db.php">
                      <div><?php if (isset($message)) {
                              echo $message;
                            } ?>
                      </div>

                      <?php if (isset($_SESSION['password1'])) {
                        $value1 = "value='" . $_SESSION['password1'] . "'";
                      } else {
                        $value1 = '';
                      } ?>

                      <?php if (isset($_SESSION['password2'])) {
                        $value2 = "value='" . $_SESSION['password1'] . "'";
                      } else {
                        $value2 = '';
                      } ?>

                      <input type="hidden" name="refedit" class="txtField" value="<?php echo $row_Recordset2['id']; ?>">
                      <div class="form-group">
                        <label for="name">Password:</label>
                        <input class="form-control" id="name" type="password" name="password1" placeholder="Type password" <?php echo $value1; ?>>
                        <label class="mt-2" for="name">Repeat Password:</label>
                        <input class="form-control" id="name" type="password" name="password2" placeholder="Type password" <?php echo $value2; ?>>
                        <p class="pt-2 text-dark">Password 8 characters minimum.<br>
                          At least one uppercase letter.<br>
                          At least one lowercase letter.<br>
                          At least one digit.<br>
                          at least one special character.</p>

                      </div>
                  </div>
                  <div class="modal-footer modal_footer">
                    <button class="btn modal_button_cancel" data-dismiss="modal">
                      <span>Cancel</span>
                    </button>
                    <input class="btn modal_button_submit" type="submit" name="submitPass" value="Submit">
                  </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- delete  modal -->
            <div class="modal  fade text-dark" id="UserDelete<?php echo $row_Recordset2['id']; ?>">
              <div class="modal-dialog">
                <div class="modal-content  border radius">
                  <div class="modal-header modal_header">
                    <h5 class=" modal-title">You are about to delete &nbsp;"<?php echo $row_Recordset2['username']; ?>"</h5>
                    <button class="close" data-dismiss="modal">
                      <span>&times;</span>
                    </button>
                  </div>
                  <div class="modal-body  modal_body">
                    <form method="post" action="site-users-db.php">
                      <div>Please confirm or cancel!
                      </div>
                      <div style="padding-bottom:5px;">
                      </div>

                      <input type="hidden" name="refdel" class="txtField" value="<?php echo $row_Recordset2['id']; ?>">
                  </div>

                  <div class="modal-footer modal_footer">

                    <button class="btn modal_button_cancel" data-dismiss="modal">
                      <span>Cancel</span>
                    </button>

                    <input class="btn modal_button_submit" type="submit" name="submitDel" value="Delete">
                    </form>
                  </div>
                </div>
              </div>
            </div>
          <?php
          } while ($row_Recordset2 = mysqli_fetch_assoc($Recordset2)); ?>
        </tbody>
      </table>


      <?php if ($totalRows_Recordset1 == 0) {
        echo "<h5 class='text-center text-dark' style='margin-top: 200px;'>No Records found</h5> ";
      } else { ?>
        <table id="tblUser1" class="table-text table-hover table table-sm center" style="padding-top: 0; margin-top: 0">
          <thead>
            <tr>
              <th colspan="8">
                <div class="p-2 text-dark h4">Jobs added by <?php echo $_SESSION['name']; ?></div>
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


            </tr>

          </thead>
          <tbody>
            <?php
            do { ?>
              <tr>
                <td class="tdm">
                  <a href="./viewjob.php?jobid=<?php echo $row_Recordset1['job_id']; ?>"><?php echo $row_Recordset1['job_id']; ?></a>
                </td>
                <td><a href="./editcust.php?custid=<?php echo $row_Recordset1['customerid']; ?>"><span><?php echo substr($row_Recordset1['Name'], 0, 12); ?></span></a></td>
                <?php if ($row_Recordset1['stringid_c'] == 0) { ?>
                  <td class="d-none d-md-table-cell" data-toggle="modal" data-target="#StringViewModal<?php echo $row_Recordset1['stringidm']; ?>"><?php echo $row_Recordset1['brandm'] ?> &nbsp;<?php echo $row_Recordset1['typem']; ?>

                  <?php } elseif ((!empty($row_Recordset1['stringid_m'])) && (!empty($row_Recordset1['stringid_c']))) { ?>
                  <td class="d-none d-md-table-cell" data-toggle="modal" data-target="#StringViewModal<?php echo $row_Recordset1['stringidm']; ?>">Hybrid click for info

                  <?php } else { ?>
                  <td class="d-none d-md-table-cell">String Unknown
                  <?php } ?>
                  </td>
                  <!-- View String  modal -->
                  <div class="modal  fade" id="StringViewModal<?php echo $row_Recordset1['stringidm']; ?>">
                    <div class="modal-dialog">
                      <div class="modal-content  border radius">
                        <div class="modal-header modal_header">
                          <h5 class=" modal-title">Viewing Mains: &nbsp;<?php echo $row_Recordset1['brandm'] ?> &nbsp;<?php echo $row_Recordset1['typem']; ?></h5>
                          <button class="close" data-dismiss="modal">
                            <span>&times;</span>
                          </button>
                        </div>
                        <div class="modal-body modal_body">
                          <p class="form-text mb-0" style="font-size:12px">Start Length:</p>
                          <?php echo $row_Recordset1['lengthm'] . "M"; ?>
                          <hr>
                          <p class="form-text mb-0" style="font-size:12px" style="font-size:12px">Restrings Completed:</p>
                          <?php echo $row_Recordset1['stringm_number']; ?>


                          <?php if ($row_Recordset1['stringid_c'] != $row_Recordset1['stringid_m'] && (!is_null($row_Recordset1['stringid_c']))) { ?>
                        </div>
                        <div class="modal-header modal_header rounded-0">
                          <h5 class=" modal-title">Viewing Crosses:&nbsp;<?php echo $row_Recordset1['brandm'] ?> &nbsp;<?php echo $row_Recordset1['typec']; ?></h5>

                        </div>
                        <div class="modal-body modal_body">
                          <p class="form-text mb-0" style="font-size:12px">Start Length:</p>
                          <?php echo $row_Recordset1['lengthc'] . "M"; ?>
                          <hr>
                          <p class="form-text mb-0" style="font-size:12px" style="font-size:12px">Restrings Completed:</p>
                          <?php echo $row_Recordset1['stringc_number']; ?>

                        <?php } ?>
                        <hr>
                        <p class="form-text mb-0" style="font-size:12px" style="font-size:12px">Sport:</p>
                        <?php echo $row_Recordset1['sportname']; ?>

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

                  <?php if ($row_Recordset1['delivered'] == 0) { ?>
                    <td class="text-danger"><?php echo $row_Recordset1['collection_date']; ?></td><?php } else { ?>
                    <td><?php echo $row_Recordset1['collection_date']; ?></td>
                  <?php } ?>

                  <?php if ($row_Recordset1['paid'] == 0) { ?>
                    <td class="text-danger"><?php echo $currency . $row_Recordset1['price']; ?></td><?php } else { ?>
                    <td><?php echo $currency . $row_Recordset1['price']; ?></td>
                  <?php } ?>

                  <td><a class="text-dark fa-solid fa-pen-to-square fa-lg" href="./editjob.php?jobid=<?php echo $row_Recordset1['job_id']; ?>"></a></td>
                  <td class="d-none d-md-table-cell"><i class="text-dark fa-solid fa-trash-can fa-lg" data-toggle="modal" data-target="#delModal<?php echo $row_Recordset1['job_id']; ?>"></i></td>
                  <td><a class="fa-solid fa-tags fa-lg fa-flip-horizontal" href="./label.php?jobid=<?php echo $row_Recordset1['job_id']; ?>"></a></td>
              </tr>
              <!-- delete  modal -->
              <div class="modal  fade" id="delModal<?php echo $row_Recordset1['job_id']; ?>">
                <div class="modal-dialog">
                  <div class="modal-content  border radius">
                    <div class="modal-header modal_header">
                      <h5 class=" modal-title">You are about to delete &nbsp;"<?php echo $row_Recordset1['job_id']; ?>"</h5>
                      <button class="close" data-dismiss="modal">
                        <span>&times;</span>
                      </button>
                    </div>
                    <div class="modal-body modal_body">
                      <form method="post" action="./db-update.php">
                        <div>Please confirm or cancel!
                        </div>
                        <div style="padding-bottom:5px;">
                        </div>
                        <input type="hidden" name="refdel" class="txtField" value="<?php echo $row_Recordset1['job_id']; ?>">
                        <input type="hidden" name="stringidm" class="txtField" value="<?php echo $row_Recordset1['stringidm']; ?>">
                        <?php if (isset($row_Recordset1['stringidc'])) { ?>
                          <input type="hidden" name="stringidc" class="txtField" value="<?php echo $row_Recordset1['stringidc']; ?>">
                        <?php } ?>


                    </div>
                    <div class="modal-footer modal_footer">
                      <button class="btn modal_button_cancel" data-dismiss="modal">
                        <span>Cancel</span>
                      </button>
                      <input class="btn modal_button_submit" type="submit" name="submitdelete" value="Delete">
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            <?php
            } while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1)); ?>
          </tbody>
        </table>

      <?php } ?>

    </div>


    <div class="container center">
      <div class="p-3 row">

        <div class="col-2">
          <a href="#" type="button" class="dot fa-solid fa-plus fa-2x" data-toggle="modal" data-target="#AddUser"></a>
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
          <h3 class="dotbt h6 " title="Restrings for <?php echo $current_month_text; ?>"><?php echo $totalRows_Recordset6 ?></h3>
        </div>
        <div class="col-2">
          <a href="#" class="dotbt h6" title="Total restrings"><?php echo $totalRows_Recordset7 ?></a>

        </div>
        <div class="col-2">
          <a href="./jobs-unpaid.php" class="dotbt h6" title="Amount Owed"><?php echo "$currency" . $sum_owed ?></a>
        </div>
        <div class="col-2">
          <a href="#" class="dotbtt h7" title="Total Income"><small><?php echo "$currency" . $sum ?></small></a>
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
                    <a class="btn modal_button_cancel" href="./site-users.php">Cancel</a>
                  </div>
                </div>
                <div class="col-4">
                  <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input class="btn modal_button_submit" type="submit" name="submitclearmessage" value="Clear">
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add MODAL -->

    <div class="modal  fade text-dark" id="AddUser">
      <div class="modal-dialog">
        <div class="modal-content  border radius">
          <div class="modal-header modal_header">
            <h5 class=" modal-title">You are adding a new user"</h5>
            <button class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <form method="post" action="site-users-db.php">
            <div class="modal-body  modal_body">
              <div class="form-group">
                <label for="name">User Name</label>
                <input class="form-control" id="name" type="text" placeholder="Enter Username" name="username">
                <label class="pt-3" for="email">Email Address</label>
                <input class="form-control" id="email" placeholder="Enter Email" name="email">
              </div>
              <input type="hidden" name="active" value="1">
              <label for="name">Access Level</label>

              <select style='font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12pt; width:80%' class=" form-control" id="level" name="level">
                <option value="1">1 (Super User)</option>
                <option value="2">2 (Add jobs only)</option>

              </select>

            </div>
            <div class="modal-footer modal_footer">
              <button class="btn modal_button_cancel" data-dismiss="modal">
                <span>Cancel</span>
              </button>
              <input class="btn modal_button_submit" type="submit" name="submitAdd" value="Submit">
          </form>
        </div>
      </div>
    </div>


    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript" src="./js/noellipses.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js"></script>
    <!-- Datepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>



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
          paging: false,
          pagingType: "simple_numbers_no_ellipses",
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
          ]
        });
      });
    </script>

    <script>
      jQuery(document).ready(function($) {

        $('#tblUser1').DataTable({
          pagingType: "simple_numbers_no_ellipses",
          language: {
            'search': '',
            'searchPlaceholder': 'Search Jobs:',
            "sLengthMenu": "",
            "info": "",
            "infoEmpty": "",
          },
          pageLength: 8,
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
</body>

</html>