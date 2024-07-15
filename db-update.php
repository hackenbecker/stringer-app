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
  if ($_POST['stringidc'] == $_POST['stringid']) {
    $_POST['stringidc'] = 0;
  }

  $query_Recordset7 = "SELECT stringid, stringidc FROM stringjobs WHERE job_id = '" . $_POST['jobid'] . "'";
  $Recordset7 = mysqli_query($conn, $query_Recordset7) or die(mysqli_error($conn));
  $row_Recordset7 = mysqli_fetch_assoc($Recordset7);

  //need to add scenarios for hybrid string

  //First lets determine if a hybrid string is being used or not
  if (($row_Recordset7['stringid'] == $row_Recordset7['stringidc']) or ($row_Recordset7['stringid'] > 0 && $row_Recordset7['stringidc'] == 0)) {
    //hybrid string is currently not being used
    $pre_hybrid = 0;
  } elseif (($row_Recordset7['stringid'] == $row_Recordset7['stringidc']) or ($row_Recordset7['stringid'] > 0 && $row_Recordset7['stringidc'] == 0)) {
    //hybrid string is not being even after the edit for has been posted
    $pre_hybrid = 0;
  } elseif (($row_Recordset7['stringid'] != $_POST['stringidc']) && ($row_Recordset7['stringidc'] > 0)) {
    $pre_hybrid = 1;
  } else {
    $pre_hybrid = 1;
  }
  //-------------------------------------------------------

  if (($_POST['stringid'] == $_POST['stringidc']) or ($_POST['stringid'] > 0 && $_POST['stringidc'] == 0)) {
    //hybrid string is not being even after the edit for has been posted
    $post_hybrid = 0;
  } elseif (($_POST['stringid'] != $_POST['stringidc']) && ($_POST['stringidc'] > 0)) {
    $post_hybrid = 1;
  } else {
    $post_hybrid = 1;
  }


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
    "', comments='" . $_POST['comments'] .
    "', price='" . $price .
    "', paid='" . $_POST['paid'] .
    "', delivered='" . $_POST['delivered'] .
    "', pre_tension='" . $_POST['preten'] .
    "', collection_date='" . $_POST['daterecd'] .
    "', delivery_date='" . $_POST['datereqd'] . "' WHERE job_id = '" . $_POST['jobid'] . "'";
  mysqli_query($conn, $sql);

  //-------------------------------------------------------

  if ($_POST['stringid'] == $row_Recordset7['stringid'] && $_POST['stringidc'] != $row_Recordset7['stringidc']) {
    //scenarion 1: Main stay the same and crosses change

    if ($pre_hybrid == 1 && $post_hybrid == 1) {
      $sql = "UPDATE string set string_number = string_number - 0.5 WHERE stringid ='" . $row_Recordset7['stringidc'] . "'";
      $sqla = "UPDATE string set string_number = string_number + 0.5 WHERE stringid ='" . $_POST['stringidc'] . "'";
    } elseif ($pre_hybrid == 0 && $post_hybrid == 1) {

      $sql = "UPDATE string set string_number = string_number - 1 WHERE stringid ='" . $row_Recordset7['stringid'] . "'";
      $sqla = "UPDATE string set string_number = string_number + 0.5 WHERE stringid ='" . $_POST['stringid'] . "'";
      $sqlb = "UPDATE string set string_number = string_number + 0.5 WHERE stringid ='" . $_POST['stringidc'] . "'";
    } elseif ($pre_hybrid == 1 && $post_hybrid == 0) {

      $sql = "UPDATE string set string_number = string_number - 0.5 WHERE stringid ='" . $row_Recordset7['stringid'] . "'";
      $sqla = "UPDATE string set string_number = string_number - 0.5 WHERE stringid ='" . $row_Recordset7['stringidc'] . "'";
      $sqlb = "UPDATE string set string_number = string_number + 1 WHERE stringid ='" . $_POST['stringid'] . "'";
    } elseif ($pre_hybrid == 0 && $post_hybrid == 0) {
      $sql = "UPDATE string set string_number = string_number - 1 WHERE stringid ='" . $row_Recordset7['stringid'] . "'";
      $sqla = "UPDATE string set string_number = string_number + 1 WHERE stringid ='" . $_POST['stringid'] . "'";
    }

    mysqli_query($conn, $sql);
    mysqli_query($conn, $sqla);
    if (isset($sqlb)) {
      mysqli_query($conn, $sqlb);
    }
  } elseif ($_POST['stringid'] != $row_Recordset7['stringid'] && $_POST['stringidc'] == $row_Recordset7['stringidc']) {
    //scenarion 3: Main change and crosses stay the same
    if ($pre_hybrid == 1 && $post_hybrid == 1) {
      $sql = "UPDATE string set string_number = string_number - 0.5 WHERE stringid ='" . $row_Recordset7['stringid'] . "'";
      $sqla = "UPDATE string set string_number = string_number + 0.5 WHERE stringid ='" . $_POST['stringid'] . "'";
    } elseif ($pre_hybrid == 0 && $post_hybrid == 1) {


      $sql = "UPDATE string set string_number = string_number - 1 WHERE stringid ='" . $row_Recordset7['stringid'] . "'";
      $sqla = "UPDATE string set string_number = string_number + 0.5 WHERE stringid ='" . $_POST['stringid'] . "'";
      $sqlb = "UPDATE string set string_number = string_number + 0.5 WHERE stringid ='" . $_POST['stringidc'] . "'";
    } elseif ($pre_hybrid == 1 && $post_hybrid == 0) {

      $sql = "UPDATE string set string_number = string_number - 0.5 WHERE stringid ='" . $row_Recordset7['stringid'] . "'";
      $sqla = "UPDATE string set string_number = string_number - 0.5 WHERE stringid ='" . $row_Recordset7['stringidc'] . "'";
      $sqlb = "UPDATE string set string_number = string_number + 1 WHERE stringid ='" . $_POST['stringid'] . "'";
    } elseif ($pre_hybrid == 0 && $post_hybrid == 0) {

      $sql = "UPDATE string set string_number = string_number - 1 WHERE stringid ='" . $row_Recordset7['stringid'] . "'";
      $sqla = "UPDATE string set string_number = string_number + 1 WHERE stringid ='" . $_POST['stringid'] . "'";
    }

    mysqli_query($conn, $sql);
    mysqli_query($conn, $sqla);
    if (isset($sqlb)) {
      mysqli_query($conn, $sqlb);
    }
  } elseif ($_POST['stringid'] != $row_Recordset7['stringid'] && $_POST['stringidc'] != $row_Recordset7['stringidc']) {

    //scenarion 3: Main change and crosses change
    if ($pre_hybrid == 1 && $post_hybrid == 1) {
      $sql = "UPDATE string set string_number = string_number - 0.5 WHERE stringid ='" . $row_Recordset7['stringid'] . "'";
      $sqla = "UPDATE string set string_number = string_number + 0.5 WHERE stringid ='" . $_POST['stringid'] . "'";
      $sqlb = "UPDATE string set string_number = string_number - 0.5 WHERE stringid ='" . $row_Recordset7['stringidc'] . "'";
      $sqlc = "UPDATE string set string_number = string_number + 0.5 WHERE stringid ='" . $_POST['stringidc'] . "'";
    } elseif ($pre_hybrid == 0 && $post_hybrid == 1) {

      $sql = "UPDATE string set string_number = string_number - 1 WHERE stringid ='" . $row_Recordset7['stringid'] . "'";
      $sqla = "UPDATE string set string_number = string_number + 0.5 WHERE stringid ='" . $_POST['stringid'] . "'";

      $sqlb = "UPDATE string set string_number = string_number - 0 WHERE stringid ='" . $row_Recordset7['stringidc'] . "'";
      $sqlc = "UPDATE string set string_number = string_number + 0.5 WHERE stringid ='" . $_POST['stringidc'] . "'";
    } elseif ($pre_hybrid == 1 && $post_hybrid == 0) {

      $sql = "UPDATE string set string_number = string_number - 0.5 WHERE stringid ='" . $row_Recordset7['stringid'] . "'";
      $sqla = "UPDATE string set string_number = string_number + 1 WHERE stringid ='" . $_POST['stringid'] . "'";
      $sqlb = "UPDATE string set string_number = string_number + 0 WHERE stringid ='" . $row_Recordset7['stringidc'] . "'";
      $sqlc = "UPDATE string set string_number = string_number - 0.5 WHERE stringid ='" . $_POST['stringidc'] . "'";
    } elseif ($pre_hybrid == 0 && $post_hybrid == 0) {
      $sql = "UPDATE string set string_number = string_number - 1 WHERE stringid ='" . $row_Recordset7['stringid'] . "'";
      $sqla = "UPDATE string set string_number = string_number + 1 WHERE stringid ='" . $_POST['stringid'] . "'";
      $sqlb = "UPDATE string set string_number = string_number - 1 WHERE stringid ='" . $row_Recordset7['stringidc'] . "'";
      $sqlc = "UPDATE string set string_number = string_number + 1 WHERE stringid ='" . $_POST['stringidc'] . "'";
    }
    mysqli_query($conn, $sql);
    mysqli_query($conn, $sqla);
    mysqli_query($conn, $sqlb);
    mysqli_query($conn, $sqlc);
  } else {
    //scenarion 4: Main nothing changes

  }
  //-------------------------------------------------------
  //lets add the image if there is one to the image table in the DB
  if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
    $image = $_FILES['image']['tmp_name'];
    $imgContent = file_get_contents($image);

    // Insert image data into database as BLOB
    $sql = "INSERT INTO images(image) VALUES(?) ";
    $statement = $conn->prepare($sql);
    $statement->bind_param('s', $imgContent);
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
  $sql = "UPDATE customer
  set Name='" . $_POST['customername'] .
    "', Email='" . $_POST['customeremail'] .
    "', Mobile='" . $_POST['customermobile'] .
    "', pref_string='" . $_POST['stringid'] .
    "', pref_stringc='" . $_POST['stringidc'] .
    "', tension='" . $_POST['tension'] .
    "', tensionc='" . $_POST['tensionc'] .
    "', prestretch='" . $_POST['preten'] .
    "', racketid='" . $_POST['racketid'] .
    "', Notes='" . $_POST['comments'] . "' WHERE cust_ID = '" . $_POST['customerid'] . "'";
  $_SESSION['message'] = "Customer modified Successfully";
  mysqli_query($conn, $sql);
  //redirect back to the main page.
  header("location:./customers.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//---Section to update racket-----------------------------------
//----------------------------------------------------------------

if (!empty($_POST['editracket'])) {
  $sql = "UPDATE rackets
  set manuf='" . $_POST['manuf'] .
    "', model='" . $_POST['model'] .
    "', sport='" . $_POST['sportid'] .
    "', pattern='" . $_POST['pattern'] . "' WHERE racketid = '" . $_POST['editracket'] . "'";
  $_SESSION['message'] = "Racket modified Successfully";
  mysqli_query($conn, $sql);
  //redirect back to the main page.
  header("location:./rackets.php"); //Redirecting To the main page
}
//----------------------------------------------------------------
//---Section to update stock string-----------------------------------
//----------------------------------------------------------------

if (!empty($_POST['editstockstring'])) {
  $sql = "UPDATE string
  set Owner_supplied='" . $_POST['ownersupplied'] .
    "', note='" . $_POST['notes'] .
    "', reel_price='" . $_POST['purchprice'] .
    "', racket_price='" . $_POST['racketprice'] .
    "', empty='" . $_POST['emptyreel'] .
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
  $sql = "UPDATE all_string
  set brand='" . $_POST['brand'] .
    "', notes='" . $_POST['notes'] .
    "', type='" . $_POST['type'] .
    "', sportid='" . $_POST['sportid'] .
    "', length='" . $_POST['length'] . "' WHERE string_id = '" . $_POST['editimstring'] . "'";
  $_SESSION['message'] = "In market string modified Successfully";
  mysqli_query($conn, $sql);
  //redirect back to the main page.
  header("location:./string-im.php"); //Redirecting To the main page
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
  //first lets check if the racket has any jobs assigned to it. If they do we will need to add a message to get the user to reassign new rackets in the releavnt jobs
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


  $query_Recordset8 = "SELECT * FROM string WHERE stock_id = " . $_POST['stockid'];
  $Recordset8 = mysqli_query($conn, $query_Recordset8) or die(mysqli_error($conn));
  $row_Recordset8 = mysqli_fetch_assoc($Recordset8);
  $number_of_reels = mysqli_num_rows($Recordset8);
  $number_of_reels++;

  $query_Recordset1 = "SELECT * FROM string";
  $Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
  $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
  $totalRows_Recordset1 = mysqli_num_rows($Recordset1);



  do {
    $last_id = $row_Recordset1['stringid'];
  } while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));
  $last_id++;

  $sql = "INSERT INTO string (stringid, stock_id, string_number, Owner_supplied, note, reel_no, reel_price, racket_price, empty, purchase_date) VALUES ('"
    . $last_id . "', '"
    . $_POST['stockid'] . "', '"
    . '0' . "', '"
    . $_POST['ownersupplied'] . "', '"
    . $_POST['notes'] . "', '"
    . $number_of_reels . "', '"
    . $_POST['purchprice'] . "', '"
    . $_POST['racketprice'] . "', '"
    . '0' . "', '"
    . $_POST['datepurch'] . "')";

  mysqli_query($conn, $sql);
  $_SESSION['message'] = "Reel added Successfully";
  //redirect back to the main page.
  header("location:$location"); //Redirecting To the main page
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

  if ($_POST['marker'] == 1) {
    $location = "./addavstring.php?marker=1";
  } elseif ($_POST['marker'] == 2) {
    $location = "./addcustomer.php?marker=2";
  } elseif ($_POST['marker'] == 3) {
    $location = "./string-im.php?marker=3";
  }

  if (empty($_POST['notes'])) {
    $_POST['notes'] = ' ';
  }

  $sql = "INSERT INTO all_string (string_id, brand, type, sportid, length, notes) VALUES ('"
    . $last_id . "', '"
    . $_POST['brand'] . "', '"
    . $_POST['type'] . "', '"
    . $_POST['sport'] . "', '"
    . $_POST['length'] . "', '"
    . $_POST['notes'] . "')";

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

  $sql = "INSERT INTO rackets (racketid, manuf, model, pattern, sport) VALUES ('"
    . $last_id . "', '"
    . $_POST['brand'] . "', '"
    . $_POST['type'] . "', '"
    . $_POST['pattern'] . "', '"
    . $_POST['sport'] . "')";

  mysqli_query($conn, $sql);
  $_SESSION['message'] = "Racket added Successfully";
  //redirect back to the main page.
  header("location:$location"); //Redirecting To the main page
}
//---------------------------------------------------------------
//------------------------Add new customer to DB-----------------
//----------------------------------------------------------------
if (isset($_POST['submitaddcust'])) {

  if (!isset($_POST['preten'])) {
    $_POST['preten'] = '0';
  }
  $sql = "INSERT INTO customer (Name, Email, Mobile, pref_string, pref_stringc, tension, tensionc, prestretch, racketid, Notes) VALUES ('"
    . $_POST['customername'] . "', '"
    . $_POST['customeremail'] . "', '"
    . $_POST['customermobile'] . "', '"
    . $_POST['stringid'] . "', '"
    . $_POST['stringidc'] . "', '"
    . $_POST['tension'] . "', '"
    . $_POST['tensionc'] . "', '"
    . $_POST['preten'] . "', '"
    . $_POST['racketid'] . "', '"
    . $_POST['comments'] . "')";

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
//----------------------------------------------------------------
//------------------Section to add a new job  to DB---------------
//----------------------------------------------------------------

if (isset($_POST['submitadd'])) {
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

  $query_Recordset1 = "SELECT * FROM stringjobs ORDER BY job_id DESC LIMIT 1";
  $Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
  $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
  $totalRows_Recordset1 = mysqli_num_rows($Recordset1);


  if ($_POST['$stringidc'] == 0) {
    $_POST['$stringidc'] = $_POST['$stringid'];
    $_POST['$tensionc'] = $_POST['$tension'];
  }

  do {
    $last_id = $row_Recordset1['job_id'];
  } while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));
  $last_id++;
  $sql = "INSERT INTO stringjobs (job_id, customerid, stringid, stringidc, racketid, collection_date, delivery_date, pre_tension, tension, tensionc, grip_required, paid, delivered, comments, free_job ) VALUES ('"
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
    . $_POST['comments'] . "', '"
    . $_POST['freerestring'] . "')";
  mysqli_query($conn, $sql) or die(mysqli_error($conn));




  $query_Recordset2 = "SELECT * FROM string LEFT JOIN all_string on string.stock_id = all_string.string_id where  stringid =" . $_POST['stringid'];
  $Recordset2 = mysqli_query($conn, $query_Recordset2) or die(mysqli_error($conn));
  $row_Recordset2 = mysqli_fetch_assoc($Recordset2);
  $totalRows_Recordset2 = mysqli_num_rows($Recordset2);
  do {
    if ($_POST['freerestring'] == 1) {
      $price = 0 + $gripprice;
    } else {

      $price = $row_Recordset2['racket_price'] + $gripprice;
    }
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

    // Insert image data into database as BLOB
    $sql = "INSERT INTO images(image) VALUES(?) ";
    $statement = $conn->prepare($sql);
    $statement->bind_param('s', $imgContent);
    $current_id = $statement->execute() or die("<b>Error:</b> Problem on Image Insert<br/>" . mysqli_connect_error());
    $last_id1 = $conn->insert_id;
    mysqli_query($conn, "UPDATE stringjobs set imageid='" . $last_id1 . "' WHERE job_id ='" . $last_id . "'");

    $conn->close();
  }

  $_SESSION['message'] = "Job added Successfully";
  //redirect back to the main page.
  header("location:./string-jobs.php"); //Redirecting To the main page
}
