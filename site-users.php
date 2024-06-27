<?php require_once('./Connections/wcba.php');
include('./menu.php');

//-------------------------------------------------------------------
// Initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
  header('Location: index.html');
  exit;
}

//---------------------------------------------------
//load all of the DB Queries
$sql = "SELECT * FROM accounts";
$Recordset1 = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);
//-------------------------------------------------------


?>
<!DOCTYPE html>
<html>

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

</head>

<body>
  <?php //main nav menu

  echo $main_menus;
  ?>

  <!-- HOME SECTION -->
  <div class="home-section diva">
    <div class="subheader"></div>
    <!--Lets build the table-->
    <p class="fxdtext"><strong>User</strong> details</p>

    <table id="tblUser" class="table-text tabl-hover table table-sm center">
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
          <th class="d-none d-lg-table-cell" style="text-align: center">
            Password
          </th>
          <th style="text-align: center">
            Edit
          </th>
          <th style="text-align: center">
            Delete
          </th>

        </tr>
      </thead>
      <?php
      do { ?>
        <tr>
          <td class="pl-3"><?php echo $row_Recordset1['username']; ?></td>
          <td class="d-none d-lg-table-cell pl-3" style="text-align: center"><?php echo $row_Recordset1['email']; ?></td>
          <td class="pl-3" style="text-align: center"><?php echo $row_Recordset1['level']; ?></td>

          <td class="d-none d-lg-table-cell" style="text-align: center">
            <?php
            if ($row_Recordset1['active'] == '1') { ?>
              <i class="text-success fa-solid fa-check"></i>
            <?php } else { ?>
              <i class="text-danger fa-solid fa-xmark"></i><?php } ?>
          </td>
          <td class="d-none d-lg-table-cell" style="text-align: center">
            <small class="p-1 bg-success rounded text-white m-1" data-toggle="modal" data-target="#UserPass<?php echo $row_Recordset1['id']; ?>">Reset Password</small>
          </td>
          <td style="text-align: center"><i class="text-primary fa-solid fa-pen-to-square" data-toggle="modal" data-target="#UserEdit<?php echo $row_Recordset1['id']; ?>"></i></td>
          <td style="text-align: center"><i class="text-warning fa-solid fa-trash-can" data-toggle="modal" data-target="#UserDelete<?php echo $row_Recordset1['id']; ?>"></i></td>
        </tr>

        <!-- EDIT MODAL -->
        <div class="modal  fade text-dark" id="UserEdit<?php echo $row_Recordset1['id']; ?>">
          <div class="modal-dialog">
            <div class="modal-content  border radius">
              <div class="modal-header bg-dark text-secondary ">
                <h5 class=" modal-title">You are editing &nbsp;"<?php echo $row_Recordset1['username']; ?>"</h5>
                <button class="close" data-dismiss="modal">
                  <span>&times;</span>
                </button>
              </div>
              <div class="modal-body  bg-secondary text-white">
                <form method="post" action="site-users-db.php">
                  <div><?php if (isset($message)) {
                          echo $message;
                        } ?>
                  </div>
                  <div style="padding-bottom:5px;">
                  </div>

                  <input type="hidden" name="refedit" class="txtField" value="<?php echo $row_Recordset1['id']; ?>">

                  <div class="form-group">
                    <label for="name">User Name</label>
                    <input class="form-control" id="name" type="text" name="username" value="<?php echo $row_Recordset1['username']; ?>">
                    <label class="pt-3" for="email">Email Address</label>

                    <input class="form-control" id="email" type="text" name="email" value="<?php echo $row_Recordset1['email']; ?>">
                  </div>
                  <input type="hidden" name="active" value="0">
                  <?php
                  if ($row_Recordset1['active'] == '1') {
                    $checked = "checked";
                  } else {
                    $checked = "unchecked";
                  } ?>


                  <div class="form-group">
                    <label for="name">Access level</label>
                    <?php
                    if ($row_Recordset1['level'] === 1) {
                      $selected = "selected='selected'";
                    } elseif ($row_Recordset1['level'] === 2) {
                      $selected = "selected='selected'";
                    } elseif ($row_Recordset1['level'] === 3) {
                      $selected = "selected='selected'";
                    } elseif ($row_Recordset1['level'] === 4) {
                      $selected = "selected='selected'";
                    } else {
                      $selected = "";
                    }

                    ?>
                    <select style='font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12pt; width:80%' class=" form-control" id="level" name="level">
                      <option value="<?php echo $row_Recordset1['level']; ?>" <?php echo $selected; ?>>1 (Super User)</option>
                      <option value="<?php echo $row_Recordset1['level']; ?>" <?php echo $selected; ?>>2 (League Admin)</option>
                      <option value="<?php echo $row_Recordset1['level']; ?>" <?php echo $selected; ?>>3 (Club Admin)</option>
                      <option value="<?php echo $row_Recordset1['level']; ?>" <?php echo $selected; ?>>4 (News Editor)</option>
                    </select>
                  </div>
                  <div class="pt-3 form-check">
                    <label class="form-check-label mr-2">
                      <input type="checkbox" class="form-check-input" name="active" value="1" <?php echo $checked; ?>> Tick to make active.
                    </label>
                  </div>

              </div>
              <div class="modal-footer bg-dark">
                <button class="btn btn-primary" data-dismiss="modal">
                  <span>Cancel</span>
                </button>
                <input class="btn btn-success" type="submit" name="submitEdit" value="Submit" class="buttom">
              </div>
              </form>
            </div>
          </div>
        </div>


        <!-- Password MODAL -->
        <div class="modal  fade text-dark" id="UserPass<?php echo $row_Recordset1['id']; ?>">
          <div class="modal-dialog">
            <div class="modal-content  border radius">
              <div class="modal-header bg-dark text-secondary ">
                <h5 class=" modal-title">You are resetting the password for &nbsp;"<?php echo $row_Recordset1['username']; ?>"</h5>
                <button class="close" data-dismiss="modal">
                  <span>&times;</span>
                </button>
              </div>
              <div class="modal-body  bg-secondary text-white">
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

                  <input type="hidden" name="refedit" class="txtField" value="<?php echo $row_Recordset1['id']; ?>">
                  <div class="form-group">
                    <label for="name">Password:</label>
                    <input class="form-control" id="name" type="password" name="password1" placeholder="Type password" <?php echo $value1; ?>>
                    <label class="mt-2" for="name">Repeat Password:</label>
                    <input class="form-control" id="name" type="password" name="password2" placeholder="Type password" <?php echo $value2; ?>>
                    <p class="pt-2 text-warning">Password 8 characters minimum.<br>
                      At least one uppercase letter.<br>
                      At least one lowercase letter.<br>
                      At least one digit.<br>
                      at least one special character.</p>

                  </div>
              </div>
              <div class="modal-footer bg-dark">
                <button class="btn btn-primary" data-dismiss="modal">
                  <span>Cancel</span>
                </button>
                <input class="btn btn-success" type="submit" name="submitPass" value="Submit" class="buttom">
              </div>
              </form>
            </div>
          </div>
        </div>

        <!-- delete  modal -->
        <div class="modal  fade text-dark" id="UserDelete<?php echo $row_Recordset1['id']; ?>">
          <div class="modal-dialog">
            <div class="modal-content  border radius">
              <div class="modal-header bg-dark text-secondary ">
                <h5 class=" modal-title">You are about to delete &nbsp;"<?php echo $row_Recordset1['username']; ?>"</h5>
                <button class="close" data-dismiss="modal">
                  <span>&times;</span>
                </button>
              </div>
              <div class="modal-body  bg-secondary text-white">
                <form method="post" action="site-users-db.php">
                  <div>Please confirm or cancel!
                  </div>
                  <div style="padding-bottom:5px;">
                  </div>

                  <input type="hidden" name="refdel" class="txtField" value="<?php echo $row_Recordset1['id']; ?>">
              </div>

              <div class="modal-footer bg-dark">

                <button class="btn btn-primary" data-dismiss="modal">
                  <span>Cancel</span>
                </button>

                <input class="btn btn-warning" type="submit" name="submitDel" value="submit" class="buttom">
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php
      } while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1)); ?>
    </table>
  </div>

  <div class="align-center dark-overlay-lighter col-12 m-1 p-2" style="text-align: center">
    <button class="btn btn-warning" data-toggle="modal" data-target="#AddUser"><span>Add a new user</span></button>
  </div>




  <!-- Add MODAL -->

  <div class="modal  fade text-dark" id="AddUser">
    <div class="modal-dialog">
      <div class="modal-content  border radius">
        <div class="modal-header bg-dark text-secondary ">
          <h5 class=" modal-title">You are adding a new user"</h5>
          <button class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <form method="post" action="site-users-db.php">
          <div class="modal-body  bg-secondary text-white">
            <div class="form-group">
              <label for="name">User Name</label>
              <input class="form-control" id="name" type="text" placeholder="Enter Username" name="username">
              <label class="pt-3" for="email">Email Address</label>
              <input class="form-control" id="email" placeholder="Enter Email" name="email">
            </div>
            <input type="hidden" name="active" value="1">

            <select style='font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12pt; width:80%' class=" form-control" id="level" name="level">
              <option value="1">1 (Super User)</option>
              <option value="2">2 (League Admin)</option>
              <option value="3">3 (Club Admin)</option>
              <option value="4">4 (News Editor)</option>
            </select>

          </div>
          <div class="modal-footer bg-dark">
            <button class="btn btn-primary" data-dismiss="modal">
              <span>Cancel</span>
            </button>
            <input class="btn btn-success" type="submit" name="submitAdd" value="Submit" class="buttom">
        </form>
      </div>
    </div>
  </div>
  </div>
  </div>
  </div>
  </div>

  </div>
  </div>
  </header>
  </div>
</body>

</html>
<!-- jQuery CDN - Slim version (=without AJAX) -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<!-- Popper.JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

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
  output$(function() {
    $('.datepicker').datepicker({
      language: "es",
      autoclose: true,
      format: "dd/mm/yyyy"
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