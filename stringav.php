<?php require_once('Connections/wcba.php');
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
if ($_SESSION['level'] != 1) {
  header('Location: ./nopermission.php');
  exit;
}
//load all of the DB Queries
/*
Still to do
1. view / edit delete for string reels
2. view / edit delete for all_string
3. view / edit delete for rackets
4. view / edit delete for customers
5. Ticket printing
6. Customer front end?
7. Stringing patterns for rackets
8. view / edit delete for sports
9. view / edit delete for grips
*/
//-------------------------------------------------------
$query_Recordset2 = "SELECT * FROM all_string LEFT JOIN sport ON all_string.sportid = sport.sportid ORDER BY all_string.string_id ASC";
$Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));
$row_Recordset2 = mysqli_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);
//-------------------------------------------------------
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



<body id="home-section-results" data-spy="scroll" data-target="#main-nav">
  <?php //main nav menu
  echo $main_menus; ?>
  <!-- HOME SECTION -->
  <section id="home-section">
    <div>
      <div class="home-inner container">
        <!--Lets build the table-->
        <div class="container">
          <div class="p-3 row">
            <h4 class="pr-3">In market string</h4>
            <a href="./addamarketstring.php?marker=3" type="button" class="btn rounded-circle  btn-warning fa-solid fa-plus"></a>
          </div>
          <br>
          <div>
            <table id="tblUser" class="border:none; table table-sm  
 text-white">
              <thead>
                <tr style='text-align:center; background-color:#016e51; border-top: 1px solid #ffffff; border-bottom: 1px solid #ffffff'>
                  <th class="text-center">String ID.</th>
                  <th class="text-center">Name</th>
                  <th class="text-center">Length</th>
                  <th class="text-center d-none d-md-table-cell">Sport</th>
                  <th class="text-center"></th>
                  <th class="text-center"></th>
                </tr>
              </thead>
              <tbody>
                <?php
                do { ?>
                  <tr class="text-white" style="text-align:center; border-top: 1px solid #bbbbbb;">
                    <td style="text-align: center">
                      <h6 class="text-primary"><?php echo $row_Recordset2['string_id']; ?></h6>
                    </td>
                    <td><?php echo $row_Recordset2['brand'] . " " . $row_Recordset2['type']; ?></td>
                    <td><?php echo $row_Recordset2['length'] . "m"; ?></td>
                    <td class="d-none d-md-table-cell"><?php echo $row_Recordset2['sportname']; ?></td>
                    <td style="text-align: center"><a class="text-primary fa-solid fa-pen-to-square" href="./editjob.php?jobid=<?php echo $row_Recordset2['string_id']; ?>"></i></td>
                    <td style="text-align: center"><i class="text-warning fa-solid fa-trash-can" data-toggle="modal" data-target="#delModal<?php echo $row_Recordset2['string_id']; ?>"></i></td>
                  </tr>
                <?php
                } while ($row_Recordset2 = mysqli_fetch_assoc($Recordset2)); ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <?php
    ?>
    </div>
  </section>
  <br><br> <br><br> <br><br>
  <!-- FOOTER -->
  <?php echo $footer; ?>
  <!-- Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script type="text/javascript" src="./js/theme.js"></script>

  <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
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
        order: [
          [0, 'asc']
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
  <script type="text/javascript">
    document.getElementById('themeSwitch').addEventListener('change', function(event) {
      (event.target.checked) ? document.body.setAttribute('data-theme', 'dark'): document.body.removeAttribute('data-theme');
    });
  </script>
  <script>
    var themeSwitch = document.getElementById('themeSwitch');
    if (themeSwitch) {
      themeSwitch.addEventListener('change', function(event) {
        resetTheme(); // update color theme
      });

      function resetTheme() {
        if (themeSwitch.checked) { // dark theme has been selected
          document.body.setAttribute('data-theme', 'dark');
          document.getElementById("imglogo").src = "./img/logo-dark.png";
          localStorage.setItem('themeSwitch', 'dark'); // save theme selection 
        } else {
          document.body.removeAttribute('data-theme');
          document.getElementById("imglogo").src = "./img/logo.png";
          localStorage.removeItem('themeSwitch'); // reset theme selection 
        }
      };
    }
  </script>
  <script>
    var themeSwitch = document.getElementById('themeSwitch');
    if (themeSwitch) {
      initTheme(); // on page load, if user has already selected a specific theme -> apply it
      document.getElementById("imglogo").src = "./img/logo-dark.png";

      themeSwitch.addEventListener('change', function(event) {
        resetTheme(); // update color theme
      });

      function initTheme() {
        var darkThemeSelected = (localStorage.getItem('themeSwitch') !== null && localStorage.getItem('themeSwitch') === 'dark');
        // update checkbox
        themeSwitch.checked = darkThemeSelected;
        // update body data-theme attribute
        darkThemeSelected ? document.body.setAttribute('data-theme', 'dark') : document.body.removeAttribute('data-theme');
      };

      function resetTheme() {
        if (themeSwitch.checked) { // dark theme has been selected
          document.body.setAttribute('data-theme', 'dark');
          document.getElementById("imglogo").src = "./img/logo-dark.png";
          localStorage.setItem('themeSwitch', 'dark'); // save theme selection 
        } else {
          document.body.removeAttribute('data-theme');
          document.getElementById("imglogo").src = "./img/logo.png";
          localStorage.removeItem('themeSwitch'); // reset theme selection 
        }
      };
    }
  </script>
  <script>
    var imgsrc = localStorage.getItem('themeSwitch');
    if (imgsrc == "dark") {
      document.getElementById("imglogo").src = "./img/logo-dark.png";
    } else {
      document.getElementById("imglogo").src = "./img/logo.png";

    }
  </script>
</body>

</html>