<?php
session_start();
require __DIR__ . "../../vendor/autoload.php";

use Dompdf\Dompdf;
use Dompdf\Options;

require_once "../../../processphp/config.php"; // This file should define both $con (mysqli) and $connection (PDO)

// --- 1. GET AND VALIDATE INPUT ---
if (empty($_GET['lumber_app_id'])) {
    die("Error: lumber_app_id is required.");
}
$lumber_app_id = $_GET['lumber_app_id'];


// --- 2. UPDATE c_endorsement (Already safe - uses prepared statement) ---
$formattedDate = date('F j, Y');
$stmt = $con->prepare("UPDATE c_endorsement SET date_penro = ? WHERE lumber_app_id = ?");
$stmt->bind_param("si", $formattedDate, $lumber_app_id);

if ($stmt->execute()) {
    echo "Record updated successfully. Affected rows: " . $stmt->affected_rows;
} else {
    echo "Error updating record: " . $stmt->error;
}
$stmt->close();


// --- 3. FETCH DATA (NOW USING SECURE PREPARED STATEMENTS) ---

// Get main endorsement data
$stmt = $con->prepare("SELECT * FROM c_endorsement WHERE lumber_app_id = ?");
$stmt->bind_param("i", $lumber_app_id);
$stmt->execute();
$lumber_app_qry = $stmt->get_result();
$lumber_ap_row = $lumber_app_qry->fetch_assoc();
$stmt->close();

// !! IMPORTANT FIX FOR WARNINGS !!
// If no record is found, stop the script. This prevents all 'null' warnings later.
if (!$lumber_ap_row) {
    die("<br>Error: No endorsement record found for lumber_app_id: " . htmlspecialchars($lumber_app_id));
}

// Get payment data
$stmt = $con->prepare("SELECT * FROM payment_feny WHERE lumber_app_id = ?");
$stmt->bind_param("i", $lumber_app_id);
$stmt->execute();
$lumber_app_qry1 = $stmt->get_result();
$lumber_ap_row1 = $lumber_app_qry1->fetch_assoc();
$stmt->close();

// Use null coalescing (??) to prevent errors if no payment record exists
$refnumber = $lumber_ap_row1['Reference_Number'] ?? 'N/A';
$datepaid = $lumber_ap_row1['Date_payment'] ?? null;


// Get lumber application data
$stmt = $con->prepare("SELECT * FROM lumber_application WHERE lumber_app_id = ?");
$stmt->bind_param("i", $lumber_app_id);
$stmt->execute();
$lumber_app_qry = $stmt->get_result();
$lumber_app_details_row = $lumber_app_qry->fetch_assoc();
$stmt->close();

if (!$lumber_app_details_row) {
    die("<br>Error: No lumber application record found for lumber_app_id: " . htmlspecialchars($lumber_app_id));
}

// Assign variables from the application details
$municipal_qry_result = $lumber_app_details_row['Office'];
$office_under = $lumber_app_details_row['office_under'];
$_office_cover = $lumber_app_details_row['Office'];
$suffix = $lumber_app_details_row['Suffix'];
$Flow_stat = $lumber_app_details_row['Flow_stat'];
$date_applied = $lumber_app_details_row['date_applied']; // Keep original string for signature query


// Get order of payment data
$stmt = $con->prepare("SELECT * FROM order_of_payment WHERE lumber_app_id = ?");
$stmt->bind_param("i", $lumber_app_id);
$stmt->execute();
$lumber_app_qry = $stmt->get_result();
$result_orderofpayment = $lumber_app_qry->fetch_assoc();
$stmt->close();

$cashbond = $result_orderofpayment['cash'] ?? 0;
$Amount_Decimal = $result_orderofpayment['Amount_Decimal'] ?? 0;


// --- 4. SIGNATURE LOGIC (Already safe - uses prepared statement) ---
$query = "
    SELECT * FROM signatory_managerdb 
    WHERE official_station = ? 
      AND signature_type = 'Endorsement' 
      AND signature_order = '1'
      AND (
          ? BETWEEN date_started AND date_ended 
          OR date_ended = ''
      )
";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "ss", $office_under, $date_applied);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $lumber_ap_row22 = mysqli_fetch_assoc($result);
    $signature_1 = $lumber_ap_row22['signature_file'] ?? null;
} else {
    echo "Signature query failed: " . mysqli_error($con);
}
mysqli_stmt_close($stmt);

// Copy signature file
if ($signature_1) {
    $file1 = "../../../admin/uploads/$signature_1";
    $dest_file = 'uploads/' . $signature_1;
    if (!file_exists('uploads/')) {
        mkdir('uploads/', 0777, true);
    }
    if (file_exists($file1)) {
        copy($file1, $dest_file);
    }
}


