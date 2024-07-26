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
    <p class="fxdtextb"><strong>H</strong>elp</p>
    <div class="container my-1  firstparaaltej">
      <div class="container  my-1 pb-3 px-1 firstparaej">
        <div class="container  px-1  pt-3 form-text" style="margin-top: 40px;">
          <h5><strong>Contents:</strong></h5>
          <p>
          <ol>
            <li><a href="#">Installation and setup.</a></li>
            <li><a href="#ov">Overview.</a></li>
            <li><a href="#am">Adding in market string.</a></li>
            <li><a href="#as">Adding stock string.</a></li>
            <li><a href="#ar">Adding rackets.</a></li>
            <li><a href="#ac">Adding customers.</a></li>
            <li><a href="#aj">Adding jobs.</a></li>
            <li><a href="./knots.php">Knots.</a></li>

          </ol>
          </p><br>
          <h5 id="is">Installation and setup</h5>
          <p>
          <ul style="list-style: disc">
            <li>Copy all of the files and folders to the web installation folder on your server. This is normally "public_html" or something very similar.</li>

            <li>Create an empty MySQL database on your server using phpMyAdmin. Make a note of the DB name, user and password.</li>
            <li>
              Open up a web browser and navigate to your domain. The first page you should be greeted with is a database configuration page. Fill in the fields using the details you made a note of in the previous step. If this is the first time using the app tick the box to create the tables and insert sample data.
            </li>
            <li>
              When you first start the stringer app you will only be able to view certain pages.
              All other pages will require you to login before they can be viewed.
              Select login from the menu. The default login is admin and the password is $Admin001</li>
            <li>Click on the account icon from the main menu. Here you will see your account details. Change the password on the account by clicking reset password.</li>
            <li>
              Go to settings on the main menu. Set your currency and units to suits your location.
            </li>
            <li>
              Next go to settings and check that the reel lengths you have in stock are present in the list.
              Most have been added, but you may need to add more.
            </li>
            <li>
              The admin is a super user. You may wish to add another user that has less privileges. Go to settings "User accounts" to add more users and set the passwords.
            </li>
            <li>
              Click settings "Payment account details" These are the bank account details that will be printed on the label. These should reflect the account you wish to get paid into.
            </li>
            <li>
              Lastly set your domain name. This should be yourdomian.com and should not have any https prefixes. This ensures the QR code is setup properly on the label once its printed. If you have created sub domains on your hosting site, your domain name must reflect this.
            </li>

          </ul>



          <h5><span class="anchor" id="ov"></span>Overview</h5>
          <ul>
            <li>
              <div class="row">Click on jobs from the main menu and you will see following screen layout.</div>
              <div class="row my-2"><img src="./img/help1.png" width="300px"></div>
              <div class="row">The layout is responsive so will change dependant on screen size.
                The top bar (1) always has the menu bar. Clicking the logo will always take you back to the homepage.
                The main part of the page (2) is the content section.
                The footer section (3) is the information bar.</div>
              <div class="row my-2"><img src="./img/help2.png" width="300px"></div>

              Working from left to right, you will see a plus button (A). The functionality of this button will change depending on what page is loaded. The next button (B) is the warning/information alert. If it is flashing there is a system generated warning message. Click the exclamation mark and either close or clear the message window. Note: The button will continue to flash until you clear the message. The next circle along (C) shows the number of restrings for the current month. The next (D) is the total restings on the system. Next (E)is the amount of money owed. Clicking this will list jobs that still have outstanding payments due. The next (F) is the total amount earned from restrings.
            </li>
            <li>
              In order to add jobs into the database you will need to first add your customers, add your stock string and any in market string if needed.
            </li>
          </ul>


          <h5><span class="anchor" id="am"></span>Adding in market string</h5>
          <ul>
            <li>
              The in market string is purely a database of the strings that are readily available in the market. Some have been populated as a starting point, but you will need to add any string you use on a regular basis.
              Go to settings and click "In market string".
              Check that the strings you use are listed.
            </li>
            <li>If you do not see the strings you use, click the plus button in the bottom left.
              Enter a brand (Yonex, Head, or Wilson for example). Enter a type (BG80, Razor Soft or X-one for example). Select a sport from the dropdown.
              Add any notes for the string. You may wish to add notes on performance characteristics or gauge
            </li>
            Click submit and the string will be added to the database.
            </li>
          </ul>

          <h5><span class="anchor" id="as"></span>Adding stock string</h5>
          <ul>
            <li>
              Select "Stock string" from the main menu. Click the plus button to add a new reel of stock string. This is the string that you have available and ready to use. The first pull down is "Sport". Select the sport you are adding a string reel for.</li>
            <li>A new pulldown menu will now appear called "Base reel". Pull this menu down and select the market string (That you added in the previous section) that represents this reel. If you do not see the string in the list you will need to add a new in-market string. Hit the green plus button next to the pull down.
            </li>
            <li>
              Select the purchase price for the reel and how much you want to charge for each restring that uses this reel. Select the reel length from the drop down and the purchase date. Add any notes that relate to this reel.
              The last option is "Owner supplied" Some individuals may supply you with a reel of string. If this is the case select this option. It will tag the string as being "Owner supplied" You may also want to add a comment in the notes if this is the case to state who owns the string.
            </li>
            Click submit and the string will be added to the database.
            </li>
          </ul>

          <h5><span class="anchor" id="ar"></span>Adding rackets</h5>
          <ul>
            <li>
              This is a list of customer and in market rackets.
            </li>
            <li>If you do not see the rackets you use, click the plus button in the bottom left.
              Enter a brand (Yonex, Head, or Wilson for example). Enter a model
              Add any notes for the racket. You may wish to add notes on stringing patters or links to stringing patterns
            </li>
            Click submit and the racket will be added to the database.
            </li>
          </ul>

          <h5><span class="anchor" id="ac"></span>Adding a customer</h5>
          <ul>
            <li>
              This is a list of customers that you string for. You ca set their preferences for tension, pre-tension, string and racket plus add any notes that are relevant to the customer.
            </li>
            <li>
              To add a new customer click the plus in the bottom left. Complete all of the information relating to the customer and click submit. Note: Mobile and email are optional.
          </ul>



          <h5><span class="anchor" id="aj"></span>Adding jobs</h5>
          <ul>
            <li>
              You are now ready to add a new job to the database
            </li>
            <li>
              To add a job select jobs from the main menu. The table shows all of the jobs that have been requested.
              To get more information click on the restring no. This will show all details of the job plus any other jobs that the customer has previously requested.
            </li>
            <li>
              The table shows some data in red text. If a racket is still in the queue and has not been delivered to the customer it will show in red. Additionally if a job has not been paid for it will also show in red.
            </li>
            <li>
              To add a new job press the plus sign in the bottom left of the page. Select a customer from the dropdown, and the a sport.
              this should load any preferences that are stored in customer profile.
            </li>
            <li>
              Select which string is required for the main. The default is for the crosses to be the same, but this can be set independently. Once the job is submitted any string selections will automatically be deducted from the stock reels that were selected. If the job is deleted or edited the string selections will be automatically adjusted.
            </li>
            <li>
              Use the tension slider to select a tension for mains and crosses. And select a pre-stretch setting if required.
            </li>
            <li>
              Select a racket from the pull-down menu. You will also have the option to take a picture of the racket. This will be stored in the database. If your web server has "Imagemagick" installed, the image will automatically be optimised for storage.
            </li>
            <li>
              Select the dates that you received the racket and also the date for when the racket is required.
            </li>
            <li>
              Lastly select if a grip is required. This will add a grip cost onto the job. The grip cost can be edited in setting. If for whatever reason it is a free restring the box can be ticked. This will set the job cost to zero. Note: if a grip has been selected, this will still be charged. Finally click submit and the job will be added to the list.</li>
            <li>
              Once the job is complete a label can be printed using the label icon on the table. This will generate a PDF label with all of the job details and a QR code that links back to the customers home page. The PDF can be sent to any standard thermal label printer.
            </li>
          </ul>


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