<?php
if (((filesize("./Connections/wcba.php")) == 0) or (!file_exists("./Connections/wcba.php"))) {
  header("location:./db-config.php?code=1378907769354882");
}
require_once('./Connections/wcba.php');
require_once('./menu.php');
//-------------------------------------------------------------------
// Initialize the session
if (!isset($_SESSION)) {
  session_start();
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
  <title>CREative Restrings</title>
  <link rel="icon" type="image/png" href="./img/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="./img/favicon-16x16.png" sizes="16x16" />
</head>

<body data-spy="scroll" data-target="#main-nav" id="home">
  <?php //main nav menu
  echo $main_menus; ?>
  <div class="home-section diva">
    <div class="subheader"> </div>
    <p class="fxdtextb"><strong>H</strong>ome</p>
    <div class="container my-1  firstparaaltej">
      <div class="container  my-1 pb-3 px-1 firstparaej">
        <div class="container  px-1  pt-3 form-text" style="margin-top: 40px;">
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae duo sunt, unum facit. <b>Addidisti ad extremum etiam indoctum fuisse.</b> Bonum integritas corporis: misera debilitas. Magna laus. </p>
          <p>Quid sequatur, quid repugnet, vident. <b>Zenonem roges;</b> Sint ista Graecorum; <b>Duo Reges: constructio interrete.</b> </p>
          <ul>
            <li>Non est ista, inquam, Piso, magna dissensio.</li>
            <li>Hoc non est positum in nostra actione.</li>
            <li>Quaero igitur, quo modo hae tantae commendationes a natura profectae subito a sapientia relictae sint.</li>
            <li>Estne, quaeso, inquam, sitienti in bibendo voluptas?</li>
          </ul>
          <p>Hoc Hieronymus summum bonum esse dixit. Sed ad bona praeterita redeamus. Nemo igitur esse beatus potest. Venit ad extremum; Bork </p>
        </div>
      </div>
    </div>
  </div>

  </header>
  <footer id="main-footer">
    <div class="container">
      <div class="row">
        <div class="col text-center py-4">
          <h3><i>CRE<span class="text-danger">ative</span></i></h3>
          <span class="text-dark" id="year"></span>
          </p>
        </div>
      </div>
    </div>
  </footer>
  <script src="http://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
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