// --- 5. ASSIGN VARIABLES FOR PDF TEMPLATE ---
// These are now safe because of the check for $lumber_ap_row above
$full_name = $lumber_ap_row['full_name'];
// !! FATAL ERROR FIX: DO NOT OVERWRITE $lumber_app_id !!
// $lumber_app_id  = $lumber_ap_row['lumber_app_id']; // <-- THIS WAS THE BUG. REMOVED.
$penroaddress = $lumber_ap_row['penroaddress'];
$office_address = $lumber_ap_row['office_under'];
$office_under = $lumber_ap_row['office_under'];
$bussiness_name = $lumber_ap_row['bussiness_name'];
$full_address = $lumber_ap_row['full_address'];
$date_ = $lumber_ap_row['date_'];
$date_penro = $lumber_ap_row['date_penro'];
$ldname = $lumber_ap_row['ldname'];
$owner = $lumber_ap_row['owner'];
$ldaddress = $lumber_ap_row['ldaddress'];
$MPdateissued = $lumber_ap_row['MPdateissued'];
$MPdateexpiry = $lumber_ap_row['MPdateexpiry'];
$BNNumber = $lumber_ap_row['BNNumber'];
$DTIdateissued = $lumber_ap_row['DTIdateissued'];
$DTIdateexpiry = $lumber_ap_row['DTIdateexpiry'];
$SCtype = ""; // These were initialized as empty
$municipal = "";
$province2 = "";
$totalsupply = $lumber_ap_row['totalsupply']; // This will be overwritten
$particulars = $lumber_ap_row['particulars'];
$treespecie = ""; // This will be overwritten
$date_ = $lumber_ap_row['date_'];


// --- 6. EFFICIENTLY FETCH DATA WITH PDO (FIXED QUERIES) ---
// Use PDO ($connection) as in original code, but now securely

