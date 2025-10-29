<?php
// =============================================================================
// SECTION 1: INITIALIZATION & SETUP
// =============================================================================

// Start the session to handle user data
session_start();

// Set the default timezone for date functions
date_default_timezone_set("Asia/Manila");

// Include the Composer autoloader for third-party libraries (like Dompdf)
require __DIR__ . "/vendor/autoload.php";

// Include your database configuration file
// IMPORTANT: This file should establish a PDO connection object named $connection
require_once "../../processphp/config.php";

// Use Dompdf classes
use Dompdf\Dompdf;
use Dompdf\Options;


// =============================================================================
// SECTION 2: INPUT VALIDATION
// =============================================================================

// Get the lumber application ID from the URL.
// Validate it to ensure it's a positive integer.
$lumber_app_id = isset($_GET['lumber_app_id']) ? filter_var($_GET['lumber_app_id'], FILTER_VALIDATE_INT) : 0;

// If the ID is not valid, stop the script with an error message.
if ($lumber_app_id <= 0) {
    die("Error: Invalid or missing Application ID.");
}


// =============================================================================
// SECTION 3: DATABASE OPERATIONS & BUSINESS LOGIC
// =============================================================================

try {
    // --- 3.1: FETCHING MAIN APPLICATION DATA ---
    // This single query joins multiple tables to get all necessary data at once.
    $sql = "SELECT 
                ce.*, 
                pf.Reference_Number, 
                pf.Date_payment,
                cal.Date_from AS validation_date_from_calendar
            FROM c_endorsement ce
            LEFT JOIN payment_feny pf ON ce.lumber_app_id = pf.lumber_app_id
            LEFT JOIN calendar_sitevisit_db cal ON ce.lumber_app_id = cal.lumber_app_id
            WHERE ce.lumber_app_id = :lumber_app_id";
    
    $stmt = $connection->prepare($sql);
    $stmt->execute([':lumber_app_id' => $lumber_app_id]);
    $appData = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no data is found for the main application, stop the script.
    if (!$appData) {
        die("No endorsement record found for the given Application ID.");
    }
    
    // Assign fetched data to variables for use in the template
    $office_address   = $appData['office_address'];
    $office_under     = $appData['office_under'];
    $penroaddress     = $appData['penroaddress'];
    $bussiness_name   = $appData['bussiness_name'];
    $date_            = $appData['date_'];
    $full_address     = $appData['full_address'];
    $ldname           = $appData['ldname'];
    $full_name        = $appData['full_name'];
    $ldaddress        = $appData['ldaddress'];
    $MPdateissued     = $appData['MPdateissued'];
    $MPdateexpiry     = $appData['MPdateexpiry'];
    $BNNumber         = $appData['BNNumber'];
    $DTIdateissued    = $appData['DTIdateissued'];
    $DTIdateexpiry    = $appData['DTIdateexpiry'];
    $refnumber        = $appData['Reference_Number'] ?? $appData['refnumber']; // Use payment ref if available
    $datepaid         = $appData['Date_payment'] ?? $appData['datepaid'];     // Use payment date if available
    $datevalidation   = $appData['validation_date_from_calendar'] ?? $appData['datevalidation'];


    // --- 3.2: FETCHING AGGREGATED SUPPLIER DATA ---
    // This query gets summarized data from the supplier details table.
    // GROUP_CONCAT is used to combine multiple rows of species/names into a single string.
    $sql_supp = "SELECT 
                    SUM(result) as total_supply,
                    GROUP_CONCAT(DISTINCT Species SEPARATOR ', ') as tree_species,
                    GROUP_CONCAT(DISTINCT ownername SEPARATOR ', ') as supplier_names,
                    validity_val,
                    office_cover
                 FROM supp_contdetails 
                 WHERE lumber_app_id = :lumber_app_id 
                 GROUP BY lumber_app_id, validity_val, office_cover";

    $stmt_supp = $connection->prepare($sql_supp);
    $stmt_supp->execute([':lumber_app_id' => $lumber_app_id]);
    $suppData = $stmt_supp->fetch(PDO::FETCH_ASSOC);

    // Assign supplier data to variables, with default values if none are found.
    $totalsupply    = $suppData['total_supply'] ?? 0;
    $treespecie     = $suppData['tree_species'] ?? 'N/A';
    $lsname         = $suppData['supplier_names'] ?? 'N/A';
    $yrvalidity     = $suppData['validity_val'] ?? 'N/A';
    $office_cover   = $suppData['office_cover'] ?? 'N/A';
    
    
    // --- 3.3: CHECK FOR EXISTING DOCUMENT AND UPDATE STATUS ---
    // Check if a document with Number_of_doc = '10' already exists.
    $checkSql = "SELECT COUNT(*) FROM lumber_app_doc_erow WHERE lumber_app_id = :lumber_app_id AND Number_of_doc = '10'";
    $checkStmt = $connection->prepare($checkSql);
    $checkStmt->execute([':lumber_app_id' => $lumber_app_id]);
    $docExists = $checkStmt->fetchColumn();

    // If the document does NOT exist, insert it and update the application status.
    if ($docExists == 0) {
        $doc_type_name = 'Endorsement for PENRO';
        $date_applied = date('m/d/y');
        $number_of_doc = '10';
        $doc_status = 'For Review (FG)';
        
        $insertSql = "INSERT INTO lumber_app_doc_erow (lumber_app_id, doc_type_name, date_applied, Number_of_doc, doc_status) 
                      VALUES (:lumber_app_id, :doc_type_name, :date_applied, :Number_of_doc, :doc_status)";
        
        $insertStmt = $connection->prepare($insertSql);
        $insertStmt->execute([
            ':lumber_app_id'   => $lumber_app_id,
            ':doc_type_name'   => $doc_type_name,
            ':date_applied'    => $date_applied,
            ':Number_of_doc'   => $number_of_doc,
            ':doc_status'      => $doc_status
        ]);
        
        // Now, update the main application status
        $new_status = 'For Initial Chief RPS';
        $new_flow_stat = '7';
        
        $updateSql = "UPDATE lumber_application SET Status = :Status, Flow_stat = :Flow_stat WHERE lumber_app_id = :lumber_app_id";
        $updateStmt = $connection->prepare($updateSql);
        $updateStmt->execute([
            ':Status'       => $new_status,
            ':Flow_stat'    => $new_flow_stat,
            ':lumber_app_id'=> $lumber_app_id
        ]);
    }
    
    // --- 3.4: LOGGING THE ACTION ---
    // This history log seems to be added every time the script runs.
    $log_title   = 'On Chief RPS';
    $log_details = 'Documents reviewed by Chief RPS and recommend the approval of Certification to DMO IV.';
    $log_date    = date('m/d/y');
    $log_time    = date("h:i:sa");
    
    $logSql = "INSERT INTO client_client_document_history (lumber_app_id, Date, Title, Details, Time)
               VALUES (:lumber_app_id, :Date, :Title, :Details, :Time)";
    
    $logStmt = $connection->prepare($logSql);
    $logStmt->execute([
        ':lumber_app_id' => $lumber_app_id,
        ':Date'          => $log_date,
        ':Title'         => $log_title,
        ':Details'       => $log_details,
        ':Time'          => $log_time
    ]);

} catch (PDOException $e) {
    // If any database error occurs, stop the script and show the error.
    die("Database error: " . $e->getMessage());
}


// =============================================================================
// SECTION 4: PDF GENERATION
// =============================================================================

// Start output buffering to capture the HTML from the template file
ob_start();

// The included file will have access to all the variables defined above ($office_address, $totalsupply, etc.)
include "template_short_PENRO_ENDRM.php";

// Get the HTML content from the buffer and clean it
$html = ob_get_clean();

// Configure Dompdf options
$options = new Options;
$options->setChroot(__DIR__); // Set base path for local files (images, css)
$options->setIsRemoteEnabled(true); // Allow loading remote images/css

// Instantiate Dompdf
$dompdf = new Dompdf($options);

// Set paper size and orientation
$dompdf->setPaper("LEGAL", "portrait");

// Load the final HTML content
$dompdf->loadHtml($html);

// Render the HTML as PDF
$dompdf->render();

// Add metadata to the PDF
$dompdf->addInfo("Title", "Application Endorsement");

// Stream the generated PDF to the browser.
// "Attachment" => 0 means it will display in the browser instead of forcing a download.
$dompdf->stream("endorsement.pdf", ["Attachment" => 0]);

?>