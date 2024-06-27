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


  <div class="home-section diva">
    <div class="subheader"> </div>
    <p class="fxdtext"><strong>IN</strong>formation</p>
    <div class="container my-1  firstparaaltej">
      <div class="container  my-1 pb-3 px-1 firstparaej">
        <div class="container  px-1  pt-3 form-text" style="margin-top: 40px;">

          <p>For racket restrings please call 07769 354 882 <br />or email <a href="mailto:stringing@devizesbc.org.uk">stringing@devizesbc.org.uk </a><br />to discuss your requirements.</p>
          <p>Racket restrings can in certain circumstances be turned around in 24 hours, this does depend however on the string required.</p>
          <p><strong>We use a Head TE 3300 Electronic stringing machine.</strong></p>
          <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; - 6 point mounting system</p>
          <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; - Pre-stretch up to 20%</p>
          <p>&nbsp;</p>
          <p>Tennis or Squash strings are ordered as required or if supplying your own string there will be a labour charge of &pound;12 per racket.</p>
          <p>Rackets can be dropped off / collected either on a Thursday or Friday evening at Devizes Leisure Centre (In the main hall) but please call to arrange first. Alternatively rackets can be dropped of in Devizes at any other time.</p>
          <p><strong>Online payment details.</strong><br />Name: Christopher Eagleton<br />Acc No: 67843287<br />Sort Code: 60-83-71</p>
          <p>Email: <a href="mailto:stringing@devizesbc.org.uk">stringing@devizesbc.org.uk</a></p>
          <p>The strings currently stocked are</p>
          <ul>
            <li>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <strong>Ashaway Rally 21 Fire &pound;15</strong></li>
            <li><strong>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Yonex BG66 Ultimax &pound;17</strong></li>
            <li><strong>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Yonex BG80 &pound;17</strong></li>
            <li><strong>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Yonex Exbolt 65 &pound;18<br /><br /></strong></li>
          </ul>
          <p>Other strings are available but will vary in price and delivery to those stated above.</p>
          <p>&nbsp;</p>




        </div>
      </div>
    </div>
  </div>
  </header>


  <!-- CONTACT MODAL -->
  <div class="modal fade text-dark" id="contactModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Contact Us</h5>
          <button class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form>
            <div class="form-group">
              <label for="name">Name</label>
              <input type="text" class="form-control">
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" class="form-control">
            </div>
            <div class="form-group">
              <label for="message">Message</label>
              <textarea class="form-control"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary btn-block">Submit</button>
        </div>
      </div>
    </div>
  </div>


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