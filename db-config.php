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
    //This section creates and writes the DB connection script based on the parameters enetered in the form
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
      //This first section will create all of the tables.
      if ($_POST['setupdb'] == 1) {
        $conn = new mysqli($sn, $un, $pass, $db);
        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }
        $query_disable_checks = 'SET foreign_key_checks = 0';
        $query_result = mysqli_query($conn, $query_disable_checks);
        // Get the first table
        $show_query = 'Show tables';
        $query_result = mysqli_query($conn, $show_query);
        $row = mysqli_fetch_array($query_result);
        while ($row) {
          $query = 'DROP TABLE IF EXISTS ' . $row[0];
          $query_result = mysqli_query($conn, $query);
          // Getting the next table
          $show_query = 'Show tables';
          $query_result = mysqli_query($conn, $show_query);
          $row = mysqli_fetch_array($query_result);
        }
        $sql = "CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` int(3) NOT NULL,
  `email` varchar(100) NOT NULL,
  `active` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
        mysqli_query($conn, $sql);
        $sql = "CREATE TABLE `all_string` (
  `string_id` int(11) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `notes` varchar(500) NOT NULL,
  `type` varchar(50) NOT NULL,
  `sportid` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        mysqli_query($conn, $sql);
        $sql = "CREATE TABLE `customer` (
  `cust_ID` int(5) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Notes` varchar(500) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Mobile` varchar(20) NOT NULL,
  `pref_string` int(6) NOT NULL,
  `pref_stringc` int(6) NOT NULL,
  `tension` varchar(5) NOT NULL,
  `tensionc` varchar(5) NOT NULL,
  `prestretch` varchar(5) NOT NULL,
  `racketid` int(10) NOT NULL,
  `discount` int(3) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        mysqli_query($conn, $sql);
        $sql = "CREATE TABLE `grip` (
  `gripid` int(11) NOT NULL,
  `Price` varchar(6) NOT NULL,
  `type` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        mysqli_query($conn, $sql);
        $sql = "CREATE TABLE `rackets` (
  `racketid` int(7) NOT NULL,
  `manuf` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `pattern` varchar(255) NOT NULL,
  `sport` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        mysqli_query($conn, $sql);
        $sql = "CREATE TABLE `reel_lengths` (
  `reel_length_id` int(3) NOT NULL,
  `length` int(4) NOT NULL,
  `warning_level` int(4) NOT NULL,
  `sport` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        mysqli_query($conn, $sql);
        $sql = "CREATE TABLE `settings` (
  `id` int(5) NOT NULL,
  `description` varchar(20) NOT NULL,
  `value` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        mysqli_query($conn, $sql);
        $sql = "CREATE TABLE `sport` (
  `sportid` int(4) NOT NULL,
  `sportname` varchar(50) NOT NULL,
  `string_length_per_racket` int(3) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        mysqli_query($conn, $sql);
        $sql = "CREATE TABLE `string` (
  `stringid` int(5) NOT NULL,
  `stock_id` varchar(50) NOT NULL,
  `string_number` varchar(4) NOT NULL,
  `Owner_supplied` varchar(3) NOT NULL,
  `purchase_date` varchar(10) NOT NULL,
  `note` varchar(100) NOT NULL,
  `reel_no` int(6) NOT NULL,
  `reel_price` varchar(6) NOT NULL,
  `racket_price` varchar(5) NOT NULL,
  `empty` int(1) NOT NULL,
  `lengthid` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        mysqli_query($conn, $sql);
        $sql = "CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `image` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        mysqli_query($conn, $sql);
        $sql = "CREATE TABLE `stringjobs` (
  `job_id` int(10) NOT NULL,
  `customerid` int(10) NOT NULL,
  `stringid` int(10) NOT NULL,
  `stringidc` int(10) NOT NULL,
  `racketid` int(10) DEFAULT NULL,
  `collection_date` varchar(11) NOT NULL,
  `delivery_date` varchar(11) NOT NULL,
  `pre_tension` varchar(6) NOT NULL,
  `tension` varchar(2) NOT NULL,
  `tensionc` varchar(2) NOT NULL,
  `price` varchar(8) NOT NULL,
  `grip_required` varchar(3) DEFAULT NULL,
  `paid` varchar(7) NOT NULL,
  `delivered` varchar(3) NOT NULL,
  `comments` varchar(500) NOT NULL,
  `free_job` varchar(3) NOT NULL,
  `imageid` int(10) NOT NULL,
  `addedby` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        mysqli_query($conn, $sql);
        $p1 = '$2y$10$0cSH/MytCUStZG15UZrEGu.EHtZ5J54JnzNbe9ne0wMl89Mwps8EC';
        //lets insert all of the data
        $sql = "INSERT INTO `accounts` (`id`, `username`, `password`, `level`, `email`, `active`) VALUES
