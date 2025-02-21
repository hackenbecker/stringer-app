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
  if (!file_exists('./Connections')) {
    mkdir('./Connections', 0777, true);
  }
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
  `tension` varchar(4) NOT NULL,
  `tensionc` varchar(4) NOT NULL,
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
(6, 'String', 'Use this for owner supplied or unknown string', 'Generic', 1),
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
(21, 'Wilson', 'Control', 'NXT', 2),
(23, 'Ashaway', '0.66mm Gauge Excellent feel an repulsion ', 'Zymax 66 Fire Power', 1),
(24, 'Yonex', '0.68mm Gauge Amplified power and sharp feel', 'BG80 Power', 1),
(26, 'Yonex ', '0.68 Guague ', 'Exbolt68', 1),
(27, 'Tecnifibre', '1.20mm Gauge', '305', 3),
(28, 'Tecnifibre', '1.15mm gauge', 'Dynamix', 3),
(29, 'Ashaway', '18 Gauge', 'PowerNick', 3),
(805, 'Weiss Cannon', 'Gauge 1.23 mm Polyester', 'Ultra Cable 17 ', 2),
(806, 'Volkl', 'Gauge 1.3 mm Polyester', 'V-Square 16 ', 2),
(807, 'Weiss Cannon', '    Polyester', 'Blue Rock N Power', 2),
(808, 'Babolat', '    Polyester', 'RPM Blast Rough 17', 2),
(809, 'Tecnifibre', 'Gauge 1.25 mm Polyester', 'Black Code 4S 17 ', 2),
(810, 'Diadem', '    Polyester', 'Elite XT 17', 2),
(811, 'Tecnifibre', 'Gauge 1.3 mm Polyester', 'Black Code 4S 16 ', 2),
(812, 'Volkl', 'Gauge 1.15 mm Polyester', 'Cyclone 18L ', 2),
(813, 'Diadem', '    Polyester', 'Elite XT 16', 2),
(814, 'Volkl', 'Gauge 1.3 mm Polyester', 'Cyclone Tour 16 ', 2),
(815, 'Diadem', 'Gauge 1.2 mm Polyester', 'Solstice Power 17 ', 2),
(816, 'MSV', 'Gauge 1.27 mm Polyester', 'Focus-Hex 16 ', 2),
(817, 'Volkl', 'Gauge 1.3 mm Polyester', 'V-Torque Tour 16 ', 2),
(818, 'Weiss Cannon', 'Gauge 1.18 mm Polyester', 'Red Ghost 17L ', 2),
(819, 'Pacific', 'Gauge 1.3 mm Polyester', 'Poly Power Pro 16 ', 2),
(820, 'Luxilon', 'Gauge 1.25 mm Polyester', 'ECO Power 17 ', 2),
(821, 'Volkl', 'Gauge 1.1 mm Polyester', 'Cyclone 19 ', 2),
(822, 'Babolat', '    Polyester', 'RPM Blast Rough 16', 2),
(823, 'Diadem', 'Gauge 1.3 mm Polyester', 'Solstice Power 16 ', 2),
(824, 'Prince', 'Gauge 1.3 mm Polyester', 'Vortex 16 ', 2),
(825, 'Solinco', 'Gauge 1.25 mm Polyester', 'Revolution 16L ', 2),
(826, 'Volkl', 'Gauge 1.2 mm Polyester', 'Cyclone Tour 18 ', 2),
(827, 'MSV', 'Gauge 1.25 mm Polyester', 'Focus-Hex +38 16L ', 2),
(828, 'Wilson', 'Gauge 1.25 mm Polyester', 'Revolve 17 ', 2),
(829, 'Double', 'Gauge 1.31 mm Polyester', 'AR Diablo ', 2),
(830, 'Yonex', 'Gauge 1.25 mm Polyester', 'Poly Tour Drive 16L ', 2),
(831, 'Gosen', 'Gauge 1.24 mm Nylon', 'AK Control 17 ', 2),
(832, 'MSV', 'Gauge 1.23 mm Polyester', 'Focus-Hex 16 ', 2),
(833, 'Kirschbaum', '    Polyester', 'Spiky Shark 16', 2),
(834, 'Solinco', 'Gauge 1.15 mm Polyester', 'Hyper-G 18 ', 2),
(835, 'Head', 'Gauge 1.25 mm Polyester', 'Hawk Power 17 ', 2),
(836, 'Tecnifibre', 'Gauge 1.25 mm Polyester', 'Razor Soft 17 ', 2),
(837, 'Double', 'Gauge 1.24 mm Polyester', 'AR Diablo ', 2),
(838, 'Kirschbaum', '    Polyester', 'Spiky Shark 17', 2),
(839, 'Babolat', '    Polyester', 'RPM Blast Rough 15L', 2),
(840, 'Signum Pro', 'Gauge 1.3 mm Polyester', 'Plasma HEXtreme 16 ', 2),
(841, 'Double', 'Gauge 1.25 mm Polyester', 'AR WTS 25 ', 2),
(842, 'Solinco', 'Gauge 1.3 mm Polyester', 'Hyper-G 16 Round ', 2),
(843, 'MSV', 'Gauge 1.2 mm Polyester', 'Focus Hex Soft 17L ', 2),
(844, 'Diadem', 'Gauge 1.25 mm Polyester', 'Solstice Pro 16L ', 2),
(845, 'Diadem', 'Gauge 1.25 mm Polyester', 'Solstice Power 16L ', 2),
(846, 'Solinco', 'Gauge 1.15 mm Polyester', 'Tour Bite Soft 18 ', 2),
(847, 'Babolat', 'Gauge 1.35 mm Polyester', 'RPM Blast 15L ', 2),
(848, 'Yonex', 'Gauge 1.25 mm Polyester', 'Polytour Rev 16L ', 2),
(849, 'Double', 'Gauge 1.3 mm Polyester', 'AR Twice Dragon', 2),
(850, 'Solinco', 'Gauge 1.1 mm Polyester', 'Tour Bite 19 ', 2),
(851, 'Wilson', 'Gauge 1.3 mm Polyester', 'Revolve 16 ', 2),
(852, 'MSV', 'Gauge 1.18 mm Polyester', 'Co-Focus 17L ', 2),
(853, 'Wilson', 'Gauge 1.35 mm Nylon', 'NXT Max 15L ', 2),
(854, 'Diadem', 'Gauge 1.35 mm Polyester', 'Solstice Pro 15L ', 2),
(855, 'Gamma', 'Gauge 1.25 mm Polyester', 'Ocho 17 ', 2),
(856, 'Head', 'Gauge 1.25 mm Polyester', 'Lynx Tour 17 ', 2),
(857, 'Volkl', '    Polyester', 'Cyclone 18', 2),
(858, 'Kirschbaum', '    Polyester', 'Xplosive Speed 1.25', 2),
(859, 'Diadem', 'Gauge 1.35 mm Polyester', 'Solstice Power 15L ', 2),
(860, 'Gamma', 'Gauge 1.24 mm Polyester', 'Moto Soft 17 ', 2),
(861, 'Wilson', 'Gauge 1.35 mm Nylon', 'NXT 15L ', 2),
(862, 'Babolat', 'Gauge 1.3 mm Polyester', 'RPM Blast Orange 16 ', 2),
(863, 'Head', 'Gauge 1.3 mm Polyester', 'Lynx 16 ', 2),
(864, 'Solinco', '    Polyester', 'Hyper-G 16', 2),
(865, 'Luxilon', 'Gauge 1.3 mm Polyester', 'Element Rough 16 ', 2),
(866, 'IsoSpeed', '    Polyester', 'Pyramid 16', 2),
(867, 'Tecnifibre', 'Gauge 1.3 mm Nylon/Polyurethane', 'Multifeel 16 Black ', 2),
(868, 'Volkl', 'Gauge 1.3 mm Polyester', 'V-Star 16 ', 2),
(869, 'Solinco', '    Polyester', 'Tour Bite 16 Soft', 2),
(870, 'Luxilon', '    Polyester', 'Alu Power Soft 1.25', 2),
(871, 'Gamma', 'Gauge 1.3 mm Polyester', 'Ocho 16 ', 2),
(872, 'Tourna', '    Polyester', 'Big HItter Silver 16', 2),
(873, 'Kirschbaum', 'Gauge 1.2 mm Polyester', 'Max Power Rough 18 ', 2),
(874, 'Luxilon', 'Gauge 1.25 mm Polyester', 'LXN Smart 16L ', 2),
(875, 'L-Tec', 'Gauge 1.25 mm Polyester', 'Premium 4S 16L ', 2),
(876, 'Prince', 'Gauge 1.35 mm Polyester', 'Tour XR 15L ', 2),
(877, 'Gamma', 'Gauge 1.28 mm Polyester', 'Jet 16L ', 2),
(878, 'Prince', 'Gauge 1.25 mm Polyester', 'Tour XP 17 ', 2),
(879, 'Luxilon', '    Polyester', '4G Soft 1.25', 2),
(880, 'Solinco', '    Polyester', 'Tour Bite 16', 2),
(881, 'Luxilon', '    Polyester', 'Element 1.30', 2),
(882, 'Solinco', 'Gauge 1.15 mm Polyester', 'Outlast 18 ', 2),
(883, 'Gosen', 'Gauge 1.26 mm Polyester', 'Lumina Spin 16L ', 2),
(884, 'Solinco', '    Polyester', 'Revolution 17', 2),
(885, 'Pacific', 'Gauge 1.3 mm Polyester', 'XCite 16 ', 2),
(886, 'Solinco', 'Gauge 1.25 mm Polyester', 'Outlast 16L ', 2),
(887, 'Volkl', 'Gauge 1.1 mm Polyester', 'V-Star 19 ', 2),
(888, 'Luxilon', 'Gauge 1.25 mm Polyester', 'ALU Power Rough 16L ', 2),
(889, 'Tecnifibre', '    Polyester', 'Pro Red Code Wax 17', 2),
(890, 'MSV', 'Gauge 1.18 mm Polyester', 'Focus Hex 17L ', 2),
(891, 'Volkl', 'Gauge 1.15 mm Polyester', 'V-Star 18L ', 2),
(892, 'Gosen', 'Gauge 1.3 mm Nylon', 'AK Pro CX 16 ', 2),
(893, 'Gamma', 'Gauge 1.18 mm Polyester', 'IO 18 ', 2),
(894, 'Yonex', 'Gauge 1.25 mm Nylon', 'Rexis Comfort 16L ', 2),
(895, 'Prince', '    Polyester', 'Tour XT 18', 2),
(896, 'Solinco', '    Polyester', 'Revolution 16', 2),
(897, 'Babolat', '    Nylon', 'Origin 17', 2),
(898, 'Solinco', 'Gauge 1.3 mm Polyester', 'Confidential 16 ', 2),
(899, 'Head', 'Gauge 1.3 mm Polyester', 'Hawk 16 ', 2),
(900, 'LaserFibre', 'Gauge 1.28 mm Polyester', 'Vorso 16 ', 2),
(901, 'Gamma', 'Gauge 1.29 mm Polyester', 'Moto Soft 16 ', 2),
(902, 'Solinco', 'Gauge 1.16 mm Polyester', 'Revolution 18 ', 2),
(903, 'Tourna', '    Polyester', 'Big Hitter Black 7 17', 2),
(904, 'Solinco', '    Polyester', 'Tour Bite 17', 2),
(905, 'Signum Pro', 'Gauge 1.22 mm Polyester', 'Yellow Jacket 17g ', 2),
(906, 'Gamma', 'Gauge 1.24 mm Polyester', 'Moto 17 ', 2),
(907, 'Yonex', 'Gauge 1.25 mm Polyester', 'Poly Tour Spin 16L ', 2),
(908, 'Head', 'Gauge 1.2 mm Polyester', 'Hawk 18 ', 2),
(909, 'MSV', 'Gauge 1.25 mm Polyester', 'Focus Hex Soft 17 ', 2),
(910, 'Gamma', 'Gauge 1.4 mm Polyester', 'IO Soft 15L ', 2),
(911, 'Head', 'Gauge 1.2 mm Polyester', 'Lynx 18 ', 2),
(912, 'Signum Pro', 'Gauge 1.24 mm Polyester', 'X-Perience 17 ', 2),
(913, 'Head', 'Gauge 1.3 mm Polyester', 'Lynx Touch 16g ', 2),
(914, 'LaserFibre', 'Gauge 1.23 mm Polyester', 'Vorso 17 ', 2),
(915, 'Polyfibre', 'Gauge 1.25 mm Polyester', 'Black Venom Rough 16L ', 2),
(916, 'Klip', 'Gauge 1.2 mm Polyester', 'K-Boom 18 ', 2),
(917, 'Head', 'Gauge 1.25 mm Nylon', 'Synthetic Gut PPS 17 ', 2),
(918, 'Genesis', 'Gauge 1.29 mm Polyester', 'Black Magic 16 ', 2),
(919, 'Volkl', '    Polyester', 'Cyclone 16', 2),
(920, 'Gosen', 'Gauge 1.24 mm Nylon', 'AK Pro CX 17 ', 2),
(921, 'Y-Tex ', '    Polyester', 'Quadro Twist 16L', 2),
(922, 'Tourna', '    Nylon/Polyester', 'Synthetic Gut Armor 17', 2),
(923, 'Gamma', '    Polyester', 'Zo Tour Rough 16', 2),
(924, 'Gamma', 'Gauge 1.28 mm Polyester', 'IO Soft 16 ', 2),
(925, 'Solinco', 'Gauge 1.25 mm Polyester', 'Hyper-G 16L ', 2),
(926, 'Solinco', 'Gauge 1.2 mm Polyester', 'Hyper-G 17 ', 2),
(927, 'Volkl', 'Gauge 1.25 mm Polyester', 'Cyclone Tour 17 ', 2),
(928, 'Kirschbaum', 'Gauge 1.2 mm Polyester', 'Max Power 18 ', 2),
(929, 'Tecnifibre', 'Gauge 1.2 mm Polyester', 'Razor Code 18 ', 2),
(930, 'Head', 'Gauge 1.3 mm Polyester', 'Lynx Tour 16 ', 2),
(931, 'Gamma', '    Polyester', 'Zo Verve 17', 2),
(932, 'Wilson', 'Gauge 1.35 mm Polyester', 'Revolve 15 ', 2),
(933, 'Tecnifibre', 'Gauge 1.2 mm Polyester', 'Black Code 4S 18 ', 2),
(934, 'Tecnifibre', 'Gauge 1.32 mm Polyester', 'Black Code 15L ', 2),
(935, 'Volkl', 'Gauge 1.18 mm Polyester', 'V-Torque 18 ', 2),
(936, 'Tourna', '    Polyester', 'Black Zone 17', 2),
(937, 'Solinco', '    Polyester', 'Outlast 16', 2),
(938, 'Babolat', 'Gauge 1.25 mm Nylon', 'Synthetic Gut 17 ', 2),
(939, 'Kirschbaum', 'Gauge 1.3 mm Polyester', 'Max Power Rough 16 ', 2),
(940, 'Gosen', 'Gauge 1.38 mm Nylon', 'AK Pro Spin 15L ', 2),
(941, 'Signum Pro', 'Gauge 1.17 mm Polyester', 'Tornado 18 ', 2),
(942, 'Prince', 'Gauge 1.25 mm Polyester', 'Tour XR 17 ', 2),
(943, 'Signum Pro', '    Polyester', 'Plasma HEXtreme 16L/1.25', 2),
(944, 'Solinco', 'Gauge 1.2 mm Nylon', 'Vanquish 17 ', 2),
(945, 'Gamma', 'Gauge 1.23 mm Polyester', 'IO Soft 17 ', 2),
(946, 'Prince', '    Polyester', 'Tour XC 17L', 2),
(947, 'Gamma', 'Gauge 1.32 mm Polyester', 'Zo Verve 16 ', 2),
(948, 'Tourna', '    Nylon/Polyester', 'Synthetic Gut Armor 16', 2),
(949, 'Babolat', 'Gauge 1.3 mm Nylon', 'RPM Soft 16 ', 2),
(950, 'Kirschbaum', 'Gauge 1.3 mm Polyester', 'Max Power 16 ', 2),
(951, 'Solinco', 'Gauge 1.25 mm Polyester', 'Tour Bite 16L ', 2),
(952, 'Dunlop', 'Gauge 1.3 mm Polyester', 'Ice 16 ', 2),
(953, 'Solinco', 'Gauge 1.25 mm Polyester', 'Tour Bite Soft 16L ', 2),
(954, 'Topspin', 'Gauge 1.24 mm Polyester', 'Cyber Whirl 17 ', 2),
(955, 'Pacific', 'Gauge 1.25 mm Polyester', 'XCite 16L ', 2),
(956, 'Babolat', 'Gauge 1.3 mm Nylon', 'Synthetic Gut 16 ', 2),
(957, 'Signum Pro', 'Gauge 1.18 mm Polyester', 'Hyperion 18 ', 2),
(958, 'Gamma', '    Polyester', 'Moto 16', 2),
(959, 'Topspin', '    Polyester', 'Cyber Blue 16', 2),
(960, 'Solinco', 'Gauge 1.15 mm Polyester', 'Tour Bite 18 ', 2),
(961, 'Dunlop', 'Gauge 1.21 mm Polyester', 'Black Widow 18 ', 2),
(962, 'Yonex', 'Gauge 1.25 mm Polyester', 'Poly Tour Fire 16L ', 2),
(963, 'Solinco', 'Gauge 1.05 mm Polyester', 'Tour Bite 20 ', 2),
(964, 'Kirschbaum', 'Gauge 1.25 mm Polyester', 'Black Shark 17 ', 2),
(965, 'Double', 'Gauge 1.3 mm Nylon', 'AR 666 ', 2),
(966, 'Luxilon', '    Polyester', 'ALU Power 125/16L', 2),
(967, 'Tecnifibre', 'Gauge 1.33 mm Nylon/Polyester', 'Triax 16 ', 2),
(968, 'Gosen', 'Gauge 1.32 mm Polyester', 'Polylon Premium 16 ', 2),
(969, 'Volkl', '    Polyester', 'Cyclone 17', 2),
(970, 'Yonex', 'Gauge 1.25 mm Polyester', 'Poly Tour HS 16L ', 2),
(971, 'Tecnifibre', 'Gauge 1.24 mm Nylon/Polyurethane', 'HDX Tour 17 ', 2),
(972, 'Kirschbaum', '    Polyester', 'Pro Line II 1.30', 2),
(973, 'Wilson', 'Gauge 1.32 mm Nylon', 'NXT Duramax 16 ', 2),
(974, 'Gamma', 'Gauge 1.4 mm Nylon', 'TNT2 15L ', 2),
(975, 'Double', 'Gauge 1.25 mm Polyester', 'AR Twice Shark ', 2),
(976, 'Head', 'Gauge 1.25 mm Polyester', 'Lynx 17 ', 2),
(977, 'Solinco', 'Gauge 1.2 mm Polyester', 'Tour Bite Soft 17 ', 2),
(978, 'Kirschbaum', 'Gauge 1.25 mm Polyester', 'Super Smash 17 ', 2),
(979, 'Gamma', 'Gauge 1.23 mm Polyester', 'IO 17 ', 2),
(980, 'LaserFibre', 'Gauge 1.3 mm Nylon', '1200 16 ', 2),
(981, 'MSV', '    Polyester', 'Hepta-Twist 17', 2),
(982, 'Gamma', 'Gauge 1.4 mm Nylon', 'Marathon DPC 15 ', 2),
(983, 'Kirschbaum', '    Polyester', 'Xplosive Speed 1.30', 2),
(984, 'Gamma', 'Gauge 1.17 mm Nylon', 'TNT2 18 ', 2),
(985, 'Babolat', 'Gauge 1.3 mm Nylon', 'Origin 16 ', 2),
(986, 'Tecnifibre', 'Gauge 1.3 mm Polyester', 'Ice Code 16 ', 2),
(987, 'Signum Pro', '    Polyester', 'Poly-Plasma Pure 16L', 2),
(988, 'Tourna', '    Nylon/Polyester', 'Quasi-Gut Armor 17', 2),
(989, 'Tourna', '    Polyester', 'Black Zone 16', 2),
(990, 'Babolat', '    Nylon', 'SpiralTek 17', 2),
(991, 'Yonex', 'Gauge 1.25 mm Nylon', 'Rexis Speed 16L ', 2),
(992, 'Kirschbaum', 'Gauge 1.25 mm Polyester', 'Max Power Rough 17 ', 2),
(993, 'Volkl', '    Polyester', 'V-Torque 16', 2),
(994, 'Gamma', 'Gauge 1.18 mm Nylon', 'Synthetic Gut 18 ', 2),
(995, 'IsoSpeed', '    Polyester', 'Black Fire 17', 2),
(996, 'Topspin', 'Gauge 1.26 mm Polyester', 'Cyber Delta 17 ', 2),
(997, 'Tourna', 'Gauge 1.25 mm Polyester', 'Big HItter Black 7 16 ', 2),
(998, 'Weiss Cannon', 'Gauge 1.16 mm Polyester', 'Mosquito Bite 18 ', 2),
(999, 'Tecnifibre', 'Gauge 1.25 mm Polyester', 'Razor Code 17 ', 2),
(1000, 'Gosen', 'Gauge 1.24 mm Polyester', 'Poly Professional 17 ', 2),
(1001, 'Solinco', 'Gauge 1.25 mm Polyester', 'Barb Wire 16L ', 2),
(1002, 'Prince', '    Polyester', 'Tour XP 16', 2),
(1003, 'Wilson', 'Gauge 1.25 mm Nylon', 'Synthetic Gut Power 17 ', 2),
(1004, 'Luxilon', 'Gauge 1.25 mm Polyester', 'Element 16L ', 2),
(1005, 'Head', 'Gauge 1.25 mm Nylon', 'Velocity MLT 17 ', 2),
(1006, 'Boris Becker', 'Gauge 1.23 mm Polyester', 'Bomber NYC 17 ', 2),
(1007, 'Prince', '    Polyester', 'Tour XC 15L', 2),
(1008, 'Prince', '    Polyester', 'Tour XS 16', 2),
(1009, 'Prince', 'Gauge 1.3 mm Nylon', 'Lightning Pro 16 ', 2),
(1010, 'Wilson', 'Gauge 1.26 mm Nylon', 'NXT Power 17 ', 2),
(1011, 'Signum Pro', 'Gauge 1.3 mm Polyester', 'Firestorm 16 ', 2),
(1012, 'Prince', 'Gauge 1.3 mm Polyester', 'Tour XR 16 ', 2),
(1013, 'Gosen', '    Polyester', 'Polylon Comfort 16', 2),
(1014, 'Prince', '    Polyester', 'Tour XC 16L', 2),
(1015, 'Solinco', '    Polyester', 'Outlast 17', 2),
(1016, 'Tourna', '    Polyester', 'Big Hitter Silver 17', 2),
(1017, 'Kirschbaum', '    Polyester', 'Helix 16', 2),
(1018, 'Gosen', 'Gauge 1.27 mm Polyester', 'Polylon Premium 16L ', 2),
(1019, 'Tourna', '    Nylon', 'Irradiated 17', 2),
(1020, 'Kirschbaum', 'Gauge 1.275 mm Polyester', 'Touch Turbo 16L ', 2),
(1021, 'Prince', '    Nylon', 'Premier Control 16', 2),
(1022, 'Kirschbaum', 'Gauge 1.3 mm Polyester', 'Black Shark 16 ', 2),
(1023, 'RS ', '    Polyester', 'RS Lyon 17', 2),
(1024, 'Gosen', 'Gauge 1.29 mm Polyester', 'Poly Professional 16 ', 2),
(1025, 'Gamma', '    Polyester', 'Poly Z 16', 2),
(1026, 'Solinco', 'Gauge 1.25 mm Polyester', 'Tour Bite Diamond Rough 16L ', 2),
(1027, 'Gamma', 'Gauge 1.3 mm Nylon', 'Ocho TNT 16 ', 2),
(1028, 'Signum Pro', 'Gauge 1.23 mm Polyester', 'Poly Plasma 17 ', 2),
(1029, 'Dunlop', '    Polyester', 'Black Widow 17', 2),
(1030, 'Luxilon', '    Polyester', 'ALU Power Feel 18/1.20', 2),
(1031, 'Gosen', '    Nylon', 'A.K. Control 16', 2),
(1032, 'Kirschbaum', '    Polyester', 'Helix 17', 2),
(1033, 'Kirschbaum', 'Gauge 1.25 mm Polyester', 'Max Power 17 ', 2),
(1034, 'Double', '    Polyester', 'AR Raptor 1.27', 2),
(1035, 'Babolat', 'Gauge 1.25 mm Nylon/Polyester', 'RPM Team 17 ', 2),
(1036, 'Poly Star', 'Gauge 1.3 mm Polyester', 'Turbo 16 ', 2),
(1037, 'Yonex', '    Polyester', 'Poly Tour Tough 16L', 2),
(1038, 'Polyfibre', 'Gauge 1.3 mm Polyester', 'Panthera 16 ', 2),
(1039, 'Gosen', 'Gauge 1.3 mm Polyester', 'G-Tour 16 ', 2),
(1040, 'Gamma', 'Gauge 1.27 mm Nylon', 'TNT2 17 ', 2),
(1041, 'Kirschbaum', '    Polyester', 'Super Smash 16/1.30', 2),
(1042, 'Boris Becker', 'Gauge 1.28 mm Polyester', 'Bomber NYC 16 ', 2),
(1043, 'Babolat', 'Gauge 1.3 mm Nylon', 'Xalt 16 ', 2),
(1044, 'Kirschbaum', '    Polyester', 'Touch Turbo 1.30', 2),
(1045, 'Gamma', 'Gauge 1.25 mm Polyester', 'Zo Dart 17 ', 2),
(1046, 'Prince', '     Nylon', 'Topspin Plus 16', 2),
(1047, 'Pacific', 'Gauge 1.25 mm Polyester', 'Poly Power Pro 16L ', 2),
(1048, 'Wilson', '    Polyester', 'Ripspin 17', 2),
(1049, 'Babolat', '    Polyester', 'Pro Hurricane Tour 17', 2),
(1050, 'Babolat', '    Nylon', 'Xcel French Open Black 16', 2),
(1051, 'Polyfibre', 'Gauge 1.18 mm Polyester', 'Hexablade 17L ', 2),
(1052, 'Wilson', '    Polyester', 'Enduro Tour 16', 2),
(1053, 'Prince', '    Polyester', 'Tour XP 15L', 2),
(1054, 'Gamma', 'Gauge 1.25 mm Nylon', 'TNT2 Rx 17 ', 2),
(1055, 'Dunlop', '    Polyester', 'Black Widow 16', 2),
(1056, 'Kirschbaum', '    Polyester', 'P2 16/1.30', 2),
(1057, 'Gamma', 'Gauge 1.32 mm Nylon', 'Marathon DPC 16 ', 2),
(1058, 'Solinco', 'Gauge 1.25 mm Polyester', 'Hyper-G-Soft 16L ', 2),
(1059, 'Gamma', 'Gauge 1.32 mm Nylon', 'TNT2 Tour 16 ', 2),
(1060, 'Kirschbaum', 'Gauge 1.25 mm Polyester', 'Touch Turbo 17 ', 2),
(1061, 'Wilson', '    Polyester', 'Ripspin 16', 2),
(1062, 'Yonex', 'Gauge 1.2 mm Polyester', 'Poly Tour Pro 17 ', 2),
(1063, 'Volkl', '    Nylon', 'Synthetic Gut 17', 2),
(1064, 'Kirschbaum', 'Gauge 1.25 mm Polyester', 'Super Smash Spiky 17 ', 2),
(1065, 'Head', 'Gauge 1.2 mm Polyester', 'Hawk Touch 18 ', 2),
(1066, 'Tourna', '    Nylon', 'Quasi Gut 17', 2),
(1067, 'Signum Pro', 'Gauge 1.23 mm Polyester', 'Poly-Plasma Pure 17 ', 2),
(1068, 'Poly Star', 'Gauge 1.3 mm Polyester', 'Strike 16 ', 2),
(1069, 'Signum Pro', '    Polyester', 'Tornado 16', 2),
(1070, 'Solinco', 'Gauge 1.2 mm Nylon', 'X-Natural 17 ', 2),
(1071, 'Kirschbaum', '    Polyester', 'Super Smash Spiky 1.20', 2),
(1072, 'Topspin', '    Polyester', 'Cyber Blue 17', 2),
(1073, 'IsoSpeed', 'Gauge 1.12 mm Polyester', 'V18 19 ', 2),
(1074, 'Solinco', 'Gauge 1.35 mm Polyester', 'Tour Bite 15L ', 2),
(1075, 'Gamma', 'Gauge 1.25 mm Nylon', 'Synthetic Gut 17 ', 2),
(1076, 'Volkl', '    Polyester', 'V-Pro 17', 2),
(1077, 'Babolat', '    Polyester', 'Pro Hurricane Tour 16', 2),
(1078, 'Babolat', 'Gauge 1.2 mm Polyester', 'RPM Blast 18 ', 2),
(1079, 'Topspin', 'Gauge 1.2 mm Polyester', 'Cyber Blue 17L ', 2),
(1080, 'Volkl', '    Nylon', 'Synthetic Gut 16', 2),
(1081, 'Dunlop', 'Gauge 1.26 mm Polyester', 'Explosive 17 ', 2),
(1082, 'Gosen', '    Polyester', 'Polymaster II 16', 2),
(1083, 'One Strings', '    Polyester', 'Carbon NRG 16', 2),
(1084, 'RS', 'Gauge 1.2 mm Polyester', 'RS Lyon 17', 2),
(1085, 'Boris Becker', '    Polyester', 'Bomber 16', 2),
(1086, 'Kirschbaum', 'Gauge 1.3 mm Polyester', 'Pro Line X 16 ', 2),
(1087, 'Head', 'Gauge 1.3 mm Nylon', 'Reflex MLT 16 ', 2),
(1088, 'Volkl', '    Polyester', 'V-Pro 16', 2),
(1089, 'SuperString', '    Polyester', 'Viper V2 Rough 16L', 2),
(1090, 'Kirschbaum', '    Polyester', 'Super Smash Spiky 16/1.30', 2),
(1091, 'Gamma', 'Gauge 1.22 mm Nylon', 'Synthetic Gut 17 W/wearguard ', 2),
(1092, 'Yonex', 'Gauge 1.3 mm Polyester', 'Poly Tour HS 16 ', 2),
(1093, 'Prince', '    Nylon', 'Premier Power 18', 2),
(1094, 'Dunlop', 'Gauge 1.25 mm Polyester', 'Ice 17 ', 2),
(1095, 'Yonex', 'Gauge 1.3 mm Nylon', 'Mono Preme 16 ', 2),
(1096, 'Gamma', 'Gauge 1.25 mm Nylon', 'Ocho TNT 17 ', 2),
(1097, 'Tecnifibre', '    Polyester', 'Pro Red Code 18', 2),
(1098, 'Pacific', '    Polyester', 'Poly Force Xtreme 16L', 2),
(1099, 'Gamma', 'Gauge 1.3 mm Nylon', 'FXT 16 ', 2),
(1100, 'Poly Star', 'Gauge 1.25 mm Polyester', 'Turbo 16L ', 2),
(1101, 'Luxilon', 'Gauge 1.25 mm Polyester', 'Alu Power Vibe 16 ', 2),
(1102, 'Prince', '    Polyester', 'Tour XS 1.35', 2),
(1103, 'Signum Pro', 'Gauge 1.18 mm Polyester', 'Poly-Plasma 17L ', 2),
(1104, 'Wilson', '    Polyester', 'Revolve Spin 16', 2),
(1105, 'Dunlop', 'Gauge 1.2 mm Polyester', 'Explosive 18 ', 2),
(1106, 'Pacific', '    Polyester', 'ATP Poly Power Pro 16L', 2),
(1107, 'Kirschbaum', 'Gauge 1.25 mm Polyester', 'P2 17 ', 2),
(1108, 'Head', '    Polyester', 'Hawk Rough 17', 2),
(1109, 'Tecnifibre', '    Polyester', 'Pro Red Code 16', 2),
(1110, 'Kirschbaum', '    Polyester', 'Competition 16/1.30', 2),
(1111, 'Head', 'Gauge 1.15 mm Polyester', 'Hawk Touch 19 ', 2),
(1112, 'Solinco', 'Gauge 1.2 mm Polyester', 'Barb Wire 17 ', 2),
(1113, 'MSV', '    Polyester', 'Focus Evo 16', 2),
(1114, 'Wilson', 'Gauge 1.3 mm Nylon', 'Synthetic Gut Power 16 ', 2),
(1115, 'Wilson', '    Polyester', 'Ripspin 15', 2),
(1116, 'Solinco', 'Gauge 1.3 mm Polyester', 'Tour Bite Diamond Rough 16 ', 2),
(1117, 'Weiss Cannon', 'Gauge 1.28 mm Polyester', 'Scorpion 16L ', 2),
(1118, 'Volkl', '    Polyester', 'V-Pro 18', 2),
(1119, 'Tourna', '    Nylon/Polyester', 'Quasi-Gut Armor 16', 2),
(1120, 'Tourna', '    Polyester', 'Big Hitter Blue 17', 2),
(1121, 'Yonex', 'Gauge 1.3 mm Nylon', 'Tour Super 850 16 ', 2),
(1122, 'Kirschbaum', 'Gauge 1.25 mm Polyester', 'Pro Line Evolution 17 ', 2),
(1123, 'LaserFibre', 'Gauge 1.25 mm Polyester', 'Native Tour 17 ', 2),
(1124, 'Gosen', 'Gauge 1.2 mm Polyester', 'Polylon PolyBreak 18 ', 2),
(1125, 'Topspin', '    Polyester', 'Cyber Flash 16', 2),
(1126, 'Luxilon', '    Polyester', 'Big Banger Original 130/16', 2),
(1127, 'Signum Pro', 'Gauge 1.23 mm Polyester', 'Tornado 17 ', 2),
(1128, 'Mantis ', '    Polyester', 'Power Poly 17', 2),
(1129, 'Tecnifibre', 'Gauge 1.3 mm Polyester', 'Ruff Code 16 ', 2),
(1130, 'Gamma', 'Gauge 1.24 mm Nylon', 'FXT 17 ', 2),
(1131, 'Head', '    Polyester', 'Sonic Pro Edge 16', 2),
(1132, 'Gamma', '    Polyester', 'RZR Rx 17', 2),
(1133, 'Kirschbaum', 'Gauge 1.2 mm Polyester', 'P2 17L ', 2),
(1134, 'Kirschbaum', 'Gauge 1.25 mm Polyester', 'Pro Line X 17 ', 2),
(1135, 'Yonex', '    Polyester', 'Poly Tour Spin G 1.25', 2),
(1136, 'Klip', 'Gauge 1.27 mm Nylon', 'Scorcher 17 ', 2),
(1137, 'Signum Pro', 'Gauge 1.19 mm Polyester', 'Poly Megaforce 17 ', 2),
(1138, 'Wilson', 'Gauge 1.3 mm Nylon', 'Sensation Control 16 ', 2),
(1139, 'Weiss Cannon', 'Gauge 1.18 mm Polyester', 'Turbotwist 17L ', 2),
(1140, 'Poly Star', 'Gauge 1.25 mm Polyester', 'Strike 16L ', 2),
(1141, 'Wilson', 'Gauge 1.25 mm Nylon', 'Synthetic Gut Control 17 ', 2),
(1142, 'Dunlop', '    Polyester', 'Explosive 16', 2),
(1143, 'Signum Pro', '    Polyester', 'Firestorm 1.25', 2),
(1144, 'Tecnifibre', '    Polyester', 'Pro Red Code 17', 2),
(1145, 'Kirschbaum', 'Gauge 1.2 mm Polyester', 'Pro Line II 17L ', 2),
(1146, 'Mantis', '    Polyester', 'Comfort Poly 16', 2),
(1147, 'Wilson', 'Gauge 1.35 mm Nylon', 'Sensation 15 ', 2),
(1148, 'Wilson', '    Polyester', 'Enduro Tour 17', 2),
(1149, 'Weiss Cannon', 'Gauge 1.3 mm Nylon', '6 Star 16 ', 2),
(1150, 'Head', 'Gauge 1.25 mm Polyester', 'Hawk 17 ', 2),
(1151, 'IsoSpeed', '   Polyester', 'Pulse 16', 2),
(1152, 'Pacific', '   Polyester', 'X Force 18', 2),
(1153, 'Wilson', '   Polyester', 'Enduro Pro 16', 2),
(1154, 'Babolat', '   Polyester', 'Hurricane Feel 16', 2),
(1155, 'Gosen', '   Polyester', 'Polymaster I 16', 2),
(1156, 'Gamma', '   Polyester', 'Zo Twist 16', 2),
(1157, 'Signum Pro', '   Polyester', 'Poly-Plasma 16L', 2),
(1158, 'Kirschbaum', 'Gauge 1.25 mm Polyester', 'Pro Line II 17 ', 2),
(1159, 'Klip', '    Polyester', 'Hardcore 16/1.30', 2),
(1160, 'Babolat', '    Polyester', 'Ballistic Polymono 16', 2),
(1161, 'Babolat', 'Gauge 1.3 mm Nylon', 'M7 16 ', 2),
(1162, 'Head', 'Gauge 1.25 mm Nylon', 'Reflex MLT 17 ', 2),
(1163, 'Prince', '    Nylon', 'Premier Control 17', 2),
(1164, 'Gamma', 'Gauge 1.38 mm Nylon', 'Synthtic Gut 15L W/wearguard ', 2),
(1165, 'Weiss Cannon', 'Gauge 1.24 mm Polyester', 'Black 5 Edge 17 ', 2),
(1166, 'Gosen', '    Polyester', 'Sidewinder 17', 2),
(1167, 'Gosen', 'Gauge 1.3 mm Nylon', 'Tecflex 16 ', 2),
(1168, 'Ashaway', '    Nylon', 'Liberty 16', 2),
(1169, 'Babolat', '    Polyester', 'RPM Dual 16', 2),
(1170, 'Prince', '    Nylon', 'Premier Control 15', 2),
(1171, 'Gamma', 'Gauge 1.24 mm Nylon', 'TNT2 Pro Plus 17L ', 2),
(1172, 'Gosen', 'Gauge 1.3 mm Nylon', 'Multi CX 16 ', 2),
(1173, 'Gosen', 'Gauge 1.24 mm Polyester', 'Polylon SP 17 ', 2),
(1174, 'Luxilon', '    Polyester', 'Monotec Zolo 15L', 2),
(1175, 'Prince', '    Nylon', 'Premier Control 15L', 2),
(1176, 'Yonex', 'Gauge 1.25 mm Polyester', 'Poly Tour Strike 16L ', 2),
(1177, 'Babolat', '    Polyester', 'RPM Blast 17/1.25', 2),
(1178, 'Prince', '    Nylon', 'Premier Power 16', 2),
(1179, 'LaserFibre', 'Gauge 1.25 mm Polyester', 'JB Tour 100 17g ', 2),
(1180, 'Head', '  Nylon', 'FXP Power 16', 2),
(1181, 'Ashaway', '  Nylon', 'Synthetic Gut 16', 2),
(1182, 'Signum Pro', '  Polyester', 'Hyperion 16', 2),
(1183, 'Kirschbaum', '  Polyester', 'Long Life 15', 2),
(1184, 'Topspin', '  Polyester', 'Cyber Flash 17L', 2),
(1185, 'Tecnifibre', '  Polyester', 'Black Code 18', 2),
(1186, 'Gosen', '  Nylon/Polyurethane', 'TecGut Remplir 16', 2),
(1187, 'Polyfibre', 'Gauge 1.25 mm Polyester', 'Hexablade 16L ', 2),
(1188, 'Dunlop', '    Polyester', 'Comfort Poly 17', 2),
(1189, 'Head', '    Polyester', 'Ultra Tour 16', 2),
(1190, 'Kirschbaum', 'Gauge 1.15 mm Polyester', 'Pro Line II 18 ', 2),
(1191, 'Gosen', '    Nylon', 'Nanosilver 17', 2),
(1192, 'Pacific', '    Polyester', 'X Force 17', 2),
(1193, 'MSV', '    Polyester', 'Focus Evo 17', 2),
(1194, 'Kirschbaum', 'Gauge 1.25 mm Polyester', 'Competition 17 ', 2),
(1195, 'Babolat', '    Polyester', 'RPM Blast 16', 2),
(1196, 'Pacific', '    Polyester', 'Poly Force 16L', 2),
(1197, 'Gosen', '    Nylon', 'Compositemaster II 16', 2),
(1198, 'Alien', '    Polyester', 'Black Diamond 16', 2),
(1199, 'Head', '    Nylon', 'FXP Power 17', 2),
(1200, 'Wilson', 'Gauge 1.4 mm Nylon', 'NXT Duramax 15 ', 2),
(1201, 'Gamma', '    Nylon', 'TNT2 Rx 16', 2),
(1202, 'Pacific', '    Polyester', 'Poly Force 17', 2),
(1203, 'Gamma', 'Gauge 1.27 mm Nylon', 'TNT2 Tour 17 ', 2),
(1204, 'Tourna', '    Polyester', 'Big Hitter Silver Rough 16', 2),
(1205, 'Prince', 'Gauge 1.3 mm Nylon', 'Perfection 16 ', 2),
(1206, 'Kirschbaum', 'Gauge 1.15 mm Polyester', 'Pro Line I 18L ', 2),
(1207, 'Luxilon', 'Gauge 1.17 mm Polyester', 'Big Banger TiMO 17L ', 2),
(1208, 'Poly Star', 'Gauge 1.25 mm Polyester', 'Classic 16L ', 2),
(1209, 'Dunlop', 'Gauge 1.19 mm Nylon', 'S-Gut 18 ', 2),
(1210, 'Pacific', '    Gut', 'Prime Natural Gut 16', 2),
(1211, 'Prince', '    Polyester', 'Poly EXP 17', 2),
(1212, 'Pacific', 'Gauge 1.29 mm Polyester', 'X Force 16L ', 2),
(1213, 'Solinco', '    Polyester', 'Barb Wire 16', 2),
(1214, 'Polyfibre', 'Gauge 1.3 mm Polyester', 'TCS', 2),
(1215, 'Polyfibre', '    Polyester', 'Poly Hightec 16L', 2),
(1216, 'Volkl', '    Nylon', 'Gripper 17', 2),
(1217, 'Toalson ', 'Gauge 1.3 mm Nylon', 'Leoina 77 ', 2),
(1218, 'Babolat', '    Polyester', 'Hurricane Feel 17', 2),
(1219, 'Luxilon', '    Polyester', 'Big Banger Rough 130/16', 2),
(1220, 'Kirschbaum', '    Polyester', 'Pro Line I 1.30', 2),
(1221, 'Solinco', 'Gauge 1.3 mm Nylon', 'X-Natural 16 ', 2),
(1222, 'Ashaway', '    Nylon/Zyex', 'MonoGut ZX 16', 2),
(1223, 'Gosen', '   Nylon', 'AK Pro 17', 2),
(1224, 'Prince', '   Nylon', 'Premier Touch 15L', 2),
(1225, 'Wilson', '   Nylon', 'K-Gut 16', 2),
(1226, 'Polyfibre', '   Polyester', 'Hightec Premium 16L', 2),
(1227, 'Dunlop', '   Nylon', 'M-Fil Tour 16', 2),
(1228, 'Babolat', '   Nylon', 'Conquest 16', 2),
(1229, 'Solinco', 'Gauge 1.2 mm Polyester', 'Tour Bite Diamond Rough 17 ', 2),
(1230, 'Yonex', 'Gauge 1.32 mm Nylon', 'Tour Super Pro 850 16 ', 2),
(1231, 'Polyfibre', '    Polyester', 'TCS Rapid 16L/1.25', 2),
(1232, 'Tecnifibre', 'Gauge 1.3 mm Nylon/Polyester', 'HDX Tour 16 ', 2),
(1233, 'Gosen', '    Nylon', 'AK Pro 16', 2),
(1234, 'Polyfibre', 'Gauge 1.3 mm Polyester', 'Black Venom 16 ', 2),
(1235, 'Luxilon', '    Polyester', 'Big Banger ALU Power Spin 127/16', 2),
(1236, 'Gosen', 'Gauge 1.24 mm Nylon', 'Multi CX 17 ', 2),
(1237, 'Signum Pro', '    Polyester', 'Poly Megaforce 16', 2),
(1238, 'Prince', '    Nylon', 'Premier Power 17', 2),
(1239, 'L-Tec', '    Polyester', 'Premium Pro OS 16L', 2),
(1240, 'Signum Pro', '    Polyester', 'Poly Speed Spin 1.28', 2),
(1241, 'Gosen', 'Gauge 1.24 mm Polyester', 'Polylon 17 ', 2),
(1242, 'Head', 'Gauge 1.25 mm Nylon/Polyolefin', 'Rip Control 17 ', 2),
(1243, 'Tecnifibre', 'Gauge 1.3 mm Polyester', 'Razor Code 16 ', 2),
(1244, 'Prince', '   Nylon', 'Premier Touch 17', 2),
(1245, 'Wilson', '   Polyester', 'Spin Cycle 16L', 2),
(1246, 'Gosen', '   Nylon', 'Compositemaster I 16', 2),
(1247, 'Weiss Cannon', '   Polyester', 'Scorpion 1.33', 2),
(1248, 'Klip', '   Nylon', 'Synthetic Gut 16', 2),
(1249, 'Tecnifibre', '   Polyester', 'Black Code 17', 2),
(1250, 'Luxilon', '   Polyester', '4G Rough 16L', 2),
(1251, 'Polyfibre', '   Polyester', 'TCS 17', 2),
(1252, 'Polyfibre', '   Polyester', 'Hightec Premium 16/1.30', 2),
(1253, 'Gosen', 'Gauge 1.24 mm Polyester', 'Polylon PolyBreak 17 ', 2),
(1254, 'MSV', '    Polyester', 'Co.-Focus 16L', 2),
(1255, 'Yonex', '    Polyester', 'Poly Tour Air 16L', 2),
(1256, 'Topspin', '    Polyester', 'Cyber Flash 17', 2),
(1257, 'Poly Star', 'Gauge 1.3 mm Polyester', 'Classic 16 ', 2),
(1258, 'Gamma', '    Polyester', 'Zo Ice 16', 2),
(1259, 'Head', '    Polyester', 'Sonic Pro 16', 2),
(1260, 'Weiss Cannon', 'Gauge 1.24 mm Polyester', 'Turbotwist 17 ', 2),
(1261, 'Signum Pro', 'Gauge 1.24 mm Polyester', 'Hyperion 17 ', 2),
(1262, 'Head', '    Nylon/Polyester', 'FXP Tour 16', 2),
(1263, 'Polyfibre', '    Polyester', 'Poly Hightec 16/1.30', 2),
(1264, 'Gamma', '    Polyester', 'Zo Magic 16', 2),
(1265, 'Diadem', 'Gauge 1.25 mm Nylon', 'Impulse 17 ', 2),
(1266, 'Head', '    Nylon', 'FXP 17', 2),
(1267, 'Gosen', '    Polyester', 'Polylon 16', 2),
(1268, 'Head', '    Polyester', 'Ultratour 17', 2),
(1269, 'Babolat', '    Nylon', 'SG SpiralTek 16', 2),
(1270, 'L-Tec', 'Gauge 1.23 mm Polyester', 'Premium OS 17 ', 2),
(1271, 'Tourna', '    Polyester', 'Big Hitter Silver Rough 17', 2),
(1272, 'Luxilon', '    Polyester', 'Big Banger XP 125/16L', 2),
(1273, 'Kirschbaum', 'Gauge 1.25 mm Polyester', 'Pro Line I 17 ', 2),
(1274, 'Dunlop', '    Nylon', 'Tour Performance 16', 2),
(1275, 'Prince', '    Nylon', 'Premier Touch 16', 2),
(1276, 'Yonex', 'Gauge 1.3 mm Nylon', 'Rexis 16 ', 2),
(1277, 'Klip', '    Polyester', 'K-Boom 16/1.30', 2),
(1278, 'Dunlop', '    Nylon', 'Comfort Synthetic 16', 2),
(1279, 'Kirschbaum', '    Nylon', 'Touch Multifibre 16', 2),
(1280, 'Dunlop', 'Gauge 1.22 mm Nylon', 'DNA 17 ', 2),
(1281, 'Kirschbaum', 'Gauge 1.2 mm Polyester', 'Competition 17L ', 2),
(1282, 'Gosen', 'Gauge 1.3 mm Polyester', 'Polylon SP 16 ', 2),
(1283, 'Gamma', 'Gauge 1.27 mm Nylon', 'Live Wire XP 17 ', 2),
(1284, 'Weiss Cannon', '    Nylon', 'Explosiv 1.30', 2),
(1285, 'Gamma', 'Gauge 1.18 mm Polyester', 'Zo Black Ice 18 ', 2),
(1286, 'Wilson', 'Gauge 1.3 mm Gut', 'Natural Gut 16 ', 2),
(1287, 'Luxilon', 'Gauge 1.12 mm Polyester', 'Big Banger Ace 18 ', 2),
(1288, 'L-Tec', 'Gauge 1.25 mm Polyester', 'Premium 3S 16L ', 2),
(1289, 'Poly Star', 'Gauge 1.2 mm Polyester', 'Energy 17 ', 2),
(1290, 'Poly Star', 'Gauge 1.2 mm Polyester', 'Classic 17 ', 2),
(1291, 'Weiss Cannon', '    Polyester', 'Scorpion 1.22', 2),
(1292, 'Gosen', '    Nylon', 'Nanocubic 16', 2),
(1293, 'L-Tec', 'Gauge 1.25 mm Polyester', 'Premium 5S 16L ', 2),
(1294, 'Polyfibre', '    Polyester', 'Poly Hightec 17', 2),
(1295, 'Poly Star', 'Gauge 1.3 mm Polyester', 'Energy 16 ', 2),
(1296, 'Wilson', 'Gauge 1.25 mm Nylon/Polyester', 'Enduro Pro 17 ', 2),
(1297, 'Prince', '   Polyester', 'Beast XP 16', 2),
(1298, 'Babolat', '   Polyester', 'RPM Team 16 Black', 2),
(1299, 'Polyfibre', '   Polyester', 'Poly Hightec 18', 2),
(1300, 'Dunlop', '   Nylon', 'Synthetic Gut 16', 2),
(1301, 'Leopard', '   Polyester', 'Plus Control 16', 2),
(1302, 'Polyfibre', '   Polyester', 'Viper 17/1.20', 2),
(1303, 'Gamma', '   Nylon', 'Asterisk Spin 16', 2),
(1304, 'Luxilon', 'Gauge 1.25 mm Polyester', '4G 16L ', 2),
(1305, 'Luxilon', 'Gauge 1.22 mm Polyester', 'Big Banger Timo 17 ', 2),
(1306, 'Dunlop', 'Gauge 1.3 mm Nylon', 'Pearl 16 ', 2),
(1307, 'Polyfibre', '    Polyester', 'Hightec Premium 17', 2),
(1308, 'Gamma', '    Polyester', 'RZR Rx 16', 2),
(1309, 'Dunlop', 'Gauge 1.26 mm Polyester', 'Juice 17 ', 2),
(1310, 'Gamma', 'Gauge 1.27 mm Nylon', 'Live Wire 17 ', 2),
(1311, 'Solinco', '    Nylon', 'Vanquish 16', 2),
(1312, 'Wilson', '    Nylon', 'Hollow Core Pro 17', 2),
(1313, 'Gosen', '    Nylon', 'Powermaster I 16', 2),
(1314, 'Wilson', '    Nylon', 'NXT 16', 2),
(1315, 'Gamma', 'Gauge 1.22 mm Nylon', 'Professional 18 ', 2),
(1316, 'Kirschbaum', 'Gauge 1.2 mm Polyester', 'Pro Line I 17L ', 2),
(1317, 'Ashaway', '    Polyester', 'MonoGut 17', 2),
(1318, 'Head', '    Polyester', 'Sonic Pro 17', 2),
(1319, 'Head', '    Nylon', 'Synthetic Gut PPS 16', 2),
(1320, 'Weiss Cannon', '    Polyester', 'Silverstring 1.25', 2),
(1321, 'Gosen', 'Gauge 1.31 mm Polyester', 'Sidewinder 16 ', 2),
(1322, 'Gamma', '   ', 'Zo Tour 16', 2),
(1323, 'Babolat', '   Nylon', 'Xcel Power 16', 2),
(1324, 'Polyfibre', '   Polyester', 'TCS 16L', 2),
(1325, 'Gamma', '   Polyester', 'Monoblast 16', 2),
(1326, 'Prince', '   Nylon', 'Syn Gut Original 17', 2),
(1327, 'Tecnifibre', '   Polyester', 'Polyspin 16L', 2),
(1328, 'Mantis', '   Nylon', 'Comfort Synthetic 16', 2),
(1329, 'Babolat', '   Nylon', 'Super Fine Play 16', 2),
(1330, 'Head', '   Nylon/Polyester', 'FXP 16', 2),
(1331, 'Wilson', '   Nylon', 'NXT Tour 16', 2),
(1332, 'Gamma', '   Nylon', 'Asterisk 16', 2),
(1333, 'Poly Star', 'Gauge 1.25 mm Polyester', 'Energy 16L ', 2),
(1334, 'Prince', '    Polyester', 'Twisted 16L', 2),
(1335, 'Luxilon', '    Gut', 'Natural Gut 1.25', 2),
(1336, 'Tecnifibre', '    Nylon/Polyester', 'Pro Mix 17', 2),
(1337, 'Weiss Cannon', '    Polyester', 'Silverstring 120', 2),
(1338, 'Klip', '    Nylon', 'Kicker 16', 2),
(1339, 'Gamma', 'Gauge 1.25 mm Polyester', 'Zo Tour 17 ', 2),
(1340, 'Dunlop', '    Nylon', 'Hexy Fiber 17', 2),
(1341, 'Dunlop', 'Gauge 1.32 mm Nylon', 'S-Gut 16 ', 2),
(1342, 'Gamma', 'Gauge 1.23 mm Polyester', 'Zo Black Ice 17 ', 2),
(1343, 'Gamma', 'Gauge 1.27 mm Nylon', 'Revelation 17 ', 2),
(1344, 'Gosen', '    Nylon', 'Powermaster II 16', 2),
(1345, 'Wilson', '    Nylon', 'Sensation 17', 2),
(1346, 'Polyfibre', '    Polyester', 'Cobra 17/1.20', 2),
(1347, 'Prince', '    Nylon', 'Synthetic Gut 18 Duraflex', 2),
(1348, 'Wilson', '    Nylon', 'NXT Tour 17', 2),
(1349, 'Dunlop', 'Gauge 1.3 mm Nylon', 'DNA 16 ', 2),
(1350, 'Gamma', 'Gauge 1.27 mm Nylon', 'Ocho XP 17 ', 2),
(1351, 'Luxilon', 'Gauge 1.38 mm Polyester', 'Big Banger XP 15L ', 2),
(1352, 'Luxilon', '    Polyester', 'M2 Pro 125/16', 2),
(1353, 'Babolat', '    Nylon', 'Xcel Power 17', 2),
(1354, 'Dunlop', '    Nylon', 'Hexy Fiber 16', 2),
(1355, 'Wilson', 'Gauge 1.3 mm Nylon', 'Optimus 16 ', 2),
(1356, 'Gamma', '    Nylon', 'Synthetic Gut 16', 2),
(1357, 'Head', '    Nylon', 'Synthetic Gut 16', 2),
(1358, 'Luxilon', '    Polyester', 'Adrenaline 16', 2),
(1359, 'Dunlop', '    Nylon', 'Explosive Synthetic 16', 2),
(1360, 'Weiss Cannon', '    Polyester', 'MatchPower 1.25', 2),
(1361, 'Dunlop', 'Gauge 1.31 mm Polyester', 'Juice 16 ', 2),
(1362, 'Luxilon', 'Gauge 1.1 mm Polyester', 'Big Banger Timo 18 ', 2),
(1363, 'Dunlop', '    Nylon', 'Silk 17', 2),
(1364, 'Head', '    Nylon', 'Fibergel Spin 16', 2),
(1365, 'Prince', '     Nylon', 'Topspin W/ Duraflex 15L', 2),
(1366, 'Gamma', 'Gauge 1.27 mm Nylon', 'Solace 17 ', 2),
(1367, 'Gamma', '   Nylon', 'Prodigy 16', 2),
(1368, 'Prince', '   Nylon', 'Premier LT 18', 2),
(1369, 'Tecnifibre', '   Nylon', 'NRG2 17/1.24', 2),
(1370, 'Dunlop', '   Nylon', 'X-Life Synthetic 15L', 2),
(1371, 'Prince', '   Nylon', 'Lightning XX 17', 2),
(1372, 'Ashaway', '   Polyester', 'MonoGut 16L', 2),
(1373, 'Kirschbaum', 'Gauge 1.25 mm Nylon', 'Touch Multifibre 17 ', 2),
(1374, 'Pacific', '    Nylon', 'Power Line 17', 2),
(1375, 'Wilson', '    Nylon', 'Sensation 16', 2),
(1376, 'Luxilon', '    Polyester', 'Adrenaline 16L/1.25', 2),
(1377, 'Gamma', 'Gauge 1.32 mm Nylon', 'Solace 16 ', 2),
(1378, 'Forten', '    Nylon', 'Sweet 16', 2),
(1379, 'Pacific', '    Nylon', 'Power Line 16L', 2),
(1380, 'Dunlop', 'Gauge 1.3 mm Nylon', 'Silk 16 ', 2),
(1381, 'Prince', '    Nylon', 'Recoil 16', 2),
(1382, 'Prince', '    Nylon', 'Lightning XX 16', 2),
(1383, 'Dunlop', '    Nylon/Polyester', 'Explosive Poly Max 16', 2),
(1384, 'Gosen', '    Nylon', 'OG-Sheep Micro 16', 2),
(1385, 'Yonex', 'Gauge 1.3 mm Polyester', 'Poly Tour Pro Yellow 16 ', 2),
(1386, 'Gamma', 'Gauge 1.27 mm Nylon', 'TNT2 Touch 17 ', 2),
(1387, 'Gamma', '  Nylon', 'TNT2 Touch 16', 2),
(1388, 'Tourna', '  Polyester', 'Big Hitter Blue Rough 16', 2),
(1389, 'Luxilon', '  Polyester', 'Savage 16/1.27', 2),
(1390, 'Prince', '  Polyester', 'Tour 17', 2),
(1391, 'SuperString', '  Polyester', 'Nikita Original 1.25', 2),
(1392, 'Gamma', '  Nylon/Zyex', 'Asterisk Tour 16', 2),
(1393, 'Pacific', '  Nylon', 'Premium Power X 16L', 2),
(1394, 'Wilson', '  Nylon', 'Hollow Core 16', 2),
(1395, 'Luxilon', '  Polyester', 'Monotec Super Poly 1.25/16L', 2),
(1396, 'Tecnifibre', '  Nylon/Polyurethane', 'Multi-Feel 16', 2),
(1397, 'Ashaway', '  Nylon/Zyex', 'Dynamite WB 16', 2),
(1398, 'Luxilon', '  Polyester', 'Adrenaline Rough 16L/1.25', 2),
(1399, 'Prince', '  Nylon', 'Premier LT 17', 2),
(1400, 'Boris Becker', 'Gauge 1.32 mm Nylon', 'Pulse 16 ', 2),
(1401, 'Babolat', '  Polyester', 'Pro Hurricane 18', 2),
(1402, 'Prince', '  Nylon', 'Premier W/Softflex 16', 2),
(1403, 'Prince', '  Polyester', 'Poly Spin 3D', 2),
(1404, 'Prince', '  Nylon', 'Premier W/ Softflex 17', 2),
(1405, 'IsoSpeed', '  Polyester', 'Axon Mono 16L', 2),
(1406, 'Gosen', '  Nylon', 'OG Sheep Micro 17', 2),
(1407, 'Volkl', '  Nylon', 'Power-Fiber II 16', 2),
(1408, 'Klip', '  Nylon', 'Scorcher 16/1.30', 2),
(1409, 'Tecnifibre', '  Nylon', 'NRG2 16', 2),
(1410, 'Gamma', '  Nylon', 'Glide Cross String 16', 2),
(1411, 'Gosen', '  Nylon', 'OG Sheep Micro 18', 2),
(1412, 'Gamma', '  Nylon', 'TNT2 16', 2),
(1413, 'Pacific', '  Nylon', 'Power Twist 16L', 2),
(1414, 'Tourna', '  Polyester', 'Big Hitter Blue Rough 17', 2),
(1415, 'Wilson', '  Nylon', 'Extreme 16 Synthetic Gut', 2),
(1416, 'Gamma', '  Nylon', 'TNT2 Pro Plus 16', 2),
(1417, 'Babolat', '  Nylon', 'Addiction 16', 2),
(1418, 'Tecnifibre', '  Nylon/Polyurethane', 'TGV 16', 2),
(1419, 'Babolat', '  Nylon', 'FiberTour 16', 2),
(1420, 'Prince', '  Nylon', 'Tournament Nylon 15L', 2),
(1421, 'Head', '  Nylon', 'Fibergel Power 17', 2),
(1422, 'Prince', '  Nylon', 'Synthetic Gut Multifilament 17', 2),
(1423, 'Dunlop', '  Nylon/Polyurethane', 'S-Gut 17', 2),
(1424, 'Dunlop', 'Gauge 1.25 mm Nylon', 'Pearl 17 ', 2),
(1425, 'Wilson', '    Nylon', 'SGX 16', 2),
(1426, 'Prince', '    Polyester', 'Tour 16', 2),
(1427, 'Babolat', '    Polyester', 'Pro Hurricane 17', 2),
(1428, 'Alpha ', '    Nylon', 'Gut 2000 16', 2),
(1429, 'Pacific', 'Gauge 1.3 mm Gut', 'Prime Gut Orange Bull 16 ', 2),
(1430, 'Wilson', '    Nylon', 'Stamina 16', 2),
(1431, 'Tourna', '    Nylon', 'Irradiated 16', 2),
(1432, 'Wilson', '    Nylon', 'Sensation Supreme 16', 2),
(1433, 'IsoSpeed', '    Nylon/Polyolefin', 'Axon Multi 16L', 2),
(1434, 'Head', '    Nylon', 'Fibergel Power 16', 2),
(1435, 'Gamma', 'Gauge 1.32 mm Nylon', 'Ocho XP 16 ', 2),
(1436, 'Wilson', 'Gauge 1.32 mm Nylon/Polyester', 'NXT Control 16 ', 2),
(1437, 'Ashaway', '    Nylon/Zyex', 'Dynamite 17', 2),
(1438, 'Tecnifibre', '    Nylon/Polyurethane', 'TGV 17/1.25', 2),
(1439, 'Tecnifibre', '    Nylon/Polyurethane', 'XR3 17', 2),
(1440, 'Babolat', '    Nylon', 'Xcel 16', 2),
(1441, 'Babolat', 'Gauge 1.35 mm Gut', 'VS Touch 15L ', 2),
(1442, 'Wilson', '  Nylon', 'Red Alert 16', 2),
(1443, 'Prince', '  Nylon', 'Syn Gut Original 16', 2),
(1444, 'Luxilon', '  Polyester', 'Adrenaline 17/1.20', 2),
(1445, 'Gamma', '  Polyester', 'Zo Power 16L', 2),
(1446, 'Tecnifibre', '  Nylon/Polyurethane', 'XR3 16', 2),
(1447, 'Volkl', '  Nylon', 'Power-Fiber II 18', 2),
(1448, 'Gamma', '  Nylon', 'WearGuard Synthetic Gut 16', 2),
(1449, 'Pacific', '  Gut', 'Classic Natural Gut 16', 2),
(1450, 'Wilson', '  Nylon', 'Synthetic Gut Extreme 17', 2),
(1451, 'Gamma', '  Nylon', 'Professional 17', 2),
(1452, 'Volkl', '  Nylon', 'Power-Fiber II 17', 2),
(1453, 'Klip', '  Nylon', 'Excellerator 16/1.30', 2),
(1454, 'Babolat', '  Nylon/Polyurethane', 'Attraction 16', 2),
(1455, 'Gamma', '  Nylon/Zyex', 'Professional Spin 16', 2),
(1456, 'Tecnifibre', '  Nylon/Polyurethane', 'E-Matrix 16', 2),
(1457, 'Babolat', '  Gut', 'VS Natural ThermoGut 16 Touch', 2),
(1458, 'Gosen', '  Nylon', 'OG Sheep Micro Super 16L', 2),
(1459, 'Prince', '  Nylon', 'Synthetic Gut Multifilament 16', 2),
(1460, 'Tecnifibre', 'Gauge 1.18 mm Nylon/Polyurethane', 'NRG2 18 ', 2),
(1461, 'Gamma', '    Nylon', 'ESP 16', 2),
(1462, 'Gamma', '    Nylon/Zyex', 'Live Wire XP 16', 2),
(1463, 'Tecnifibre', 'Gauge 1.25 mm Nylon/Polyurethane', 'Multi-Feel 17 ', 2),
(1464, 'Gamma', '  Nylon/Zyex', 'Live Wire 16', 2),
(1465, 'Ashaway', '  Nylon/Zyex', 'Dynamite Soft 17', 2),
(1466, 'Head', '  Polyolefin', 'PerfectControl 16', 2),
(1467, 'Tecnifibre', '  Nylon/Polyester', 'Pro Mix 16', 2),
(1468, 'Head', '  Nylon/Polyolefin', 'RIP Control 16', 2),
(1469, 'Babolat', '  Nylon', 'Addiction 17', 2),
(1470, 'Wilson', '  Nylon', 'NXT OS', 2),
(1471, 'Wilson', '  Nylon', 'NXT Max 16', 2),
(1472, 'Tecnifibre', '  Nylon', 'E-Matrix 17', 2),
(1473, 'IsoSpeed', '  Nylon/Polyester', 'Energetic Plus 16', 2),
(1474, 'Wilson', '  Nylon', 'Reaction 16', 2),
(1475, 'Gamma', '  Nylon/Zyex', 'Professional 16', 2),
(1476, 'Wilson', '  Nylon', 'Shock Shield 16', 2),
(1477, 'Head', '  Nylon/Polyolefin', 'RIP PerfectPower 16', 2),
(1478, 'IsoSpeed', '  Polyolefin', 'Control Classic 16', 2),
(1479, 'Pacific', 'Gauge 1.25 mm Gut', 'Prime Gut Orange Bull 16L ', 2),
(1480, 'Klip', '  Nylon', 'Venom 16/1.30', 2),
(1481, 'Wilson', '  Gut', 'Natural Gut 17', 2),
(1482, 'Tecnifibre', '  Nylon/Polyester', 'Duramix HD 16', 2),
(1483, 'Wilson', '  Nylon', 'Hollow Core Pro 16', 2),
(1484, 'Tecnifibre', '  Nylon/Polyurethane', 'X-One Biphase 17', 2),
(1485, 'Luxilon', '  Polyester', 'Monotec Supersense 16L/1.25', 2),
(1486, 'Wilson', '  Nylon', 'NXT 17', 2),
(1487, 'Tecnifibre', '  Polyester', 'X-Code 16', 2),
(1488, 'Pacific', '  Nylon', 'PMX 16L', 2),
(1489, 'Klip', 'Gauge 1.25 mm Gut', 'Legend 17 ', 2),
(1490, 'Head', '  Nylon', 'ETS 16', 2),
(1491, 'Head', '  Nylon', 'ETS 17', 2),
(1492, 'IsoSpeed', '  Nylon/Polyolefin', 'Energetic 17', 2),
(1493, 'Pacific', '  Gut', 'Tough Gut 16', 2),
(1494, 'Tecnifibre', '  Nylon/Polyester', 'Duramix HD 17', 2),
(1495, 'Tecnifibre', '  Nylon', 'X-One Biphase 18', 2),
(1496, 'Wilson', '  Nylon', 'K-Gut Pro 16', 2),
(1497, 'IsoSpeed', '  Polyolefin', 'Professional Classic 17', 2),
(1498, 'Tecnifibre', '  Nylon/Polyurethane', 'X-One Biphase 16', 2),
(1499, 'Klip', '  Gut', 'Armour Pro 16/1.30', 2),
(1500, 'Wilson', '  Gut', 'Natural Gut 16', 2),
(1501, 'Klip', '  Gut', 'Legend 16 Uncoated', 2),
(1502, 'MSV', '  Polyester', 'Hepta-Twist 16', 2),
(1503, 'SuperString', '  Polyester', 'Nikita Soft 16L', 2),
(1504, 'Tourna', '  Nylon', 'Quasi-Gut 16', 2),
(1505, 'Volkl', 'Gauge 1.2 mm Polyester', 'V-Star 18 ', 2),
(1506, 'Diadem', 'Gauge 1.32 mm Nylon', 'Impulse 16 ', 2),
(1507, 'Babolat', 'Gauge 1.25 mm Gut', 'VS Natural Team Gut 17 ', 2),
(1508, 'Babolat', '    Nylon', 'Xcel Premium 17', 2),
(1509, 'Prince', '     Polyester', 'Tournament Poly 16', 2),
(1510, 'Super ', 'Gauge 1.2 mm Polyester', 'String Tour Players V3 17 ', 2),
(1511, 'One Strings', 'Gauge 1.25 mm Polyester', 'Carbon Tour 17 ', 2),
(1512, 'Boris Becker', '    Polyester', 'Bomber 17', 2),
(1513, 'Babolat', '    Gut', 'Tonic + Natural Gut 16', 2),
(1514, 'SuperString', '    Polyester', 'Pure Control V8 17', 2),
(1515, 'Polyfibre', 'Gauge 1.25 mm Polyester', 'Black Venom 16L ', 2),
(1516, 'Luxilon', 'Gauge 1.3 mm Polyester', 'LXN Smart 16 ', 2),
(1517, 'Kirschbaum', 'Gauge 1.3 mm Polyester', 'Flash 16 ', 2),
(1518, 'Dunlop', '    Nylon', 'Comfort Synthetic 17', 2),
(1519, 'Luxilon', 'Gauge 1.3 mm Polyester', '4G 16 ', 2),
(1520, 'Babolat', '    Polyester', 'Revenge 16', 2),
(1521, 'IsoSpeed', '    Nylon/Polyolefin', 'Control 16', 2),
(1522, 'Dunlop', 'Gauge 1.3 mm Polyester', 'Explosive Tour 16 ', 2),
(1523, 'Wilson', '    Polyester', 'Enduro Mono 16L', 2),
(1524, 'Yonex', 'Gauge 1.25 mm Polyester', 'Poly Tour Pro Yellow 16L ', 2),
(1525, 'Prince', '    Nylon', 'Synthetic Gut 16 Duraflex', 2),
(1526, 'Signum Pro', 'Gauge 1.18 mm Polyester', 'Poly-Plasma Pure 17L ', 2),
(1527, 'Babolat', '    Polyester', 'Pro Hurricane 16', 2),
(1528, 'Pacific', '    Nylon', 'PMX 17', 2),
(1529, 'Diadem', 'Gauge 1.25 mm Polyester', 'Solstice Pro 16L ', 2),
(1530, 'Tourna', '    Polyester', 'Big Hitter Blue 16', 2),
(1531, 'Yonex', '    Polyester', 'Poly Tour 130/16', 2),
(1532, 'Babolat', '    Polyester', 'Revenge 17', 2),
(1533, 'Diadem', 'Gauge 1.2 mm Polyester', 'Solstice Blace 17 ', 2),
(1534, 'Dunlop', '    Nylon', 'Explosive Synthetic Gut 17', 2),
(1535, 'IsoSpeed', '    Polyester', 'Pulse 17', 2),
(1536, 'Dunlop', 'Gauge 1.25 mm Nylon', 'Iconic All 17 ', 2),
(1537, 'Head', 'Gauge 1.3 mm Nylon', 'Velocity MLT Power 16 ', 2),
(1538, 'IsoSpeed', 'Gauge 1.28 mm Polyester', 'Cream 17 ', 2),
(1539, 'Gosen', '    Nylon', 'Nanoblend 17', 2),
(1540, 'Dunlop', 'Gauge 1.25 mm Polyester', 'Explosive Speed 17 ', 2),
(1541, 'Wilson', '    Polyester', 'Enduro Gold 16', 2),
(1542, 'Luxilon', '    Polyester', 'Big Banger ALU Power Fluoro 17', 2),
(1543, 'LaserFibre', 'Gauge 1.25 mm Nylon/Polyurethane', 'Supreme 2.0 17 ', 2),
(1544, 'Pacific', '    Gut', 'Prime Natural Gut 16L', 2),
(1545, 'Tecnifibre', 'Gauge 1.25 mm Polyester', 'Ruff Code 17 ', 2),
(1546, 'Pacific', '   Gut', 'Tough Gut 16L', 2),
(1547, 'Prince', '    Polyester', 'Tournament Poly 17', 2),
(1548, 'Wilson', '   Polyester', 'Enduro Tour 18', 2),
(1549, 'Tecnifibre', '   Polyester', 'Black Code 16', 2),
(1550, 'Volkl', '   Nylon', 'Gripper 16', 2),
(1551, 'Tourna', 'Gauge 1.3 mm Polyester', 'Big Hitter Silver 7 Tour 16 ', 2),
(1552, 'LaserFibre', '    Nylon', 'Supernatural Gut Pro Stock 16', 2),
(1553, 'Polyfibre', '    Polyester', 'Viper 16L/1.25', 2),
(1554, 'Tourna', '    Polyester', 'Big Hitter 16', 2),
(1555, 'Prince', '    Nylon', 'Synthetic Gut 17 Duraflex', 2),
(1556, 'Yonex', '    ', 'Tournament 80 Spin 15L-16', 2),
(1557, 'Babolat', '    Polyester', 'Duralast 16', 2),
(1558, 'Dunlop', 'Gauge 1.27 mm Polyester', 'Explosive Bite 17 ', 2),
(1559, 'Dunlop', 'Gauge 1.25 mm Nylon', 'Iconic Touch 17 ', 2),
(1560, 'Yonex', 'Gauge 1.3 mm Nylon', 'Dynawire 16 ', 2),
(1561, 'Polyfibre', 'Gauge 1.15 mm Polyester', 'TCS 18 ', 2),
(1562, 'Luxilon', '    Polyester', 'Monotec Zolo 125/16', 2),
(1563, 'Prince', '    Polyester', 'Diablo Prism 17', 2),
(1564, 'Yonex', 'Gauge 1.3 mm Polyester', 'PolyTour REV 16 ', 2),
(1565, 'Luxilon', '    Polyester', '4G S 15', 2),
(1566, 'Dunlop', 'Gauge 1.25 mm Nylon', 'Iconic Speed 17 ', 2),
(1567, 'Yonex', '    Nylon', 'Tournament 50 16L', 2),
(1568, 'Ashaway', '    Nylon/Zyex', 'Dynamite Soft 18', 2),
(1569, 'LaserFibre', 'Gauge 1.3 mm Nylon/Polyurethane', 'Supreme 2.0 16 ', 2),
(1570, 'Gamma', '    Nylon/Zyex', 'Revelation 16', 2),
(1571, 'Babolat', '    Polyester', 'Duralast 17', 2),
(1572, 'SuperString', '    Nylon/Polyester', 'VooDoo Tour V9 17', 2),
(1573, 'Luxilon', '    Polyester', 'M2 Plus 130/16', 2),
(1577, 'String', 'Use this for owner supplied or unknown string', 'Generic', 2),
(1578, 'String', 'Use this for owner supplied or unknown string', 'Generic', 3),
(1579, 'String', 'Use this for owner supplied or unknown string', 'Generic', 4)";


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
(8, 200, 15, 2),
(9, 12, 1, 4),
(10, 200, 16, 4)";


        mysqli_query($conn, $sql);
        $sql = "INSERT INTO `settings` (`id`, `description`, `value`) VALUES
