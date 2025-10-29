<?php

// Require the Composer autoloader and your database configuration.
require __DIR__ . "/vendor/autoload.php";
require_once "../../processphp/config.php";

// Use the Dompdf classes.
use Dompdf\Dompdf;
use Dompdf\Options;

// --- ERROR HANDLING FOR DATABASE CONNECTION ---
// Check if the connection to the database was successful.
if ($con->connect_error) {
    // If the connection failed, stop the script and display the error.
    die("Connection failed: " . $con->connect_error);
}

// Start with an empty array to hold the data for the document.
$documentData = [];

// Get the lumber application ID from the URL and sanitize it.
// This is important for security.
$lumber_app_id = isset($_GET["lumber_app_id"]) ? intval($_GET["lumber_app_id"]) : 0;

// The function to add a suffix to the day.
function addDaySuffix($day) {
    if ($day >= 11 && $day <= 13) {
        return $day . 'th';
    } else {
        switch ($day % 10) {
            case 1:
                return $day . 'st';
            case 2:
                return $day . 'nd';
            case 3:
                return $day . 'rd';
            default:
                return $day . 'th';
        }
    }
}

// 1. Check if the document already exists in `cf_documents`.
// Use a prepared statement to prevent SQL injection.
$stmt = $con->prepare("SELECT * FROM cf_documents WHERE lumber_app_id = ?");

// --- ERROR HANDLING FOR PREPARED STATEMENT ---
if ($stmt === false) {
    die("MySQL prepare error: " . $con->error);
}

$stmt->bind_param("i", $lumber_app_id);
$stmt->execute();
$result = $stmt->get_result();
$documentData = $result->fetch_assoc();
$stmt->close();

// 2. If the document exists, use its data.
if ($documentData) {
    $date = $documentData['date'];
    $day = date('j', strtotime($date));
    $dayWithSuffix = addDaySuffix($day) . " Day of " . date('F, Y', strtotime($date));
} else {
    // 3. If the document doesn't exist, get the data from `lumber_application` and related tables.
    $stmt = $con->prepare("SELECT * FROM lumber_application WHERE lumber_app_id = ?");

    // --- ERROR HANDLING ---
    if ($stmt === false) {
        die("MySQL prepare error: " . $con->error);
    }

    $stmt->bind_param("i", $lumber_app_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $lumber_ap_row = $result->fetch_assoc();
    $stmt->close();

    // --- ERROR HANDLING FOR NO DATA ---
    if (!$lumber_ap_row) {
        // If no data is found, we should stop here.
        exit("No data found for the given application ID.");
    }
    
    // Continue with the rest of the queries, adding checks for each.
    $office_dir = $lumber_ap_row['Office'];
    $province_suffix = $lumber_ap_row['Suffix'];
    $bussiness_name = $lumber_ap_row['bussiness_name'];

    // Get office address.
    $stmt = $con->prepare("SELECT office_under FROM Office WHERE station = ?");
    if ($stmt === false) { die("MySQL prepare error: " . $con->error); }
    $stmt->bind_param("s", $office_dir);
    $stmt->execute();
    $result = $stmt->get_result();
    $lumber_ap_row1 = $result->fetch_assoc();
    $stmt->close();
    
    $office_penroaddress_station = $lumber_ap_row1['office_under'];

    $stmt = $con->prepare("SELECT office_address FROM Office WHERE station = ?");
    if ($stmt === false) { die("MySQL prepare error: " . $con->error); }
    $stmt->bind_param("s", $office_penroaddress_station);
    $stmt->execute();
    $result = $stmt->get_result();
    $lumber_ap_row2 = $result->fetch_assoc();
    $stmt->close();

    $office_address = $lumber_ap_row2['office_address'];
    
    // Get province name.
    $stmt = $con->prepare("SELECT prov_name FROM province WHERE Suffix = ?");
    if ($stmt === false) { die("MySQL prepare error: " . $con->error); }
    $stmt->bind_param("s", $province_suffix);
    $stmt->execute();
    $result = $stmt->get_result();
    $lumber_ap_row3 = $result->fetch_assoc();
    $stmt->close();

    $prov_name = $lumber_ap_row3['prov_name'];

    // Assign all data to the documentData array.
    $documentData = [
        'province' => $prov_name,
        'penro_address' => $office_address,
        'office_under' => $office_dir,
        'ldname' => $bussiness_name,
        'date' => date('Y-m-d')
    ];
    
    // Format the date for the HTML document.
    $day = date('j');
    $dayWithSuffix = addDaySuffix($day) . " Day of " . date('F, Y');

    // 4. Insert the new document data into `cf_documents`.
    $stmt = $con->prepare("INSERT INTO cf_documents (lumber_app_id, province, penro_address, office_under, ldname, date) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt === false) { die("MySQL prepare error: " . $con->error); }
    $stmt->bind_param("isssss", $lumber_app_id, $documentData['province'], $documentData['penro_address'], $documentData['office_under'], $documentData['ldname'], $documentData['date']);
    
    // --- ERROR HANDLING FOR EXECUTION ---
    if (!$stmt->execute()) {
        die("MySQL execute error: " . $stmt->error);
    }
    
    $stmt->close();
}

// Check one last time if we have data to proceed.
if (empty($documentData)) {
    exit("Failed to retrieve document data.");
}

// --- DOMPDF GENERATION ---
// This part of the code doesn't need error handling unless you want to check for file existence,
// but the current structure is solid.

// Set the Dompdf options.
$options = new Options;
$options->setChroot(__DIR__);
$options->setIsRemoteEnabled(true);

$dompdf = new Dompdf($options);

// Set the paper size and orientation.
$dompdf->setPaper("A4", "portrait");

// Load the HTML and replace placeholders with values.
// --- ERROR HANDLING FOR TEMPLATE FILE ---
if (!file_exists("template_CF.php")) {
    die("Error: template_CF.php not found.");
}
$html = file_get_contents("template_CF.php");
$html = str_replace(
    ["{{ province }}", "{{ penro_address }}", "{{ office_under }}", "{{ ldname }}", "{{ date }}"],
    [$documentData['province'], $documentData['penro_address'], $documentData['office_under'], $documentData['ldname'], $dayWithSuffix],
    $html
);

$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->addInfo("Title", "Memorandum");

// Send the PDF to the browser for download.
$dompdf->stream("copyfurnish.pdf", ["Attachment" => false]);

// Save the PDF file locally.
$output = $dompdf->output();
file_put_contents("acknowledgement.pdf", $output);

$con->close();

?>