(1, 'admin', '" . $p1 . "', 1, 'email@email.com', 1)";
        mysqli_query($conn, $sql);
        $sql = "INSERT INTO `all_string` (`string_id`, `brand`, `notes`, `type`, `sportid`) VALUES
(1, 'Yonex', '0.68mm Gauge Excellent repulsion', 'BG80', 1),
(2, 'Yonex', '0.65mm Gauge Repulsion control and durability', 'Exbolt 65', 1),
(3, 'Yonex', '0.65mm Gauge Power, touch and durability', 'BG66 Ultimax', 1),
(4, 'Ashaway', '0.70mm Gauge Ultimate durability for club level players', 'Rally 21 Fire', 1),
(5, 'Yonex', '0.72mm Gauge & 0.61mm Maximum control and repulsion', 'Aerobite Boost', 1),
(7, 'Tecnifibre', 'Advantages of this string being precision and comfort. Particularly beneficial for heavy string breakers.', 'Razor Soft 18/1.20', 2),
(8, 'Tecnifibre ', ' Best options for experienced players looking for maximum control on full swings', 'Razor Soft 17/1.25', 2),
(9, 'Tecnifibre', 'Provides shock absorption and optimum comfort', 'X-One Biphase 17/1.24', 2),
(10, 'Head', ' The RIP CONTROL is designed for tournament or club players and offers maximum playability and control', 'Rip Control', 2),
(11, 'Head', '1.2m guague', 'Hawk', 2),
(12, 'Yonex', '0.66mm Gauge', 'Nanogy 98', 1),
(13, 'Ashaway', '0.69mm Gauge', 'Zymax 69 Fire', 1),
(14, 'Yonex', '0.70mm Gauge', 'BG65 Ti', 1),
(15, 'Prince', 'All round playability and value', 'Synthetic Gut w/Duraflex', 2),
(16, 'Tecnifibre', 'Comfort, power, feel', 'NRG2', 2),
(17, 'Babolat', 'Maximum comfort, power, feel, tension maintenance', 'Touch VS', 2),
(18, 'Wilson', 'Maximum possibilities, fewer trade-offs', 'Champion\'s Choice', 2),
(19, 'Solinco', 'Control', 'Tour Bite', 2),
(20, 'WeissCannon', 'Spin', 'Ultra Cable', 2),
(21, 'Wilson', 'Control', 'NXT', 2),
(23, 'Ashaway', '0.66mm Gauge Excellent feel an repulsion ', 'Zymax 66 Fire Power', 1),
(24, 'Yonex', '0.68mm Gauge Amplified power and sharp feel', 'BG80 Power', 1),
(26, 'Yonex ', '0.68 Guague ', 'Exbolt68', 1),
(27, 'Tecnifibre', '1.20mm Gauge', '305', 3),
(28, 'Tecnifibre', '1.15mm gauge', 'Dynamix', 3),
(29, 'Ashaway', '18 Gauge', 'PowerNick', 3)";
        mysqli_query($conn, $sql);
        $sql = "INSERT INTO `customer` (`cust_ID`, `Name`, `Notes`, `Email`, `Mobile`, `pref_string`, `pref_stringc`, `tension`, `tensionc`, `prestretch`, `racketid`, `discount`) VALUES
(1, 'Chris Jones', '', '', '', 2, 0, '25', '0', '0', 1, 0),
(2, 'James O\'Connor', '', '', '', 5, 0, '27', '0', '0', 2, 0),
(3, 'Ashley Smith', '', '', '', 1, 1, '30', '0', '0', 8, 0)";
        mysqli_query($conn, $sql);
        $sql = "INSERT INTO `grip` (`gripid`, `Price`, `type`) VALUES
(2, '5', 'Generic Grip')";
        mysqli_query($conn, $sql);
        $sql = "INSERT INTO `rackets` (`racketid`, `manuf`, `model`, `pattern`, `sport`) VALUES
