<?php
require_once('./Connections/wcba.php');
require_once('./menu.php');

// Initialize the session
if (!isset($_SESSION)) {
  session_start();
}

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
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">

  <title>SDBA - Knots</title>
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
    <p class="fxdtextb"><strong>K</strong>nots</p>
    <div class="container my-1 firstparaaltej">
      <div class="container my-1 pb-3 px-1 firstparaej">
        <div class="container px-1 pt-3 form-text" style="margin-top: 40px;">
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
          <span class="text-dark">Copyright &copy; <span id="year"></span></span>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

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

    // Set logo correctly on initial load
    var imgsrc = localStorage.getItem('themeSwitch');
    var initLogo = document.getElementById("imglogo");
    if (initLogo) {
      initLogo.src = (imgsrc === "dark") ? "./img/logo-dark.png" : "./img/logo.png";
    }
  </script>
</body>

</html>