// Consolidate all queries on supp_contdetails
$stmt = $connection->prepare("
    SELECT 
        SUM(result) as total_supply, 
        GROUP_CONCAT(DISTINCT Species SEPARATOR ', ') as all_species,
        GROUP_CONCAT(DISTINCT ownername SEPARATOR ', ') as all_owners,
        MAX(validity_val) as yr_validity,
        MAX(office_cover) as office_cover
    FROM supp_contdetails  
    WHERE lumber_app_id = ?
");
$stmt->execute([$lumber_app_id]);
$supp_row = $stmt->fetch(PDO::FETCH_ASSOC);

$totalsupply = $supp_row['total_supply'] ?? $totalsupply; // Use fetched value, or keep previous
$treespecie = $supp_row['all_species'] ?? '';
$lsname = $supp_row['all_owners'] ?? '';
$yrvalidity = $supp_row['yr_validity'] ?? '';
$office_cover = $supp_row['office_cover'] ?? '';

// Get site visit date
$stmt = $connection->prepare("SELECT Date_from FROM calendar_sitevisit_db WHERE lumber_app_id = ? ORDER BY Date_from DESC LIMIT 1");
$stmt->execute([$lumber_app_id]);
$datevalidation = $stmt->fetchColumn() ?? '';

// Consolidate queries on payment_feny (if needed again)
$stmt = $connection->prepare("SELECT Reference_Number, Date_payment FROM payment_feny WHERE lumber_app_id = ? ORDER BY Date_payment DESC LIMIT 1");
$stmt->execute([$lumber_app_id]);
$payment_row = $stmt->fetch(PDO::FETCH_ASSOC);

// Re-assign $refnumber and $datepaid from PDO connection, keeping old value if null
$refnumber = $payment_row['Reference_Number'] ?? $refnumber;
$datepaid = $payment_row['Date_payment'] ?? $datepaid;


// --- 7. GENERATE PDF (No changes needed) ---
ob_start();
include "template_short_PENRO_ENDRM.php";
$html = ob_get_clean();
ob_end_clean();

$options = new Options;
$options->setChroot(__DIR__);
$options->setIsRemoteEnabled(true);

$dompdf = new Dompdf($options);
$dompdf->setPaper("LEGAL", "portrait");
$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->addInfo("Title", "ENDORSEMENT");
$dompdf->stream("../endorsement.pdf", ["Attachment" => 0]);

$output = $dompdf->output();
file_put_contents("file.pdf", $output);


// --- 8. UPDATE DATABASE RECORDS (Corrected Logic) ---

// Check if record ALREADY EXISTS in p_endorsement
$stmt = $connection->prepare("SELECT lumber_app_id FROM p_endorsement WHERE lumber_app_id = ?");
$stmt->execute([$lumber_app_id]);
$p_endorsement_exists = $stmt->fetch();

if (!$p_endorsement_exists) {
    // It does not exist, so INSERT
    $query = $connection->prepare("INSERT INTO p_endorsement(
        lumber_app_id, office_address, office_under, penroaddress, bussiness_name, 
        full_address, date_, ldname, owner, ldaddress, MPdateissued, MPdateexpiry, 
        BNNumber, DTIdateissued, DTIdateexpiry, SCtype, municipal, province2, 
        totalsupply, particulars, treespecie 
    ) VALUES (
        :lumber_app_id, :office_address, :office_under, :penroaddress, :bussiness_name, 
        :full_address, :date_, :ldname, :owner, :ldaddress, :MPdateissued, :MPdateexpiry, 
        :BNNumber, :DTIdateissued, :DTIdateexpiry, :SCtype, :municipal, :province2, 
        :totalsupply, :particulars, :treespecie
    )");

    $query->execute([
        ':lumber_app_id' => $lumber_app_id,
        ':office_address' => $office_address,
        ':office_under' => $office_under,
        ':penroaddress' => $penroaddress,
        ':bussiness_name' => $bussiness_name,
        ':full_address' => $full_address,
        ':date_' => $date_,
        ':ldname' => $ldname,
        ':owner' => $owner,
        ':ldaddress' => $ldaddress,
        ':MPdateissued' => $MPdateissued,
        ':MPdateexpiry' => $MPdateexpiry,
        ':BNNumber' => $BNNumber,
        ':DTIdateissued' => $DTIdateissued,
        ':DTIdateexpiry' => $DTIdateexpiry,
        ':SCtype' => $SCtype,
        ':municipal' => $municipal,
        ':province2' => $province2,
        ':totalsupply' => $totalsupply,
        ':particulars' => $particulars,
        ':treespecie' => $treespecie
    ]);
}

// Check if doc '10' exists
$doc_number_10 = '10';
$stmt = $con->prepare("SELECT Number_of_doc FROM lumber_app_doc_erow WHERE lumber_app_id = ? AND Number_of_doc = ?");
$stmt->bind_param("is", $lumber_app_id, $doc_number_10);
$stmt->execute();
$lumber_app_doc_qry = $stmt->get_result();
$lumber_ap_doc_row = $lumber_app_doc_qry->fetch_assoc();
$stmt->close();

if (!$lumber_ap_doc_row) {
    // It does not exist, so INSERT doc '10'
    $doc_type_name = 'Endorsement for PENRO ';
    $date = date('m/d/y');
    $doc_status = 'For Review (FG)';

    $query = $connection->prepare("INSERT INTO lumber_app_doc_erow(
        lumber_app_id, doc_type_name, date_applied, Number_of_doc, doc_status
    ) VALUES (
        :lumber_app_id, :doc_type_name, :date_applied, :Number_of_doc, :doc_status
    )");
    $query->execute([
        ':lumber_app_id' => $lumber_app_id,
        ':doc_type_name' => $doc_type_name,
        ':date_applied' => $date,
        ':Number_of_doc' => $doc_number_10,
        ':doc_status' => $doc_status
    ]);

    // And UPDATE lumber_application status
    $sql = "UPDATE lumber_application SET Status = :Status, Flow_stat = :Flow_stat WHERE lumber_app_id = :lumber_app_id";
    $stmt = $connection->prepare($sql);
    $stmt->execute([
        ':Status' => 'For Initial Chief RPS',
        ':Flow_stat' => '7',
        ':lumber_app_id' => $lumber_app_id
    ]);
}


// Check if history record exists
$stmt = $con->prepare("SELECT lumber_app_id FROM client_client_document_history WHERE lumber_app_id = ? LIMIT 1");
$stmt->bind_param("i", $lumber_app_id);
$stmt->execute();
$history_qry = $stmt->get_result();
$history_row = $history_qry->fetch_assoc();
$stmt->close();

if (!$history_row) {
    // It does not exist, so INSERT
    date_default_timezone_set("Asia/Manila");
    $Time = date("h:i:sa");
    $date2 = date('m/d/y');
    $Title = 'PENRO FUU';
    $Details = 'Evaluated the endorsed application from the concerned CENROs.
    Forward the complete documents to the Chief RPS.
    Note: If there are discrepancies in the endorsed documents they will be returned to CENRO FUU. Both the applicant and the CENR Officer will be notified thru SMS.';

    $query = $connection->prepare("INSERT INTO client_client_document_history(
        lumber_app_id, Date, Title, Details, Time
    ) VALUES (
        :lumber_app_id, :Date, :Title, :Details, :Time
    )");
    $query->execute([
        ':lumber_app_id' => $lumber_app_id,
        ':Date' => $date2,
        ':Title' => $Title,
        ':Details' => $Details,
        ':Time' => $Time
    ]);
}

// UPDATE doc '11'
$doc_type_name = 'Endorsement for RED';
$date = date('m/d/y');
$Number_of_doc = '11';
$doc_status = 'For Review (FG) RED';

$sql = "UPDATE lumber_app_doc_erow SET 
    doc_type_name = :doc_type_name, 
    date_applied = :date_applied,
    doc_status = :doc_status
WHERE lumber_app_id = :lumber_app_id AND Number_of_doc = :Number_of_doc";

$stmt = $connection->prepare($sql);
$stmt->execute([
    ':doc_type_name' => $doc_type_name,
    ':date_applied' => $date,
    ':doc_status' => $doc_status,
    ':lumber_app_id' => $lumber_app_id,
    ':Number_of_doc' => $Number_of_doc
]);

?>