(1, 'Victor', 'Thruster F', '', 1),
(2, 'Victor', 'Jetspeed S12', '', 1),
(3, 'Victor', 'Bravesword 1', '', 1),
(4, 'Yonex', '88D Pro', '', 1),
(5, 'Yonex', '88S Pro', '', 1),
(6, 'Yonex', 'Arcsabre', '', 1),
(7, 'Yonex', 'Astrox 77', '', 1),
(8, 'Yonex', '88S', '', 1),
(9, 'Yonex', 'Duora', '', 1),
(10, 'Apacs', 'Ziggler', '', 1),
(11, 'Ashaway', 'Quantum Q5', '', 1),
(12, 'Yonex', 'Arcsabre FD', '', 1),
(13, 'Ashaway', 'Vtc force', '', 1),
(14, 'Yonex', 'Voltric Z Force', '', 1),
(15, 'Carlton', 'Airstream', '', 1),
(16, 'Apacs', 'Wave 10', '', 1),
(17, 'Carlton', 'Attack 200', '', 1),
(18, 'Carlton', 'Fury', '', 1),
(19, 'Carlton', 'Kenesis XT', '', 1),
(20, 'Victor', 'Bravesword LYD', '', 1),
(21, 'Yonex', 'Astrox force', '', 1),
(22, 'Yonex', 'Cab 20', '', 1),
(23, 'Yonex', 'Duora Strike', '', 1),
(24, 'Yonex', 'Muscle power', '', 1),
(25, 'Yonex', 'Nano Flare', '', 1),
(26, 'Yonex', 'Nanoray 900', '', 1),
(27, 'Yonex', 'Nano Speed', '', 1),
(28, 'Yonex', 'Voltric 50 Neo', '', 1),
(29, 'Yonex', 'Z Force 2', '', 1),
(30, 'Yonex', 'Arcsabre 001', '', 1),
(31, 'Gosen', 'Gungnir 07R', '', 1),
(32, 'Victor', 'Bravesword 1800', 'https://dancewithbirdie.files.wordpress.com/2018/06/stringpattern.pdf', 1),
(33, 'Head', 'Heat 1G', '', 2),
(34, 'Wilson', 'Pro-Staff 61-90', '', 2),
(35, 'Head', 'Prestige pro 200', '', 2),
(36, 'Yonex', 'Ezone98', '', 2),
(37, 'Yonex', 'Astrox Smash', '', 1),
(38, 'Wilson ', 'Pro-Staff 90', '', 2),
(39, 'Apacs', 'Woven power', '', 1),
(40, 'Head', 'Extreme MPL 600', '', 2),
(41, 'Yonex', 'Astrox 100 ZZ', '', 1),
(42, 'Prince', 'Vortex Elite 600', '', 3),
(43, 'Generic', 'Tennis Racket', '', 2),
(44, 'Generic', 'Badminton Racket', '', 1),
(45, 'Generic', 'Squash Racket', '', 3)";
        mysqli_query($conn, $sql);
        $sql = "INSERT INTO `reel_lengths` (`reel_length_id`, `length`, `warning_level`, `sport`) VALUES
(1, 200, 16, 1),
(3, 110, 10, 3),
(4, 10, 1, 1),
(5, 12, 1, 2),
(6, 220, 16, 2),
(7, 9, 1, 3),
(8, 200, 15, 2)";
        mysqli_query($conn, $sql);
        $sql = "INSERT INTO `settings` (`id`, `description`, `value`) VALUES
(2, 'currency', '3'),
(3, 'length', 'm'),
(4, 'accname', 'John Doe'),
(5, 'accnum', '123456783'),
(6, 'scode', '00-11-22'),
(7, 'domain', 'yourdomain.com')";
        mysqli_query($conn, $sql);
        $sql = "INSERT INTO `sport` (`sportid`, `sportname`, `string_length_per_racket`, `image`) VALUES
(1, 'Badminton', 10, 'shuttle.png'),
(2, 'Tennis', 12, 'tennis.png'),
(3, 'Squash', 9, 'squash.png')";
        mysqli_query($conn, $sql);
        $sql = "INSERT INTO `string` (`stringid`, `stock_id`, `string_number`, `Owner_supplied`, `purchase_date`, `note`, `reel_no`, `reel_price`, `racket_price`, `empty`, `lengthid`) VALUES
(1, '1', '23', 'no', '05/05/2024', '', 1, '98', '17', 1, 1),
(2, '2', '20.5', 'no', '03/04/2023', '', 1, '114', '18', 1, 1),
(4, '4', '11.5', 'no', '05/04/2024', 'Ashaway Rally 21 Fire White', 1, '47', '15', 0, 1),
(8, '7', '1', 'yes', '12/06/2024', '', 1, '0', '12', 1, 5),
(9, '8', '2', 'yes', '12/06/2024', '', 1, '0', '12', 1, 5),
(10, '9', '1', 'yes', '12/06/2024', '', 1, '0', '12', 1, 5),
(17, '29', '0', 'no', '23/07/2024', '', 1, '125', '15', 0, 3),
(18, '17', '0', 'no', '24/07/2024', '', 1, '125', '15', 0, 8),
(19, '4', '1', 'yes', '01/07/2024', '', 2, '250', '30', 0, 1)";
        mysqli_query($conn, $sql);
        $sql = "INSERT INTO `stringjobs` (`job_id`, `customerid`, `stringid`, `stringidc`, `racketid`, `collection_date`, `delivery_date`, `pre_tension`, `tension`, `tensionc`, `price`, `grip_required`, `paid`, `delivered`, `comments`, `free_job`, `imageid`, `addedby`) VALUES
