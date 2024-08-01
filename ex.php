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
//load all of the DB Queries
$sql = "SELECT * FROM settings where id = 2";
$Recordset2 = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$row_Recordset2 = mysqli_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysqli_num_rows($Recordset2);
//-------------------------------------------------------
switch ($row_Recordset2['value']) {
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
}
//-------------------------------------------------------
$query_Recordset1 = "SELECT 
stringjobs.job_id as job_id,
stringjobs.customerid as customerid,
stringjobs.tension as atension,
stringjobs.tensionc as atensionc,
stringjobs.pre_tension as pre_tension,
stringjobs.price as price,
stringjobs.collection_date as collection_date,
stringjobs.delivery_date as delivery_date,
stringjobs.grip_required as grip_required,
stringjobs.paid as paid,
stringjobs.delivered as delivered,
stringjobs.free_job as free_job,
stringjobs.comments as comments,
stringjobs.racketid as racketid,
stringjobs.stringid as stringid,
stringjobs.stringidc as stringidc,
customer.Name as Name,
customer.Email as Email,
customer.Mobile as Mobile,
sport.sportname as sportname,
rackets.manuf as manuf,
rackets.model as model,
rackets.pattern as pattern,
all_string.notes as notes_stock,
all_stringc.notes as notesc_stock,
string.note as notes_string,
string.string_number as stringm_number,
string.note as notes_string,
stringc.note as notesc_string,
stringc.string_number as stringc_number,
all_string.brand as brandm,
all_string.type as typem,
all_stringc.brand as brandc,
all_stringc.type as typec,
string.stringid as stringid_m,
stringc.stringid as stringid_c,
reel_lengthsm.length as lengthm,
reel_lengthsc.length as lengthc
FROM stringjobs
LEFT JOIN customer ON customerid = cust_ID
LEFT JOIN string ON stringjobs.stringid = string.stringid 
LEFT JOIN string AS stringc ON stringjobs.stringidc = stringc.stringid
LEFT JOIN all_string ON string.stock_id = all_string.string_id
LEFT JOIN all_string AS all_stringc ON stringc.stock_id = all_stringc.string_id
LEFT JOIN reel_lengths
AS reel_lengthsm
ON reel_lengthsm.reel_length_id = string.lengthid
LEFT JOIN reel_lengths
AS reel_lengthsc
ON reel_lengthsc.reel_length_id = string.lengthid
LEFT JOIN rackets ON stringjobs.racketid = rackets.racketid 
LEFT JOIN sport ON all_string.sportid = sport.sportid 
WHERE job_id = '101444' AND paid = '0'";
$Recordset1 = mysqli_query($conn, $query_Recordset1) or die(mysqli_error($conn));
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);
//-------------------------------------------------------
$query_Recordset3 = "SELECT * FROM customer ORDER BY Name ASC;";
$Recordset3 = mysqli_query($conn, $query_Recordset3) or die(mysqli_error($conn));
$row_Recordset3 = mysqli_fetch_assoc($Recordset3);
$totalRows_Recordset3 = mysqli_num_rows($Recordset3);
//-------------------------------------------------------
$query_Recordset4 = "SELECT * FROM rackets ORDER BY manuf ASC;";
$Recordset4 = mysqli_query($conn, $query_Recordset4) or die(mysqli_error($conn));
$row_Recordset4 = mysqli_fetch_assoc($Recordset4);
$totalRows_Recordset4 = mysqli_num_rows($Recordset4);
//-------------------------------------------------------
$query_Recordset7 = "SELECT * FROM string LEFT JOIN all_string ON string.stock_id = all_string.string_id WHERE empty = '0' ORDER BY string.stringid ASC;";
$Recordset7 = mysqli_query($conn, $query_Recordset7) or die(mysqli_error($conn));
$row_Recordset7 = mysqli_fetch_assoc($Recordset7);
$totalRows_Recordset7 = mysqli_num_rows($Recordset7);
//-------------------------------------------------------
$query_Recordset8 = "SELECT * FROM string LEFT JOIN all_string ON string.stock_id = all_string.string_id WHERE empty = '0' ORDER BY string.stringid ASC;";
$Recordset8 = mysqli_query($conn, $query_Recordset8) or die(mysqli_error($conn));
$row_Recordset8 = mysqli_fetch_assoc($Recordset8);
$totalRows_Recordset8 = mysqli_num_rows($Recordset8);
//-------------------------------------------------------
$query_Recordset5 = "SELECT * FROM grip;";
$Recordset5 = mysqli_query($conn, $query_Recordset5) or die(mysqli_error($conn));
$row_Recordset5 = mysqli_fetch_assoc($Recordset5);
$totalRows_Recordset5 = mysqli_num_rows($Recordset5);
//-------------------------------------------------------
$sqlb = "SELECT * FROM settings where id ='4'";
$Recordset10 = mysqli_query($conn, $sqlb) or die(mysqli_error($conn));
$row_Recordset10 = mysqli_fetch_assoc($Recordset10);
$totalRows_Recordset10 = mysqli_num_rows($Recordset10);
//-------------------------------------------------------
$sqlb = "SELECT * FROM settings where id ='5'";
$Recordset11 = mysqli_query($conn, $sqlb) or die(mysqli_error($conn));
$row_Recordset11 = mysqli_fetch_assoc($Recordset11);
$totalRows_Recordset11 = mysqli_num_rows($Recordset11);
//-------------------------------------------------------
$sqlb = "SELECT * FROM settings where id ='6'";
$Recordset12 = mysqli_query($conn, $sqlb) or die(mysqli_error($conn));
$row_Recordset12 = mysqli_fetch_assoc($Recordset12);
$totalRows_Recordset12 = mysqli_num_rows($Recordset12);
//-------------------------------------------------------
$sqlb = "SELECT * FROM settings where id ='7'";
$Recordset13 = mysqli_query($conn, $sqlb) or die(mysqli_error($conn));
$row_Recordset13 = mysqli_fetch_assoc($Recordset13);
$totalRows_Recordset13 = mysqli_num_rows($Recordset13);
//-------------------------------------------------------
$accname = $row_Recordset10['value'];
$accnum = $row_Recordset11['value'];
$scode = $row_Recordset12['value'];
$jobid = "http://" . $row_Recordset13['value'] . "/viewjob.php?jobid=101444";
QRcode::png($jobid, './img/qrcode.png', 'L', 4, 2);
$row1 = "Restring No: " . $row_Recordset1['job_id'];
$row2 = $row_Recordset1['Name'];
$row3 = $row_Recordset1['collection_date'];
$row4 = $row_Recordset1['manuf'] . " " . $row_Recordset1['model'];
$row5a = $row_Recordset1['brandm'] . " " . $row_Recordset1['typem'];
$row5b = $row_Recordset1['brandc'] . " " . $row_Recordset1['typec'];
$row6a = $row_Recordset1['atension'];
$row6b = $row_Recordset1['atensionc'];
//Check for crosses
if ($row_Recordset1['stringidc'] == 0) {
    $row5b = "N/A";
}
if ($row_Recordset1['stringidc'] == 0) {
    $row6b = "N/A";
}
$row7 = $row_Recordset1['pre_tension'] . "%";
if ($row_Recordset1['grip_required'] == 0) {
    $row8 = "No";
} else {
    $row8 = "Yes";
}
$html = html_entity_decode(htmlentities("$currency", ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'ISO-8859-1');
$row9a = $html . $row_Recordset1['price'];
if ($row_Recordset1['paid'] == 0) {
    $row9b = "No";
} else {
    $row9b = "Yes";
}
require('invoice.php');
$row1 = $row_Recordset1['delivery_date'];
$row2 = $row_Recordset1['job_id'];
$row3 = "restring of a " . $row_Recordset1['manuf'] . $row_Recordset1['model'];

//---------Start of PDF creation
$pdf = new PDF_Invoice('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->addSociete(
    "CREative Restrings",
    "65a Quakers Rd\n" .
        "Devizes\n" .
        "Wiltshire\n" .
        "SN10 2FH "
);
$pdf->fact_dev("Invoice", "TEMPO");
$pdf->temporaire("INVOICE");
$pdf->addDate($row1);
$pdf->addClient("CL01");
$pdf->addPageNumber("1");
$pdf->addClientAdresse("John Doe\nTel: 07769354882\nEmail: Hackenbecker@gmil.com");
$pdf->addReglement("Online Payment");
$pdf->addEcheance($row1);
$pdf->addNumTVA("FR888777666");
$pdf->addReference("");
$cols = array(
    "REFERENCE"    => 23,
    "DESIGNATION"  => 78,
    "QUANTITE"     => 22,
    "P.U. HT"      => 26,
    "MONTANT H.T." => 30,
    "TVA"          => 11
);
$pdf->addCols($cols);
$cols = array(
    "REFERENCE"    => "L",
    "DESIGNATION"  => "L",
    "QUANTITE"     => "C",
    "P.U. HT"      => "R",
    "MONTANT H.T." => "R",
    "TVA"          => "C"
);
$pdf->addLineFormat($cols);
$pdf->addLineFormat($cols);

$y    = 109;


$line = array(
    "REFERENCE"    => $row2,
    "DESIGNATION"  => $row3,
    "QUANTITE"     => "1",
    "P.U. HT"      => "600.00",
    "MONTANT H.T." => "600.00",
    "TVA"          => "1"
);





$size = $pdf->addLine($y, $line);
$y   += $size + 2;

$line = array(
    "REFERENCE"    => "REF2",
    "DESIGNATION"  => "C�ble RS232",
    "QUANTITE"     => "1",
    "P.U. HT"      => "10.00",
    "MONTANT H.T." => "60.00",
    "TVA"          => "1"
);
$size = $pdf->addLine($y, $line);
$y   += $size + 2;

$pdf->addCadreTVAs();

// invoice = array( "px_unit" => value,
//                  "qte"     => qte,
//                  "tva"     => code_tva );
// tab_tva = array( "1"       => 19.6,
//                  "2"       => 5.5, ... );
// params  = array( "RemiseGlobale" => [0|1],
//                      "remise_tva"     => [1|2...],  // {la remise s'applique sur ce code TVA}
//                      "remise"         => value,     // {montant de la remise}
//                      "remise_percent" => percent,   // {pourcentage de remise sur ce montant de TVA}
//                  "FraisPort"     => [0|1],
//                      "portTTC"        => value,     // montant des frais de ports TTC
//                                                     // par defaut la TVA = 19.6 %
//                      "portHT"         => value,     // montant des frais de ports HT
//                      "portTVA"        => tva_value, // valeur de la TVA a appliquer sur le montant HT
//                  "AccompteExige" => [0|1],
//                      "accompte"         => value    // montant de l'acompte (TTC)
//                      "accompte_percent" => percent  // pourcentage d'acompte (TTC)
//                  "Remarque" => "texte"              // texte
$tot_prods = array(
    array("px_unit" => 600, "qte" => 1, "tva" => 1),
    array("px_unit" =>  10, "qte" => 1, "tva" => 1)
);
$tab_tva = array(
    "1"       => 19.6,
    "2"       => 5.5
);
$params  = array(
    "RemiseGlobale" => 1,
    "remise_tva"     => 1,       // {la remise s'applique sur ce code TVA}
    "remise"         => 0,       // {montant de la remise}
    "remise_percent" => 10,      // {pourcentage de remise sur ce montant de TVA}
    "FraisPort"     => 1,
    "portTTC"        => 10,      // montant des frais de ports TTC
    // par defaut la TVA = 19.6 %
    "portHT"         => 0,       // montant des frais de ports HT
    "portTVA"        => 19.6,    // valeur de la TVA a appliquer sur le montant HT
    "AccompteExige" => 1,
    "accompte"         => 0,     // montant de l'acompte (TTC)
    "accompte_percent" => 15,    // pourcentage d'acompte (TTC)
    "Remarque" => "Avec un acompte, svp..."
);

$pdf->addTVAs($params, $tab_tva, $tot_prods);
$pdf->addCadreEurosFrancs();
$pdf->Output();