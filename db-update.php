<?php require_once('./Connections/wcba.php');
//-------------------------------------------------------------------
// Initialize the session
if (!isset($_SESSION)) {
  session_start();
}
if (!isset($_SESSION['loggedin'])) {
  header('Location: ./login.php');
  exit;
}
if ($_SESSION['level'] < 1) {
  header('Location: ./nopermission.php');
  exit;
}
//----------------------------------------------------------------
//---Section to update job----------------------------------------
//----------------------------------------------------------------
$message = '';
if (isset($_POST['submitEditjob'])) {
  if (!isset($_POST['gripreqd'])) {
    $_POST['gripreqd'] = 0;
  }
  $gripprice = 0;
  if ($_POST['gripreqd'] == 1) {
    $query_Recordset9 = "SELECT * FROM grip";
    $Recordset9 = mysqli_query($conn, $query_Recordset9) or die(mysqli_error($conn));
    $row_Recordset9 = mysqli_fetch_assoc($Recordset9);
    $totalRows_Recordset9 = mysqli_num_rows($Recordset9);
    do {
      $gripprice = $row_Recordset9['Price'];
    } while ($row_Recordset9 = mysqli_fetch_assoc($Recordset9));
  }
  //lets make sure to reset the free job even if the string changes
  if (!isset($_POST['freerestring'])) {
    $_POST['freerestring'] = 0;
    $query_Recordset8 = "SELECT * FROM string LEFT JOIN all_string on string.stock_id = all_string.string_id WHERE stringid ='" . $_POST['stringid'] . "'";
    $Recordset8 = mysqli_query($conn, $query_Recordset8) or die(mysqli_error($conn));
    $row_Recordset8 = mysqli_fetch_assoc($Recordset8);
    $totalRows_Recordset8 = mysqli_num_rows($Recordset8);
    do {
      if (isset($_POST['setprice'])) {
        $pricex = $_POST['setprice'];
      } else {
        $pricex = $row_Recordset8['racket_price'];
      }
      $price = $pricex + $gripprice;
    } while ($row_Recordset8 = mysqli_fetch_assoc($Recordset8));
  } else {
    $price = 0 + $gripprice;
  }
  if (!isset($_POST['paid'])) {
    $_POST['paid'] = 0;
  }
  if (!isset($_POST['delivered'])) {
    $_POST['delivered'] = 0;
  }
  //-------------------------------------------------------
  //Lets sort out the string. if it was changed, we will need to add stock and take stock off reels.
  //Lets first get the two string values from the db
  if ($_POST['stringidc'] == 0) {
    $_POST['stringidc'] = $_POST['stringid'];
  }
  $query_Recordset7 = "SELECT stringid, stringidc FROM stringjobs WHERE job_id = '" . $_POST['jobid'] . "'";
  $Recordset7 = mysqli_query($conn, $query_Recordset7) or die(mysqli_error($conn));
  $row_Recordset7 = mysqli_fetch_assoc($Recordset7);
  //need to add scenarios for hybrid string
  //First lets determine if a different string is being used for mains and crosses
  if ($row_Recordset7['stringid'] == $row_Recordset7['stringidc']) {
    $pre_hybrid = 0;
    //hybrid string is not being used

  } else {
    $pre_hybrid = 1;
  }
  //-------------------------------------------------------
  if ($_POST['stringidc'] == $_POST['stringid']) {
    $post_hybrid = 0;
  } else {
    $post_hybrid = 1;
  }

  //lets update the DB with the new detials
  $comments = mysqli_real_escape_string($conn, $_POST['comments']);
  $sql = "UPDATE stringjobs
  set customerid='" . $_POST['customerid'] .
    "', stringid='" . $_POST['stringid'] .
    "', stringidc='" . $_POST['stringidc'] .
    "', racketid='" . $_POST['racketid'] .
    "', tension='" . $_POST['tensionm'] .
    "', tensionc='" . $_POST['tensionc'] .
    "', grip_required='" . $_POST['gripreqd'] .
    "', pre_tension='" . $_POST['preten'] .
    "', free_job='" . $_POST['freerestring'] .
    "', comments='" . $comments .
    "', price='" . $price .
    "', paid='" . $_POST['paid'] .
    "', delivered='" . $_POST['delivered'] .
    "', pre_tension='" . $_POST['preten'] .
    "', collection_date='" . $_POST['daterecd'] .
    "', delivery_date='" . $_POST['datereqd'] . "' WHERE job_id = '" . $_POST['jobid'] . "'";
  mysqli_query($conn, $sql);


  //----------------------------------------------------------
  //---This following section will update the string levels---
  //----------------------------------------------------------

  //scenario 1: Mains stay the same and crosses change
  if ($_POST['stringid'] == $row_Recordset7['stringid'] && $_POST['stringidc'] != $row_Recordset7['stringidc']) {
    $sql = "UPDATE string set string_number = string_number - 0.5 WHERE stringid ='" . $row_Recordset7['stringidc'] . "'";
    $sqla = "UPDATE string set string_number = string_number + 0.5 WHERE stringid ='" . $_POST['stringidc'] . "'";
    $scenario = 1;
  }

  //scenario 2: Mains change and crosses change
  elseif ($_POST['stringid'] != $row_Recordset7['stringid'] && $_POST['stringidc'] != $row_Recordset7['stringidc']) {
    $sql = "UPDATE string set string_number = string_number - 0.5 WHERE stringid ='" . $row_Recordset7['stringidc'] . "'";
    $sqla = "UPDATE string set string_number = string_number + 0.5 WHERE stringid ='" . $_POST['stringidc'] . "'";
    $sqlb = "UPDATE string set string_number = string_number - 0.5 WHERE stringid ='" . $row_Recordset7['stringid'] . "'";
    $sqlc = "UPDATE string set string_number = string_number + 0.5 WHERE stringid ='" . $_POST['stringid'] . "'";
    $scenario = 2;
  }

  //scenario 3: Mains change and crosses stay the same
  elseif ($_POST['stringid'] != $row_Recordset7['stringid'] && $_POST['stringidc'] == $row_Recordset7['stringidc']) {
    $sql = "UPDATE string set string_number = string_number - 0.5 WHERE stringid ='" . $row_Recordset7['stringid'] . "'";
    $sqla = "UPDATE string set string_number = string_number + 0.5 WHERE stringid ='" . $_POST['stringid'] . "'";
    $scenario = 3;
  }

  //scenario 4: Nothing changes
  else {
    $scenario = 4;
  }


  if (isset($sql)) {
    mysqli_query($conn, $sql);
  }
  if (isset($sqla)) {
    mysqli_query($conn, $sqla);
  }
  if (isset($sqlb)) {
    mysqli_query($conn, $sqlb);
  }
  if (isset($sqlc)) {
    mysqli_query($conn, $sqlc);
  }



  //-------------------------------------------------------
  //testing section
  /* echo $scenario . "scenario<br>";

  if (isset($sql)) {
    echo $sql . "sql<br>";
  }
  if (isset($sqla)) {
    echo $sqla . "sqla<br>";
  }
  if (isset($sqlb)) {
    echo $sqlb . "sqlb<br>";
  }
  if (isset($sqlc)) {
    echo $sqlc . "sqlc<br>";
  }


  echo $_POST['stringid'] . " Main<br>";
  echo $_POST['stringidc'] . "Crosses<br>";

  echo $pre_hybrid . "Before<br>";
  echo $post_hybrid . "After<br>";
//
  exit;
  */
  //--------------------------------------------
  //-------------------------------------------------------
  //lets add the image if there is one to the image table in the DB
  if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
    $image = $_FILES['image']['tmp_name'];
    $imgContent = file_get_contents($image);
    if (extension_loaded('imagick')) {
      //lets reduce the files size using imagick if its installed
      $imagick = new Imagick($image);
      $imagick->resizeImage(700, 5000,  imagick::FILTER_LANCZOS, 1, TRUE);
      $thumb = fopen($image, 'w');
      $imagick->writeImageFile($thumb);
    } else {
      $imagick = $imgContent;
    }
    // Insert image data into database as BLOB
    $sql = "INSERT INTO images(image) VALUES(?) ";
    $statement = $conn->prepare($sql);
    $statement->bind_param('s', $imagick);
    $current_id = $statement->execute() or die("<b>Error:</b> Problem on Image Insert<br/>" . mysqli_connect_error());
    $last_id1 = $conn->insert_id;
    $sql = "UPDATE stringjobs set imageid='" . $last_id1 . "' WHERE job_id ='" . $_POST['jobid'] . "'";
    mysqli_query($conn, $sql);
    $conn->close();
  }
  $_SESSION['message'] = "Job " . $_POST['jobid'] . " modified Successfully";
  //redirect back to the main page.
  header("location:./string-jobs.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//---Section to update customer-----------------------------------
//----------------------------------------------------------------
if (!empty($_POST['editcustomer'])) {
  $customername = mysqli_real_escape_string($conn, $_POST['customername']);
  $customermobile = mysqli_real_escape_string($conn, $_POST['customermobile']);
  $customeremail = mysqli_real_escape_string($conn, $_POST['customeremail']);
  $comments = mysqli_real_escape_string($conn, $_POST['comments']);
  $discount = rtrim($_POST['discount'], "%");
  $sql = "UPDATE customer
  set Name='" . $customername .
    "', Email='" . $customeremail .
    "', Mobile='" . $customermobile .
    "', pref_string='" . $_POST['stringid'] .
    "', pref_stringc='" . $_POST['stringidc'] .
    "', tension='" . $_POST['tension'] .
    "', tensionc='" . $_POST['tensionc'] .
    "', prestretch='" . $_POST['preten'] .
    "', racketid='" . $_POST['racketid'] .
    "', Notes='" . $comments .
    "', discount='" . $discount . "' WHERE cust_ID = '" . $_POST['customerid'] . "'";
  $_SESSION['message'] = "Customer modified Successfully";
  mysqli_query($conn, $sql);
  //redirect back to the main page.
  header("location:./customers.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//---Section to update racket-----------------------------------
//----------------------------------------------------------------
if (!empty($_POST['editracket'])) {
  $manuf = mysqli_real_escape_string($conn, $_POST['manuf']);
  $model = mysqli_real_escape_string($conn, $_POST['model']);
  $pattern = mysqli_real_escape_string($conn, $_POST['pattern']);
  $sql = "UPDATE rackets
  set manuf='" . $manuf .
    "', model='" . $model .
    "', sport='" . $_POST['sportid'] .
    "', pattern='" . $pattern . "' WHERE racketid = '" . $_POST['editracket'] . "'";
  $_SESSION['message'] = "Racket modified Successfully";
  mysqli_query($conn, $sql);
  //redirect back to the main page.
  header("location:./rackets.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//---Section to update grip-----------------------------------
//----------------------------------------------------------------
if (!empty($_POST['submiteditgrip'])) {
  $price = mb_substr($_POST['price'], 1);
  $gripname = mysqli_real_escape_string($conn, $_POST['gripname']);
  $sql = "UPDATE grip
  set Price='" . $price .
    "', type='" . $gripname . "' WHERE gripid = '" . $_POST['gripid'] . "'";
  $_SESSION['message'] = "Grip modified Successfully";
  mysqli_query($conn, $sql);
  //redirect back to the main page.
  header("location:./settings.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//---Section to update domian-----------------------------------
//----------------------------------------------------------------
if (!empty($_POST['submiteditdom'])) {
  $sql = "UPDATE settings
  set value='" . $_POST['domname'] . "' WHERE id = '7'";
  $_SESSION['message'] = "Domain modified Successfully";
  mysqli_query($conn, $sql);
  //redirect back to the main page.
  header("location:./settings.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//---Section to update account-----------------------------------
//----------------------------------------------------------------
if (!empty($_POST['submiteditacc'])) {
  $sql = "UPDATE settings
  set value='" . $_POST['accname'] . "' WHERE id = '4'";
  mysqli_query($conn, $sql);
  $sql = "UPDATE settings
  set value='" . $_POST['accnum'] . "' WHERE id = '5'";
  mysqli_query($conn, $sql);
  $sql = "UPDATE settings
  set value='" . $_POST['scode'] . "' WHERE id = '6'";
  $_SESSION['message'] = "Account details modified Successfully";
  mysqli_query($conn, $sql);
  //redirect back to the main page.
  header("location:./settings.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//---Section to update company-----------------------------------
//----------------------------------------------------------------
if (!empty($_POST['submiteditcomp'])) {
  $sql = "UPDATE settings
  set value='" . $_POST['compname'] . "' WHERE id = '12'";
  mysqli_query($conn, $sql);
  $sql = "UPDATE settings
  set value='" . $_POST['address'] . "' WHERE id = '13'";
  mysqli_query($conn, $sql);
  $sql = "UPDATE settings
  set value='" . $_POST['town'] . "' WHERE id = '14'";
  mysqli_query($conn, $sql);
  $sql = "UPDATE settings
  set value='" . $_POST['postcode'] . "' WHERE id = '15'";
  mysqli_query($conn, $sql);
  $sql = "UPDATE settings
  set value='" . $_POST['county'] . "' WHERE id = '16'";
  mysqli_query($conn, $sql);
  $sql = "UPDATE settings
  set value='" . $_POST['email'] . "' WHERE id = '17'";
  mysqli_query($conn, $sql);
  $sql = "UPDATE settings
  set value='" . $_POST['tel'] . "' WHERE id = '18'";
  mysqli_query($conn, $sql);
  $_SESSION['message'] = "Company details modified Successfully";
  //redirect back to the main page.
  header("location:./settings.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//---Section to update reel length-----------------------------------
//----------------------------------------------------------------
if (!empty($_POST['reellengthsubmitEdit'])) {
  $location = "./reel-lengths.php";
  if ((!is_numeric($_POST['length'])) or (!is_numeric($_POST['warning_level'])) or ($_POST['warning_level'] == 0)) {
    $_SESSION['message'] = "Please check the values you entered for length and warning level!";
    header("location:$location"); //Redirecting To the main page
  } else {
    $sql = "UPDATE reel_lengths
  set 
  length='" . $_POST['length'] . "', 
  warning_level='" . $_POST['warning_level'] . "',
    sport='" . $_POST['sport'] . "'
  WHERE reel_length_id = '" . $_POST['id'] . "'";
    $_SESSION['message'] = "Length modified Successfully";
    mysqli_query($conn, $sql);
    //redirect back to the main page.
    header("location:$location"); //Redirecting To the main page
  }
}
//----------------------------------------------------------------
//---Section to update sport-----------------------------------
//----------------------------------------------------------------
if (!empty($_POST['SportEdit'])) {
  $location = "./sports.php";
  if ((!is_numeric($_POST['length']))  or ($_POST['length'] == 0)) {
    $_SESSION['message'] = "Please check the values you entered for length!";
    header("location:$location"); //Redirecting To the main page
  } else {
    $sql = "UPDATE sport
  set 
  sportname='" . $_POST['name'] . "', 
  string_length_per_racket='" . $_POST['length'] . "',
  image='" . $_POST['icon'] . "'
  WHERE sportid = '" . $_POST['id'] . "'";
    $_SESSION['message'] = "Sport modified Successfully";
    mysqli_query($conn, $sql);
    //redirect back to the main page.
    header("location:$location"); //Redirecting To the main page
  }
}
//----------------------------------------------------------------
//---Section to update currency-----------------------------------
//----------------------------------------------------------------
if (!empty($_POST['submiteditcurrency'])) {
  $sql = "UPDATE settings
  set value='" . $_POST['currency'] . "' WHERE id = '2'";
  $_SESSION['message'] = "Currency modified Successfully";
  mysqli_query($conn, $sql);
  //redirect back to the main page.
  header("location:./settings.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//---Section to update units-----------------------------------
//----------------------------------------------------------------
if (!empty($_POST['submiteditunits'])) {
  $sql = "UPDATE settings
  set value='" . $_POST['units'] . "' WHERE id = '3'";
  $_SESSION['message'] = "Units modified Successfully";
  mysqli_query($conn, $sql);
  //redirect back to the main page.
  header("location:./settings.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//---Section to update stock string-----------------------------------
//----------------------------------------------------------------
if (!empty($_POST['editstockstring'])) {
  $notes = mysqli_real_escape_string($conn, $_POST['notes']);
  $sql = "UPDATE string
  set Owner_supplied='" . $_POST['ownersupplied'] .
    "', note='" . $notes .
    "', reel_price='" . $_POST['purchprice'] .
    "', stock_id='" . $_POST['stringid'] .
    "', racket_price='" . $_POST['racketprice'] .
    "', empty='" . $_POST['emptyreel'] .
    "', lengthid='" . $_POST['length'] .
    "', purchase_date='" . $_POST['datepurch'] . "' WHERE stringid = '" . $_POST['editstockstring'] . "'";
  mysqli_query($conn, $sql);
  //we need to also update all of the string job prices that are using this reel but not the free jobs
  $sqla = "UPDATE stringjobs
  set price='" . $_POST['racketprice'] .
    "' WHERE stringid = '" . $_POST['stringid'] . "'" . "AND free_job = '0'";
  mysqli_query($conn, $sqla);
  $_SESSION['message'] = "Stock string modified Successfully";
  //redirect back to the main page.
  header("location:./string.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//---Section to update in market string-----------------------------------
//----------------------------------------------------------------
if (!empty($_POST['editimstring'])) {
  $notes = mysqli_real_escape_string($conn, $_POST['notes']);
  $sql = "UPDATE all_string
  set brand='" . $_POST['brand'] .
    "', notes='" . $notes .
    "', type='" . $_POST['type'] .
    "', sportid='" . $_POST['sportid'] . "' WHERE string_id = '" . $_POST['editimstring'] . "'";
  $_SESSION['message'] = "In market string modified Successfully";
  mysqli_query($conn, $sql);
  //redirect back to the main page.
  header("location:./string-im.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//---Section to update delivered status-----------------------------------
//----------------------------------------------------------------
if (!empty($_POST['jobiddeliveredupdate'])) {

  if (isset($_POST['deliveredupdate'])) {
    $delivered = "1";
  } else {
    $delivered = "0";
  }

  $sql = "UPDATE stringjobs
  set delivered='" . $delivered . "' WHERE job_id = '" . $_POST['jobiddeliveredupdate'] . "'";
  $_SESSION['message'] = "Job " . $_POST['jobiddeliveredupdate'] . " set to delivered";
  mysqli_query($conn, $sql);
  //redirect back to the main page.
  header("location:./string-jobs.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//---Section to update paid status-----------------------------------
//----------------------------------------------------------------
if (!empty($_POST['jobidpaidupdate'])) {

  if (isset($_POST['paidupdate'])) {
    $paid = "1";
  } else {
    $paid = "0";
  }

  $sql = "UPDATE stringjobs
  set paid='" . $paid . "' WHERE job_id = '" . $_POST['jobidpaidupdate'] . "'";
  $_SESSION['message'] = "Job " . $_POST['jobidpaidupdate'] . " set to paid";
  mysqli_query($conn, $sql);
  //redirect back to the main page.
  header("location:./string-jobs.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//-----------------Section to delete a job  from DB---------------
//----------------------------------------------------------------
if (isset($_POST['submitdelete'])) {
  $sql = "DELETE FROM stringjobs WHERE job_id='" . $_POST['refdel'] . "'";
  mysqli_query($conn, $sql);
  //lets stick stock back onto the reel if the job was deleted.
  if ((isset($_POST['stringidm'])) && (!isset($_POST['stringidc']))) {
    $sql = "UPDATE string set string_number = string_number - 1 WHERE stringid ='" . $_POST['stringidm'] . "'";
  } elseif ((isset($_POST['stringidm'])) && (isset($_POST['stringidc']))) {
    if ($_POST['stringidm'] === $_POST['stringidc']) {
      $sql = "UPDATE string set string_number = string_number - 1 WHERE stringid ='" . $_POST['stringidm'] . "'";
      mysqli_query($conn, $sql);
    } elseif ($_POST['stringidm'] > 0 && $_POST['stringidc'] == 0) {
      $sql = "UPDATE string set string_number = string_number - 1 WHERE stringid ='" . $_POST['stringidm'] . "'";
      mysqli_query($conn, $sql);
    } elseif ($_POST['stringidm'] > 0 && $_POST['stringidc'] > 0) {
      $sql = "UPDATE string set string_number = string_number - 0.5 WHERE stringid ='" . $_POST['stringidm'] . "'";
      $sqla = "UPDATE string set string_number = string_number - 0.5 WHERE stringid ='" . $_POST['stringidc'] . "'";
      mysqli_query($conn, $sql);
      mysqli_query($conn, $sqla);
    }
  }
  $_SESSION['message'] = "Restring deleted Successfully";
  $_SESSION['message'] = "Job deleted Successfully";
  //redirect back to the main page.
  header("location:./string-jobs.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//------------Section to delete a customer  from DB---------------
//----------------------------------------------------------------
if (!empty($_POST['refdelcust'])) {
  //first lets chheck if the customer has any jobs assigned to them. If they do we will need to add a message to get the user to reassign the jobs to a new customer
  $query_Recordset3 = "SELECT * FROM stringjobs WHERE customerid = " . $_POST['refdelcust'];
  $Recordset3 = mysqli_query($conn, $query_Recordset3) or die(mysqli_error($conn));
  $numberofjobs = mysqli_num_rows($Recordset3);
  if ($numberofjobs == 0) {
    mysqli_query($conn, "DELETE FROM customer 
  WHERE cust_ID='" . $_POST['refdelcust'] . "'");
    $_SESSION['message'] = "Customer deleted Successfully";
  } else {
    $_SESSION['message'] = "Unable to delete customer<br>There are " . $numberofjobs . " jobs assigned to this customer<br>Please reassign the jobs to another user before deleting!";
  }
  //redirect back to the main page.
  header("location:./customers.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//------------Section to delete a reel length  from DB---------------
//----------------------------------------------------------------
if (!empty($_POST['ReelLengthDel'])) {
  //first lets chheck if the customer has any jobs assigned to them. If they do we will need to add a message to get the user to reassign the jobs to a new customer
  $query_Recordset3 = "SELECT * FROM string WHERE lengthid = " . $_POST['reel_length_id'];
  $Recordset3 = mysqli_query($conn, $query_Recordset3) or die(mysqli_error($conn));
  $numberofjobs = mysqli_num_rows($Recordset3);
  if ($numberofjobs == 0) {
    mysqli_query($conn, "DELETE FROM reel_lengths 
  WHERE reel_length_id='" . $_POST['reel_length_id'] . "'");
    $_SESSION['message'] = "Reel length deleted Successfully";
  } else {
    $_SESSION['message'] = "Unable to delete reel length<br>There are stock string reels that are assigned this length!";
  }
  //redirect back to the main page.
  header("location:./reel-lengths.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//------------Section to delete a sport from DB---------------
//----------------------------------------------------------------
if (!empty($_POST['SportDel'])) {
  //first lets check if the sport has any jobs assigned to them. If they do we will need to add a message to get the user to reassign the jobs to a new customer
  $query_Recordset3 = "SELECT * FROM all_string WHERE sportid = " . $_POST['sportid'];
  $Recordset3 = mysqli_query($conn, $query_Recordset3) or die(mysqli_error($conn));
  $numberofjobs = mysqli_num_rows($Recordset3);
  if ($numberofjobs == 0) {
    mysqli_query($conn, "DELETE FROM sport 
  WHERE sportid='" . $_POST['sportid'] . "'");
    $_SESSION['message'] = "Sport deleted Successfully";
  } else {
    $_SESSION['message'] = "Unable to delete sport<br>There are in market string reels that are assigned this sport!";
  }
  //redirect back to the main page.
  header("location:./sports.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//----------Section to delete a stock reel  from DB---------------
//----------------------------------------------------------------
if (!empty($_POST['refdelreel'])) {
  //first lets check if the reel has any jobs assigned to it. If they do we will need to add a message to get the user to reassign the jobs to a new reel
  $query_Recordset3 = "SELECT * FROM stringjobs WHERE stringid = " . $_POST['refdelreel'];
  $Recordset3 = mysqli_query($conn, $query_Recordset3) or die(mysqli_error($conn));
  $numberofjobs = mysqli_num_rows($Recordset3);
  if ($numberofjobs == 0) {
    mysqli_query($conn, "DELETE FROM string 
    WHERE stringid='" . $_POST['refdelreel'] . "'");
    $_SESSION['message'] = "Reel deleted Successfully";
  } else {
    $_SESSION['message'] = "Unable to delete reel<br>There are " . $numberofjobs . " jobs that use this reel of string<br>Please reassign the jobs to another reel before deleting!";
  }
  //redirect back to the main page.
  header("location:./string.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//----------Section to delete a in market reel from DB---------------
//----------------------------------------------------------------
if (!empty($_POST['refdelstringim'])) {
  //first lets check if the stock has any jobs assigned to it. If they do we will need to add a message to get the user to reassign the jobs to a new reel
  $query_Recordset3 = "SELECT * FROM string WHERE stock_id = " . $_POST['refdelstringim'];
  $Recordset3 = mysqli_query($conn, $query_Recordset3) or die(mysqli_error($conn));
  $numberofjobs = mysqli_num_rows($Recordset3);
  if ($numberofjobs == 0) {
    $sql = "DELETE FROM all_string WHERE string_id='" . $_POST['refdelstringim'] . "'";
    mysqli_query($conn, $sql);
    $_SESSION['message'] = "Reel deleted Successfully";
  } else {
    $_SESSION['message'] = "Unable to delete stock string<br>There are " . $numberofjobs . " stock reels that use this string as reference<br>Please reassign the stock reels to another in market string before deleting!";
  }
  //redirect back to the main page.
  header("location:./string-im.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//----------Section to delete a racket from DB---------------
//----------------------------------------------------------------
if (!empty($_POST['refdelracket'])) {
  //first lets check if the racket has any jobs assigned to it. If they do we will need to add a message to get the user to reassign new rackets in the relevant jobs
  $query_Recordset3 = "SELECT * FROM stringjobs WHERE racketid = " . $_POST['refdelracket'];
  $Recordset3 = mysqli_query($conn, $query_Recordset3) or die(mysqli_error($conn));
  $numberofjobs = mysqli_num_rows($Recordset3);
  if ($numberofjobs == 0) {
    $sql = "DELETE FROM rackets WHERE racketid='" . $_POST['refdelracket'] . "'";
    mysqli_query($conn, $sql);
    $_SESSION['message'] = "Racket deleted Successfully";
  } else {
    $_SESSION['message'] = "Unable to delete racket <br>There are " . $numberofjobs . " that use this racket <br>Please reassign the jobs to another racket before deleting!";
  }
  //redirect back to the main page.
  header("location:./rackets.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//-------------------Add new reel of string to DB-----------------
//----------------------------------------------------------------
if (isset($_POST['submitaddstockstring'])) {
  if ($_POST['marker'] == 1) {
    $location = "./string.php";
  } elseif ($_POST['marker'] == 2) {
    $location = "./customer.php";
  } elseif ($_POST['marker'] == 3) {
    $location = "./addjob.php";
  } elseif ($_POST['marker'] == 4) {
    $location = "./editjob.php";
  } elseif ($_POST['marker'] == 5) {
    $location = "./editcust.php";
  }
  if ($_POST['length'] == "x") {
    $_SESSION['message'] = "You must select length";
    //redirect back to the main page.
    header("location:$location"); //Redirecting To the main page
  } else {
    $query_Recordset8 = "SELECT * FROM string WHERE stock_id = " . $_POST['stockid'];
    $Recordset8 = mysqli_query($conn, $query_Recordset8) or die(mysqli_error($conn));
    $row_Recordset8 = mysqli_fetch_assoc($Recordset8);
    $number_of_reels = mysqli_num_rows($Recordset8);
    $number_of_reels++;
    $query_Recordset1 = "SELECT * FROM string";
    $Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
    $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
    $totalRows_Recordset1 = mysqli_num_rows($Recordset1);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    do {
      $last_id = $row_Recordset1['stringid'];
    } while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));
    $last_id++;
    $sql = "INSERT INTO string (stringid, stock_id, string_number, Owner_supplied, note, reel_no, reel_price, racket_price, empty, purchase_date, lengthid) VALUES ('"
      . $last_id . "', '"
      . $_POST['stockid'] . "', '"
      . '0' . "', '"
      . $_POST['ownersupplied'] . "', '"
      . $notes . "', '"
      . $number_of_reels . "', '"
      . $_POST['purchprice'] . "', '"
      . $_POST['racketprice'] . "', '"
      . '0' . "', '"
      . $_POST['datepurch'] . "', '"
      . $_POST['length'] . "')";
    mysqli_query($conn, $sql);
    $_SESSION['message'] = "Reel added Successfully";
    //redirect back to the main page.
    header("location:$location"); //Redirecting To the main page
  }
}
//----------------------------------------------------------------
//---------Add new reel of in market string to DB-----------------
//----------------------------------------------------------------
if (!empty($_POST['addmarketstring'])) {
  $query_Recordset1 = "SELECT * FROM all_string";
  $Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
  $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
  $totalRows_Recordset1 = mysqli_num_rows($Recordset1);
  do {
    $last_id = $row_Recordset1['string_id'];
  } while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));
  $last_id++;
  if (isset($_POST['marker'])) {
    if ($_POST['marker'] == 1) {
      $location = "./addavstring.php?marker=1";
    } elseif ($_POST['marker'] == 2) {
      $location = "./addcustomer.php?marker=2";
    } elseif ($_POST['marker'] == 3) {
      $location = "./string-im.php?marker=3";
    }
  } else {
    $location = "./string-im.php";
  }
  if (empty($_POST['notes'])) {
    $_POST['notes'] = ' ';
  }
  $brand = mysqli_real_escape_string($conn, $_POST['brand']);
  $type = mysqli_real_escape_string($conn, $_POST['type']);
  $notes = mysqli_real_escape_string($conn, $_POST['notes']);
  $sql = "INSERT INTO all_string (string_id, brand, type, sportid, notes) VALUES ('"
    . $last_id . "', '"
    . $brand . "', '"
    . $type . "', '"
    . $_POST['sport'] . "', '"
    . $notes . "')";
  mysqli_query($conn, $sql);
  $_SESSION['message'] = "Reel added Successfully";
  //redirect back to the main page.
  header("location:$location"); //Redirecting To the main page
}
//----------------------------------------------------------------
//---------Add new racket to DB-----------------
//----------------------------------------------------------------
if (isset($_POST['submitaddracket'])) {
  $query_Recordset1 = "SELECT * FROM rackets";
  $Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
  $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
  $totalRows_Recordset1 = mysqli_num_rows($Recordset1);
  do {
    $last_id = $row_Recordset1['racketid'];
  } while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));
  $last_id++;
  if ($_POST['marker'] == 1) {
    $location = "./rackets.php?marker=1";
  } elseif ($_POST['marker'] == 2) {
    $location = "./addcustomer.php?marker=2";
  } elseif ($_POST['marker'] == 3) {
    $location = "./string-im.php?marker=3";
  }
  if (empty($_POST['notes'])) {
    $_POST['notes'] = ' ';
  }
  $brand = mysqli_real_escape_string($conn, $_POST['brand']);
  $model = mysqli_real_escape_string($conn, $_POST['type']);
  $pattern = mysqli_real_escape_string($conn, $_POST['pattern']);

  //Lets check for a duplicate racket  entry
  $query_Recordset1 = "SELECT * FROM rackets WHERE manuf = '" . $brand . "' AND model = '" . $model . "'";
  $Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
  $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
  $totalRows_Recordset1 = mysqli_num_rows($Recordset1);
  if ($totalRows_Recordset1 > 0) {
    $_SESSION['message'] = "Racket already exists";
    header("location:$location"); //Redirecting To the main page
  } else {

    $sql = "INSERT INTO rackets (racketid, manuf, model, pattern, sport) VALUES ('"
      . $last_id . "', '"
      . $brand . "', '"
      . $model . "', '"
      . $pattern . "', '"
      . $_POST['sport'] . "')";
    mysqli_query($conn, $sql);
    $_SESSION['message'] = "Racket added Successfully";
    //redirect back to the main page.
    header("location:$location"); //Redirecting To the main page
  }
}
//----------------------------------------------------------------
//---------Add new reel length to DB-----------------
//----------------------------------------------------------------
if (isset($_POST['reellengthsubmitAdd'])) {
  $location = "./reel-lengths.php";



  //Lets check for a duplicate reel length entry
  $query_Recordset1 = "SELECT * FROM reel_lengths WHERE length = '" . $_POST['length'] . "' AND sport = '" . $_POST['sport'] . "'";
  $Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
  $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
  $totalRows_Recordset1 = mysqli_num_rows($Recordset1);
  if ($totalRows_Recordset1 > 0) {
    $_SESSION['message'] = "Reel length already exists";
    header("location:$location"); //Redirecting To the main page
  } else {
    if ((!is_numeric($_POST['length'])) or (!is_numeric($_POST['warning_level'])) or ($_POST['warning_level'] == 0)) {
      $_SESSION['message'] = "Please check the values you entered for length and warning level!";
      header("location:$location"); //Redirecting To the main page
    } else {
      $sql = "INSERT INTO reel_lengths (length, warning_level, sport) VALUES ('"
        . $_POST['length'] . "', '"
        . $_POST['warning_level'] . "', '"
        . $_POST['sport'] . "')";
      mysqli_query($conn, $sql);
      $_SESSION['message'] = "Reel length added Successfully";
      //redirect back to the main page.
      header("location:$location"); //Redirecting To the main page
    }
  }
}
//----------------------------------------------------------------
//---------Add new sport to DB-----------------
//----------------------------------------------------------------
if (isset($_POST['SportAdd'])) {

  $sportname = mysqli_real_escape_string($conn, $_POST['sport']);
  //Lets check for a duplicate customer entry
  $query_Recordset1 = "SELECT * FROM sport WHERE sportname = '" . $sportname . "'";
  $Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
  $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
  $totalRows_Recordset1 = mysqli_num_rows($Recordset1);
  if ($totalRows_Recordset1 > 0) {
    $_SESSION['message'] = "Sport already exists";
    header("location:./sports.php"); //Redirecting To the main page
  } else {

    $location = "./sports.php";
    if ((!is_numeric($_POST['strlen'])) or ($_POST['strlen'] == 0)) {
      $_SESSION['message'] = "Please check the values you entered for length!";
      header("location:$location"); //Redirecting To the main page
    } else {
      $sql = "INSERT INTO sport (sportname, image, string_length_per_racket) VALUES ('"
        . $_POST['sport'] . "', '"
        . $_POST['icon'] . "', '"
        . $_POST['strlen'] . "')";
      mysqli_query($conn, $sql);
      $_SESSION['message'] = "Sport added Successfully";
      //redirect back to the main page.
      header("location:$location"); //Redirecting To the main page
    }
  }
}
//---------------------------------------------------------------
//------------------------Add new customer to DB-----------------
//----------------------------------------------------------------
if (isset($_POST['submitaddcust'])) {





  $customername = mysqli_real_escape_string($conn, $_POST['customername']);
  $customeremail = mysqli_real_escape_string($conn, $_POST['customeremail']);
  $customermobile = mysqli_real_escape_string($conn, $_POST['customermobile']);
  $comments = mysqli_real_escape_string($conn, $_POST['comments']);

  //lets check for an empty name field
  if (empty($customername)) {
    $_SESSION['message'] = "You must enter a customer name";
    header("location:./addcustomer.php"); //Redirecting To the main page
  } else {



    //Lets check for a duplicate customer entry
    $query_Recordset1 = "SELECT * FROM customer WHERE Name = '" . $customername . "'";
    $Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
    $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
    $totalRows_Recordset1 = mysqli_num_rows($Recordset1);
    if ($totalRows_Recordset1 > 0) {
      $_SESSION['message'] = "Customer already exists";
      header("location:./addcustomer.php"); //Redirecting To the main page
    } else {
      if (!isset($_POST['preten'])) {
        $_POST['preten'] = '0';
      }
      $sql = "INSERT INTO customer (Name, Email, Mobile, pref_string, pref_stringc, tension, tensionc, prestretch, racketid, Notes) VALUES ('"
        . $customername . "', '"
        . $customeremail . "', '"
        . $customermobile . "', '"
        . $_POST['stringid'] . "', '"
        . $_POST['stringidc'] . "', '"
        . $_POST['tension'] . "', '"
        . $_POST['tensionc'] . "', '"
        . $_POST['preten'] . "', '"
        . $_POST['racketid'] . "', '"
        . $comments . "')";
      mysqli_query($conn, $sql);
      $last_id = mysqli_insert_id($conn);
      $_SESSION['message'] = "Customer added Successfully";
      if ($_POST['marker'] == 1) {
        $location = "./customers.php?marker=1";
      } elseif ($_POST['marker'] == 2) {
        $location = "./editjob.php?marker=2";
      } elseif ($_POST['marker'] == 3) {
        $location = "./addjob.php?customerid=$last_id";
      }
      //redirect back to the main page.
      header("location:$location"); //Redirecting To the main page
    }
  }
}
//----------------------------------------------------------------
//------------------Section to add a new job  to DB---------------
//----------------------------------------------------------------
if (isset($_POST['submitadd'])) {
  $comments = mysqli_real_escape_string($conn, $_POST['comments']);
  $gripprice = 0;
  if ($_POST['gripreqd'] == 1) {
    $query_Recordset9 = "SELECT * FROM grip";
    $Recordset9 = mysqli_query($conn, $query_Recordset9) or die(mysqli_error($conn));
    $row_Recordset9 = mysqli_fetch_assoc($Recordset9);
    $totalRows_Recordset9 = mysqli_num_rows($Recordset9);
    do {
      $gripprice = $row_Recordset9['Price'];
    } while ($row_Recordset9 = mysqli_fetch_assoc($Recordset9));
  }
  $query_Recordset1 = "SELECT * FROM stringjobs ORDER BY job_id DESC LIMIT 1;";
  $Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
  $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
  $totalRows_Recordset1 = mysqli_num_rows($Recordset1);
  if ($_POST['stringidc'] == 0) {
    $_POST['stringidc'] = $_POST['stringid'];
    $_POST['tensionc'] = $_POST['tensionm'];
  }
  do {
    $last_id = $row_Recordset1['job_id'];
  } while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));
  $last_id++;
  $sql = "INSERT INTO stringjobs (job_id, customerid, stringid, stringidc, racketid, collection_date, delivery_date, pre_tension, tension, tensionc, grip_required, paid, delivered, comments, free_job, addedby ) VALUES ('"
    . $last_id . "', '"
    . $_POST['customerid'] . "', '"
    . $_POST['stringid'] . "', '"
    . $_POST['stringidc'] . "', '"
    . $_POST['racketid'] . "', '"
    . $_POST['daterecd'] . "', '"
    . $_POST['datereqd'] . "', '"
    . $_POST['preten'] . "', '"
    . $_POST['tensionm'] . "', '"
    . $_POST['tensionc'] . "', '"
    . $_POST['gripreqd'] . "', '"
    . '0' . "', '"
    . '0' . "', '"
    . $comments . "', '"
    . $_POST['freerestring'] . "', '"
    . $_SESSION['id'] . "')";
  mysqli_query($conn, $sql) or die(mysqli_error($conn));
  $query_Recordset2 = "SELECT * FROM string LEFT JOIN all_string on string.stock_id = all_string.string_id where  stringid =" . $_POST['stringid'];
  $Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));
  $row_Recordset2 = mysqli_fetch_assoc($Recordset2);
  $totalRows_Recordset2 = mysqli_num_rows($Recordset2);
  $query_Recordset3 = "SELECT * FROM stringjobs LEFT JOIN customer ON stringjobs.customerid = customer.cust_ID WHERE job_id = " . $last_id . " ORDER BY job_id DESC LIMIT 1";
  $Recordset3 = mysqli_query($conn, $query_Recordset3) or die(mysqli_error($conn));
  $row_Recordset3 = mysqli_fetch_assoc($Recordset3);
  $discount = $row_Recordset3['discount'];
  do {
    if ($_POST['freerestring'] == 1) {
      $price = 0 + $gripprice;
    } else {
      $price = $row_Recordset2['racket_price'] + $gripprice;
    }
    //lets remove any discount
    $pricex = (($price / 100) * $discount);
    $price = $price - $pricex;
  } while ($row_Recordset2 = mysqli_fetch_assoc($Recordset2));
  mysqli_query($conn, "UPDATE stringjobs set price='" . $price . "' WHERE job_id ='" . $last_id . "'");
  //lets stick stock back onto the reel if the job was deleted.
  if ((isset($_POST['stringid'])) && (!isset($_POST['stringidc']))) {
    $sql = "UPDATE string set string_number = string_number + 1 WHERE stringid ='" . $_POST['stringid'] . "'";
  } elseif ((isset($_POST['stringid'])) && (isset($_POST['stringidc']))) {
    if ($_POST['stringid'] === $_POST['stringidc']) {
      $sql = "UPDATE string set string_number = string_number + 1 WHERE stringid ='" . $_POST['stringid'] . "'";
      mysqli_query($conn, $sql);
    } elseif ($_POST['stringid'] > 0 && $_POST['stringidc'] == 0) {
      $sql = "UPDATE string set string_number = string_number + 1 WHERE stringid ='" . $_POST['stringid'] . "'";
      mysqli_query($conn, $sql);
    } elseif ($_POST['stringid'] > 0 && $_POST['stringidc'] > 0) {
      $sql = "UPDATE string set string_number = string_number + 0.5 WHERE stringid ='" . $_POST['stringid'] . "'";
      $sqla = "UPDATE string set string_number = string_number + 0.5 WHERE stringid ='" . $_POST['stringidc'] . "'";
      mysqli_query($conn, $sql);
      mysqli_query($conn, $sqla);
    }
  }
  //lets add the image if there is one to the image table in the DB
  if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
    $image = $_FILES['image']['tmp_name'];
    $imgContent = file_get_contents($image);
    if (extension_loaded('imagick')) {
      //lets reduce the files size using imagick if its installed
      $imagick = new Imagick($image);
      $imagick->resizeImage(700, 5000,  imagick::FILTER_LANCZOS, 1, TRUE);
      $thumb = fopen($image, 'w');
      $imagick->writeImageFile($thumb);
    } else {
      $imagick = $imgContent;
    }
    // Insert image data into database as BLOB
    $sql = "INSERT INTO images(image) VALUES(?) ";
    $statement = $conn->prepare($sql);
    $statement->bind_param('s', $imagick);
    $current_id = $statement->execute() or die("<b>Error:</b> Problem on Image Insert<br/>" . mysqli_connect_error());
    $last_id1 = $conn->insert_id;
    mysqli_query($conn, "UPDATE stringjobs set imageid='" . $last_id1 . "' WHERE job_id ='" . $last_id . "'");
    $conn->close();
  }
  $_SESSION['message'] = "Job added Successfully";
  //redirect back to the main page.
  header("location:./string-jobs.php"); //Redirecting To the main page
}