(2, 'currency', '3'),
(3, 'length', 'm'),
(4, 'accname', 'John Doe'),
(5, 'accnum', '123456783'),
(6, 'scode', '00-11-22'),
(7, 'domain', 'yourdomain.com'),
(12, 'companyname', 'Acme Stringing Ltd'),
(13, 'address', '1 Acacia Avenue'),
(14, 'town', 'London'),
(15, 'county', 'UB5 6NY'),
(16, 'postcode', 'Middlesex'),
(17, 'email', 'stringing@stringing.com'),
(18, 'telephone', '555-555-5555'),
(18, 'weight', 'lbs')";

        mysqli_query($conn, $sql);
        $sql = "INSERT INTO `sport` (`sportid`, `sportname`, `string_length_per_racket`, `image`) VALUES
(1, 'Badminton', 10, 'shuttle.svg'),
(2, 'Tennis', 12, 'tennis.svg'),
(3, 'Squash', 9, 'squash.svg'),
(4, 'Racketball', 12, 'racketball.svg')";
        mysqli_query($conn, $sql);
        $sql = "INSERT INTO `string` (`stringid`, `stock_id`, `string_number`, `Owner_supplied`, `purchase_date`, `note`, `reel_no`, `reel_price`, `racket_price`, `empty`, `lengthid`) VALUES
