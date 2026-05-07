<?php
if (((filesize("./Connections/wcba.php")) == 0) or (!file_exists("./Connections/wcba.php"))) {
  header("location:./db-config.php?code=1378907769354882");
  exit;
}
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

// Fetch active strings (Excluding "String Generic")
$query_Recordset2 = "SELECT * FROM string 
                     LEFT JOIN all_string ON string.stock_id = all_string.string_id 
                     LEFT JOIN sport ON all_string.sportid = sport.sportid 
                     LEFT JOIN reel_lengths ON string.lengthid = reel_lengths.reel_length_id 
                     WHERE empty = '0' AND Owner_supplied = 'no' 
                     AND all_string.brand != 'String' 
                     AND all_string.type != 'Generic' 
                     ORDER BY string.stringid ASC";
$Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);

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

  <title>CREative Restrings</title>
  <link rel="icon" type="image/png" href="./img/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="./img/favicon-16x16.png" sizes="16x16" />
</head>

<body data-spy="scroll" data-target="#main-nav" id="home">

  <script>
    if (localStorage.getItem('themeSwitch') === 'dark') {
      document.body.setAttribute('data-theme', 'dark');
    }
  </script>

  <?php echo $main_menus; ?>

  <div class="home-section diva">
    <div class="subheader"> </div>
    <p class="fxdtextb"><strong>H</strong>ome</p>
    <div class="container my-1 firstparaaltej">
      <div class="container my-1 pb-3 px-1 firstparaej">
        <div class="container px-1 pt-3 form-text" style="margin-top: 40px;">
          <h3>Tennis, Squash and Badminton racket restringing.</h3>
          <p>For racket restrings please call 07769 354 882 <br />Or email <a href="mailto:stringing@devizesbc.org.uk">stringing@devizesbc.org.uk </a>to discuss your requirements.</p>
          <p>Racket restrings can in certain circumstances be turned around in 24 hours, this does depend however on the string required.</p>

          <div class="container">
            <strong class="py-2 mb-3">We use a Head TE 3300 Electronic stringing machine.</strong>
            <div class="row mt-3">
              <div class="col-6">
                <img src="./img/4503_source_1709658004.jpg" width="200px" alt="Head TE 3300 Stringing Machine">
              </div>
              <div class="col-6">
                <ul style="list-style: disc">
                  <li>6 point mounting system</li>
                  <li>Automatic brake system</li>
                  <li>Pre-stretch 5% - 20%</li>
                  <li>Tension increases by 0.2kg and 0.5lbs</li>
                </ul>
              </div>
            </div>
          </div>

          <p>Rackets can be dropped off / collected either on a Thursday or Friday evening at Devizes Leisure Centre (In the main hall) but please call to arrange first. Alternatively rackets can be dropped off in Devizes at any other time.</p>
          <p>Email: <a href="mailto:stringing@devizesbc.org.uk">stringing@devizesbc.org.uk</a></p>
          <p>The strings currently stocked are:</p>

          <ul>
            <?php
            if ($totalRows_Recordset2 > 0) {
              while ($row_Recordset2 = mysqli_fetch_assoc($Recordset2)) {
            ?>
                <li>
                  <strong>
                    <img class="imgsporticon m-0 p-0" src="./img/<?php echo e($row_Recordset2['image']); ?>" width="18" height="18" style="padding:0; margin:0">
                    <?php echo " " . e($row_Recordset2['brand']) . " " . e($row_Recordset2['type']) . " - £" . e($row_Recordset2['racket_price']); ?>
                  </strong>
                </li>
            <?php
              }
            } else {
              echo "<li>No stock strings currently available.</li>";
            }
            ?>
          </ul>

          <p>Other strings are available but will vary in price and delivery to those stated above. If you are supplying your own string there will be a labour charge of &pound;12 per racket.</p>
          <p>&nbsp;</p>
          <h5>Interested in using the "StringerDB" App? Download it and try it out here: <a href="https://github.com/hackenbecker/stringer-app" target="_blank">More information</a></h5>
          <p>&nbsp;</p>
        </div>
      </div>
    </div>
  </div>

  <footer id="main-footer">
    <div class="container">
      <div class="row">
        <div class="col text-center py-4">
          <h3><i>CRE<span class="text-danger">ative</span></i></h3>
          <p class="text-dark">Copyright &copy; <span id="year"></span></p>
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