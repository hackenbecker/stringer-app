<?php

//require_once('./menu.php');
//-------------------------------------------------------------------
// Initialize the session
if (!isset($_SESSION)) {
  session_start();
}
if ($_GET['code'] != 1378907769354882) {
  header("location:./index.php"); //Redirecting To the main page
} else {
  file_put_contents("./Connections/wcba.php", ""); {
    //----------------------------------------------------------------
    //---Section to update DB access-----------------------------------
    //----------------------------------------------------------------
    if (isset($_POST['submiteditdb'])) {
      $sn = $_POST['servername'];
      $un = $_POST['username'];
      $pass = $_POST['password'];
      $db = $_POST['dbname'];

      file_put_contents("./Connections/wcba.php", "");

      $fp = fopen('./Connections/wcba.php', 'w'); //opens file in append mode
      fseek($fp, 0, SEEK_END);
      fwrite($fp, '<?php ' . "\n");
      fwrite($fp, '$servername = "' . $sn . '"; ' . "\n");
      fwrite($fp, '$username = "' . $un . '"; ' . "\n");
      fwrite($fp, '$password = "' . $pass . '"; ' . "\n");
      fwrite($fp, '$dbname = "' . $db . '"; ' . "\n");
      fwrite($fp, '$conn = new mysqli($servername, $username, $password, $dbname);' . "\n");
      fwrite($fp, 'if ($conn->connect_error) {' . "\n");
      fwrite($fp, 'die("Connection failed: " . $conn->connect_error);}' . "\n");
      fclose($fp);

      $_SESSION['message'] = "Database access modified Successfully";
      //redirect back to the main page.
      header("location:./string-jobs.php"); //Redirecting To the main page
    }
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


    <div class="home-section diva">
      <div class="subheader"> </div>
      <p class="fxdtext"><strong>Database</strong>Setup</p>
      <div class="container my-1  firstparaaltej">
        <div class="container  my-1 pb-3 px-1 firstparaej">
          <div class="container  px-1  pt-3 form-text" style="margin-top: 40px;">


            <!-- db  modal -->
            <h5 class=" modal-title">Setup database connection details</h5>

            <form method="post" action="">

              <label>Server name</label>
              <div>
                <div class="container">
                  <div class="row">
                    <div class="col-12">
                      <input type="text" name="servername" class="form-control txtField">
                    </div>
                  </div>
                </div>
              </div>

              <label class="mt-2">Database name</label>
              <div>
                <div class="container">
                  <div class="row">
                    <div class="col-12">
                      <input type="text" name="dbname" class="form-control txtField">
                    </div>
                  </div>
                </div>
              </div>

              <label class="mt-2">Username</label>
              <div>
                <div class="container">
                  <div class="row">
                    <div class="col-12">
                      <input type="text" name="username" class="form-control txtField">
                    </div>
                  </div>
                </div>
              </div>

              <label class="mt-2">Password</label>
              <div>
                <div class="container">
                  <div class="row">
                    <div class="col-12">
                      <input type="password" name="password" class="form-control txtField">
                    </div>
                  </div>
                </div>
              </div>
              <div class="container mt-3">


                <input class="btn modal_button_submit" type="submit" name="submiteditdb" value="Save new database details">
            </form>

          </div>


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
<?php } ?>