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


//load all of the DB Queries

$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");

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

  <link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css" type="text/css" media="all" />
  <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.min.js"></script>
  <script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript" src="js/demo.js"></script>



  <!-- datatables styles -->
  <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />

  <link rel="stylesheet" href="css/style.css">

  <title>SDBA</title>
</head>

<body data-spy="scroll" data-target="#main-nav">
  <?php //main nav menu

  echo $main_menus;
  ?>

  <!-- HOME SECTION -->

  <div class="home-section">
    <div class="subheader"></div>
    <!--Lets build the table-->
    <p class="fxdtext"><strong>H</strong>elp</p>

    <div class="fxdtextaltvp">

      <p class="h5 form-text-alt">Test Text</p>
    </div>


    1. Setup.
    2. Overview
    3. Getting started
    2. Setting the currency.
    3. Adding in market string
    3. Adding stock string.
    4. Adding rackets.
    5. Adding customers
    6. Adding jobs.

    1. Setup
    If you are reading this, you have probably already setup the webserver and configured your setup file.


    When you first start the stringer app you will see following screen layout.
    The layout is reposnsive so will change dependant on screen size.


    Anytime you select the logo in the top right you will be taken back to the home page.
    The main menu accross the top is responsive and will change depending on the screen size.

    The home page, knots, and this page are the only pages avaialable that do not require a login.
    <h4>Adding a job</h4>
    <p>Select jobs from the top menu. Once the page loads press the plus button in the bottom left corner</p>









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
          "bFilter": false,
          "bInfo": false,
          language: {
            'search': '',
            'searchPlaceholder': '',
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