(1, '1', '23', 'no', '05/05/2024', '', 1, '98', '17', 1, 1),
(2, '2', '20.5', 'no', '03/04/2023', '', 1, '114', '18', 1, 1),
(4, '4', '1', 'no', '05/04/2024', 'Ashaway Rally 21 Fire White', 1, '47', '15', 0, 1),
(8, '7', '1', 'yes', '12/06/2024', '', 1, '0', '12', 1, 5),
(9, '8', '2', 'yes', '12/06/2024', '', 1, '0', '12', 1, 5),
(10, '9', '1', 'yes', '12/06/2024', '', 1, '0', '12', 1, 5),
(17, '29', '0', 'no', '23/07/2024', '', 1, '125', '15', 0, 3),
(18, '17', '0', 'no', '24/07/2024', '', 1, '125', '15', 0, 8),
(19, '1', '1', 'yes', '01/07/2024', '', 2, '250', '30', 0, 1),
(7, '6', '9.5', 'yes', '01/01/2023', 'Do not delete this reel.', 1, '0', '12', 0, 1),
(32, '1577', '0', 'no', '01/01/2025', 'Do not delete this reel.', 1, '0', '15', 0, 6),
(33, '1578', '0', 'no', '01/01/2025', 'Do not delete this reel.', 1, '0', '15', 0, 3),
(34, '1579', '0', 'no', '01/01/2025', 'Do not delete this reel.', 1, '0', '15', 0, 9)";

        mysqli_query($conn, $sql);
        $sql = "INSERT INTO `stringjobs` (`job_id`, `customerid`, `stringid`, `stringidc`, `racketid`, `collection_date`, `delivery_date`, `pre_tension`, `tension`, `tensionc`, `price`, `grip_required`, `paid`, `delivered`, `comments`, `free_job`, `imageid`, `addedby`) VALUES
(10001, 3, 4, 4, 8, '24/07/2024', '31/07/2024', '0', '30', '30', '15', '0', '0', '0', '', '0', 0, 1),
(10002, 3, 19, 19, 8, '25/07/2024', '24/07/2024', '0', '30', '30', '30', '0', '0', '0', '', '0', 0, 1);
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
              <label class="mt-2">Database Username</label>
              <div>
                <div class="container">
                  <div class="row">
                    <div class="col-12">
                      <input type="text" name="username" class="form-control txtField">
                    </div>
                  </div>
                </div>
              </div>
              <label class="mt-2">Database Password</label>
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