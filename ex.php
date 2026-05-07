<?php
// (c) Xavier Nicolay
require_once('Connections/wcba.php');
require_once('phpqrcode/qrlib.php');

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

// SECURITY: Cast inputs to integers to prevent SQL Injection
$safe_customerid = isset($_GET['customerid']) ? (int)$_GET['customerid'] : 0;
$safe_jobid = isset($_GET['jobid']) ? (int)$_GET['jobid'] : 0;

// PERFORMANCE: Fetch all settings in ONE single query instead of 11 separate queries
$settings = [];
$query_settings = "SELECT id, value FROM settings WHERE id IN (2, 4, 5, 6, 7, 12, 13, 14, 15, 16, 17, 18)";
$result_settings = mysqli_query($conn, $query_settings) or die(mysqli_error($conn));
while ($row = mysqli_fetch_assoc($result_settings)) {
    $settings[$row['id']] = $row['value'];
}

// Map settings array to variables
$currency_setting = $settings[2] ?? '3';
$accname = $settings[4] ?? '';
$accnum = $settings[5] ?? '';
$scode = $settings[6] ?? '';
$domain_url = $settings[7] ?? '';
$compname = $settings[12] ?? '';
$address = $settings[13] ?? '';
$town = $settings[14] ?? '';
$county = $settings[15] ?? '';
$postcode = $settings[16] ?? '';
$email = $settings[17] ?? '';
$tel = $settings[18] ?? '';

// Determine Currency
switch ($currency_setting) {
    case "1":
        $currency = "$";
        break;
    case "2":
        $currency = "€";
        break;
    case "3":
        $currency = "£";
        break;
    case "4":
        $currency = "$";
        break;
    case "5":
        $currency = "$";
        break;
    case "6":
        $currency = "元";
        break;
    case "7":
        $currency = "₹";
        break;
    case "8":
        $currency = "¥";
        break;
    case "9":
        $currency = "₽";
        break;
    default:
        $currency = "£";
        break;
}

//-------------------------------------------------------
// Fetch Invoices
$query_Recordset1 = "SELECT 
    stringjobs.job_id as job_id, stringjobs.customerid as customerid,
    stringjobs.tension as atension, stringjobs.tensionc as atensionc,
    stringjobs.pre_tension as pre_tension, stringjobs.price as price,
    stringjobs.collection_date as collection_date, stringjobs.delivery_date as delivery_date,
    stringjobs.grip_required as grip_required, stringjobs.paid as paid,
    stringjobs.delivered as delivered, stringjobs.free_job as free_job,
    stringjobs.comments as comments, stringjobs.racketid as racketid,
    stringjobs.stringid as stringid, stringjobs.stringidc as stringidc,
    customer.Name as Name, customer.Email as Email, customer.Mobile as Mobile,
    sport.sportname as sportname, rackets.manuf as manuf, rackets.model as model,
    rackets.pattern as pattern, all_string.notes as notes_stock, all_stringc.notes as notesc_stock,
    string.note as notes_string, string.string_number as stringm_number,
    stringc.note as notesc_string, stringc.string_number as stringc_number,
    all_string.brand as brandm, all_string.type as typem,
    all_stringc.brand as brandc, all_stringc.type as typec,
    string.stringid as stringid_m, stringc.stringid as stringid_c,
    reel_lengthsm.length as lengthm, reel_lengthsc.length as lengthc
    FROM stringjobs
    LEFT JOIN customer ON customerid = cust_ID
    LEFT JOIN string ON stringjobs.stringid = string.stringid 
    LEFT JOIN string AS stringc ON stringjobs.stringidc = stringc.stringid
    LEFT JOIN all_string ON string.stock_id = all_string.string_id
    LEFT JOIN all_string AS all_stringc ON stringc.stock_id = all_stringc.string_id
    LEFT JOIN reel_lengths AS reel_lengthsm ON reel_lengthsm.reel_length_id = string.lengthid
    LEFT JOIN reel_lengths AS reel_lengthsc ON reel_lengthsc.reel_length_id = string.lengthid
    LEFT JOIN rackets ON stringjobs.racketid = rackets.racketid 
    LEFT JOIN sport ON all_string.sportid = sport.sportid 
    WHERE customerid = '$safe_customerid' AND paid = '0'";

$Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);

if ($totalRows_Recordset1 == 0) {
    $_SESSION['message'] = "No outstanding invoices to pay";
    header("location: ./viewjob.php?jobid=$safe_jobid");
    exit;
}

