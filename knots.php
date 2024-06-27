<?php require_once('./Connections/wcba.php');
require_once('./menu.php');
//-------------------------------------------------------------------
// Initialize the session
if (!isset($_SESSION)) {
  session_start();
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
  <link rel="stylesheet" href="css/style.css">
  <title>CREative restrings</title>
  <link rel="icon" type="image/png" href="./img/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="./img/favicon-16x16.png" sizes="16x16" />
</head>

<body data-spy="scroll" data-target="#main-nav" id="home">
  <?php //main nav menu

  echo $main_menus; ?>

  <!-- HOME SECTION -->
  <header style="padding-top: -10em;" id="home-section">

    <div class="home-inner container">

      <div class="container-fluid">
        <div class="row p-3">
          <div class="p-3 col-sm">


            <div style="padding-top:100px;">

              <h5>Parnell Knot Left</h5>
              <img src="./img/34.png" alt="Parnell Knot" width="300">
            </div>


            <div class="my-3">

              <h5>Double half hitch Knot Left</h5>
              <img src="./img/32.png" alt="Parnell Knot" width="300">
            </div>


            <div class="my-3">
              <h5>Starting Knot Left</h5>
              <img src="./img/starting-knot.png" alt="Parnell Knot" width="300">
            </div>






          </div>
          <div class="p-3 col-sm">


            <div class="my-3">
              <h5>Parnell Knot Right</h5>
              <img src="./img/34-mirr.png" alt="Parnell Knot" width="300">
            </div>


            <div class="my-3">
              <h5>Double half hitch Knot Right</h5>
              <img src="./img/32-mirr.png" alt="Parnell Knot" width="300">
            </div>


            <div class="my-3">
              <h5>Starting Knot Right</h5>
              <img src="./img/starting-knot-mirr.png" alt="Parnell Knot" width="300">
            </div>





          </div>





        </div>
      </div>
    </div>
    </div>

    < </header>






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