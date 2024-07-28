<?php require_once('./Connections/wcba.php');
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
  <title>SDBA</title>
  <link rel="icon" type="image/png" href="./img/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="./img/favicon-16x16.png" sizes="16x16" />
</head>

<body data-spy="scroll" data-target="#main-nav">
  <?php //main nav menu
  echo $main_menus;
  ?>
  <!-- HOME SECTION -->
  <div class="home-section diva">
    <div class="subheader"> </div>
    <p class="fxdtextb"><strong>K</strong>nots</p>
    <div class="container my-1  firstparaaltej">
      <div class="container  my-1 pb-3 px-1 firstparaej">
        <div class="container  px-1  pt-3 form-text" style="margin-top: 40px;">
          <div class="row">
            <div class="col-6">
              <div class="my-3">
                <h5>Double half hitch Knot Left</h5>
                <img class="center" src="./img/double-half-hitch-left.png" alt="double half hitch Knot" width="150">
              </div>
            </div>
            <div class="col-6">
              <div class="my-3">
                <h5>Double half hitch Knot Right</h5>
                <img class="center" src="./img/double-half-hitch-right.png" alt="double half hitch Knot" width="150">
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-6">
              <div class="my-3">
                <h5>Parnell Knot Left</h5>
                <img src="./img/parnell-left.png" alt="Parnell Knot" width="150">
              </div>
            </div>
            <div class="col-6">
              <div class="my-3">
                <h5>Parnell Knot Right</h5>
                <img src="./img/parnell-right.png" alt="Parnell Knot" width="150">
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-6">
              <div class="my-3">
                <h5>Euro Knot Left</h5>
                <img src="./img/euro-left.png" alt="Euro Knot" width="150">
              </div>
            </div>
            <div class="col-6">
              <div class="my-3">
                <h5>Euro Knot Right</h5>
                <img src="./img/euro-right.png" alt="Euro Knot" width="150">
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-6">
              <div class="my-3">
                <h5>Starting Knot<br>Left</h5>
                <img src="./img/starting-knot-2-left.png" alt="Starting Knot" width="150">
              </div>
            </div>
            <div class="col-6">
              <div class="my-3">
                <h5>Starting Knot<br>Right</h5>
                <img src="./img/starting-knot-2-right.png" alt="Starting Knot" width="150">
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-6">
              <div class="my-3">
                <h5>Wilson Pro Knot<br>Left</h5>
                <img src="./img/wilson-pro-left.png" alt="Starting Knot" width="150">
              </div>
            </div>
            <div class="col-6">
              <div class="my-3">
                <h5>Wilson Pro Knot<br>Right</h5>
                <img src="./img/wilson-pro-right.png" alt="Starting Knot" width="150">
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-6">
              <div class="my-3">
                <h5>Starting Knot 2<br>Left</h5>
                <img src="./img/starting-knot-left.png" alt="Starting Knot" width="150">
              </div>
            </div>
            <div class="col-6">
              <div class="my-3">
                <h5>Starting Knot 2<br>Right</h5>
                <img src="./img/starting-knot-right.png" alt="Starting Knot" width="150">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
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