(1, 3, 4, 4, 8, '24/07/2024', '31/07/2024', '0', '30', '30', '15', '0', '0', '0', '', '0', 0, 1),
(2, 3, 19, 19, 8, '25/07/2024', '24/07/2024', '0', '30', '30', '30', '0', '0', '0', '', '0', 0, 1);
";
        mysqli_query($conn, $sql);
        //alter all of the tables to set the primary key
        $sql = "ALTER TABLE `accounts` ADD PRIMARY KEY (`id`)";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `all_string` ADD PRIMARY KEY (`string_id`)";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `customer`
  ADD PRIMARY KEY (`cust_ID`)";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `grip`
  ADD PRIMARY KEY (`gripid`)";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `images`
  ADD PRIMARY KEY (`id`)";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `rackets`
  ADD PRIMARY KEY (`racketid`)";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `reel_lengths`
  ADD PRIMARY KEY (`reel_length_id`)";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`)";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `sport`
  ADD PRIMARY KEY (`sportid`)";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `string`
  ADD PRIMARY KEY (`stringid`),
  ADD UNIQUE KEY `stringid` (`stringid`)";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `stringjobs`
  ADD PRIMARY KEY (`job_id`)";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `all_string`
  MODIFY `string_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `customer`
  MODIFY `cust_ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `grip`
  MODIFY `gripid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `rackets`
  MODIFY `racketid` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `reel_lengths`
  MODIFY `reel_length_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `settings`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `sport`
  MODIFY `sportid` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `string`
  MODIFY `stringid` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20";
        mysqli_query($conn, $sql);
        $sql = "ALTER TABLE `stringjobs`
  MODIFY `job_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1001";
        mysqli_query($conn, $sql);
      }
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
    <meta name="color-scheme" content="dark light" />
    <meta name="theme-color" media="(prefers-color-scheme: dark)" />
    <meta name="theme-color" media="(prefers-color-scheme: light)" />
  </head>


  <body data-spy="scroll" data-target="#main-nav" id="home">
    <div class="home-section diva">
      <div class="subheader"> </div>
      <p class="fxdtext"><strong>Database</strong>Setup</p>
      <div class="container my-1  firstparaaltej">
        <div class="container  my-1 pb-3 px-1 firstparaej">
          <div class="container  px-1  pt-3 form-text" style="margin-top: 40px;">
            <!-- db  modal -->
            <h5 class=" modal-title">Setup database</h5>
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
              <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" name="setupdb" value="1" id="setupdb">
                <label class="form-check-label form-text" for="setupdb">
                  Create database tables including sample data.<br>Warning: This will erase all current tables and data!
                </label>
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
    <script type="text/javascript">
      document.getElementById('themeSwitch').addEventListener('change', function(event) {
        (event.target.checked) ? document.body.setAttribute('data-theme', 'dark'): document.body.removeAttribute('data-theme');
      });
    </script>

    <script>
      var themeSwitch = document.getElementById('themeSwitch');
      if (themeSwitch) {
        initTheme(); // on page load, if user has already selected a specific theme -> apply it

        themeSwitch.addEventListener('change', function(event) {
          resetTheme(); // update color theme
        });

        function initTheme() {
          var darkThemeSelected = (localStorage.getItem('themeSwitch') !== null && localStorage.getItem('themeSwitch') === 'dark');
          // update checkbox
          themeSwitch.checked = darkThemeSelected;
          // update body data-theme attribute
          darkThemeSelected ? document.body.setAttribute('data-theme', 'dark') : document.body.removeAttribute('data-theme');
        };

        function resetTheme() {
          if (themeSwitch.checked) { // dark theme has been selected
            document.body.setAttribute('data-theme', 'dark');
            localStorage.setItem('themeSwitch', 'dark'); // save theme selection 
          } else {
            document.body.removeAttribute('data-theme');
            localStorage.removeItem('themeSwitch'); // reset theme selection 
          }
        };
      }
    </script>
    <script>
      var imgsrc = localStorage.getItem('themeSwitch');
      if (imgsrc == "dark") {
        document.getElementById("imglogo").src = "./img/logo-dark.png";
      } else {
        document.getElementById("imglogo").src = "./img/logo.png";

      }
    </script>
  </body>

  </html>
<?php  }
?>