// Generate QR Code with dynamic Job ID (Fixed bug where this was hardcoded to 101444)
$qr_url = "http://" . $domain_url . "/viewjob.php?jobid=" . $safe_jobid;
QRcode::png($qr_url, './img/qrcode.png', 'L', 4, 2);

// Setup PDF Variables
$row1 = "Restring No: " . $row_Recordset1['job_id'];
$Name = $row_Recordset1['Name'];
$Mobile = $row_Recordset1['Mobile'];
$Email = $row_Recordset1['Email'];
$CustID = $row_Recordset1['customerid'];

$row3 = $row_Recordset1['collection_date'];
$row4 = $row_Recordset1['manuf'] . " " . $row_Recordset1['model'];
$row5a = $row_Recordset1['brandm'] . " " . $row_Recordset1['typem'];
$row5b = $row_Recordset1['brandc'] . " " . $row_Recordset1['typec'];
$row6a = $row_Recordset1['atension'];
$row6b = $row_Recordset1['atensionc'];

//Check for crosses
if ($row_Recordset1['stringidc'] == 0) {
    $row5b = "N/A";
    $row6b = "N/A";
}

$row7 = $row_Recordset1['pre_tension'] . "%";
$row8 = ($row_Recordset1['grip_required'] == 0) ? "No" : "Yes";

$html = html_entity_decode(htmlentities("$currency", ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'ISO-8859-1');
$row9a = $html . $row_Recordset1['price'];
$row9b = ($row_Recordset1['paid'] == 0) ? "No" : "Yes";

// Load FPDF 
require('invoice.php');

$row1 = $row_Recordset1['delivery_date'];
$row2 = $row_Recordset1['job_id'];
$row3 = "restring of a " . $row_Recordset1['manuf'] . " " . $row_Recordset1['model'];

//---------Start of PDF creation
$pdf = new PDF_Invoice('P', 'mm', 'A4');
$pdf->AddPage();

//address box top left------------------------------------
$pdf->addSociete(
    "$compname",
    "$address\n" . "$town\n" . "$county\n" . "$postcode\n" . "$email\n" . "Tel: $tel "
);

//--------------------------------------------------------
//Info box top right--------------------------------------
$pdf->fact_dev("Payment", "Due");
$pdf->temporaire("INVOICE");
$pdf->addDate($row1);
$pdf->addClient("ID: $CustID");
$pdf->addPageNumber("1");
$pdf->addClientAdresse("$Name\nTel: $Mobile\nEmail: $Email");
$pdf->addReglement("Online Payment");
$pdf->addEcheance($row1);
$pdf->addNumTVA("Acc No:" . $accnum . "  SC: " . $scode);
$pdf->addReference("");

//----------------------------------------------------------
//Column widths
$cols = array(
    "JOBID"       => 23,
    "DESCRIPTION" => 80,
    "QTY"         => 20,
    "DATE"        => 26,
    "PRICE"       => 30,
);

//-----------------------------------------------------------
//Justify columns--------------------------------------------
$pdf->addCols($cols);
$cols = array(
    "JOBID"       => "L",
    "DESCRIPTION" => "L",
    "QTY"         => "C",
    "DATE"        => "R",
    "PRICE"       => "R",
);
$pdf->addLineFormat($cols);

$y = 109;
$total_price = 0;

//-----------------------------------------------------------
//Add rows of content
do {
    $jobdescription = "Restring of a " . $row_Recordset1['manuf'] . " " . $row_Recordset1['model'] . " with " . $row_Recordset1['brandm'] . " " . $row_Recordset1['typem'];

    if (($row_Recordset1['stringid'] != $row_Recordset1['stringidc']) && ($row_Recordset1['stringidc'] != 0)) {
        $jobdescription .= " and " . $row_Recordset1['brandc'] . " " . $row_Recordset1['typec'];
    }
    if ($row_Recordset1['grip_required'] == 1) {
        $jobdescription .=  " plus a grip";
    }

    //Add 1 row content
    $line = array(
        "JOBID"       => $row_Recordset1['job_id'],
        "DESCRIPTION" => $jobdescription,
        "QTY"         => "1",
        "DATE"        => $row_Recordset1['delivery_date'],
        "PRICE"       => EURO . $row_Recordset1['price'], // Note: EURO constant comes from invoice.php
    );
    $total_price += $row_Recordset1['price'];
    $size = $pdf->addLine($y, $line);
    $y += $size + 2;
} while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));

$total_price = EURO . $total_price;

// Output PDF
$pdf->addCadreEurosFrancs($total_price);
$pdf->Output();
