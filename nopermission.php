<?php
require_once('./Connections/wcba.php');
require_once('./menu.php');

// Initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// SECURITY Helper: Escape output to prevent XSS
function e($string)
{
  return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

$current_month_text = date("F");
$current_month_numeric = date("m");
$current_year = date("Y");

// PERFORMANCE NOTE: 
// 4 database counting queries were removed from here because they are 
// not displayed anywhere on the error page, drastically improving load times.
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

  <title>SDBA - No Permission</title>
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
    <div class="home-section align-items-center diva">
      <div class="subheader"></div>

      <div class="firstparalogin col-md-6 offset-md-3">
        <div class="card button-colours text-center card-form">
          <div class="card-body">
            <h3 class="mt-3">Warning:</h3>

            <?php if (isset($_SESSION['error'])) { ?>
              <p class="text-warning"><?php echo e($_SESSION['error']); ?></p>
              <?php unset($_SESSION['error']); ?>
            <?php } else { ?>
              <p>You do not have permission to view this page</p>
            <?php  } ?>

            <a href="./index.php" class="btn modal_body btn-block mt-4">Return Home</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

  <script>
    $('#year').text(new Date().getFullYear());
    $('body').scrollspy({
      target: '#main-nav'
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