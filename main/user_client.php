<?php
// Start session and check login status (based on sidebar.php logic)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure the database configuration file is available
if (!file_exists('../processphp/config.php')) {
    die('Error: Database configuration file not found. Please check the path.');
}
require_once('../processphp/config.php');

// Block if no log in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../admin/login.php");
    exit;
}

// --- PHP Functions for Data Retrieval ---

/**
 * Fetches client data from the database based on search criteria.
 * @param mysqli $con Database connection object.
 * @param string $search Search term (name or address part).
 * @return array Array of client records.
 */
function fetchClients($con, $search = '') {
    $clients = [];
    $search = "%" . trim($search) . "%"; // Prepare search term for LIKE query

    // Base query selects all necessary client fields for the table
    $query = "SELECT client_id, firstname, mid_name, lastname, email, mobilenum, province, citymun, brgy, Status 
              FROM user_client 
              WHERE 1=1";
    
    // Add search conditions for Name, Email, Province, City/Municipality, or Barangay
    if (!empty(trim($search, '%'))) {
        $query .= " AND (
            CONCAT(firstname, ' ', mid_name, ' ', lastname) LIKE ? OR 
            email LIKE ? OR 
            province LIKE ? OR 
            citymun LIKE ? OR 
            brgy LIKE ?
        )";
        $stmt = $con->prepare($query);
        // Bind the search parameter five times for each LIKE comparison
        $stmt->bind_param("sssss", $search, $search, $search, $search, $search);
    } else {
        $stmt = $con->prepare($query);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $clients[] = $row;
        }
    }
    $stmt->close();
    return $clients;
}

// Get search query from URL parameter
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$client_data = fetchClients($con, $search_query);

// --- Simple PDF Generation Logic (Placeholder) ---
// In a real application, you would use a library like FPDF or TCPDF.
if (isset($_GET['action']) && $_GET['action'] == 'pdf') {
    // This is a minimal header-only response to trigger a download.
    // The client-side JS will handle the actual data/format.
    // We stop execution here to prevent HTML output.
    // header('Content-Type: application/pdf');
    // header('Content-Disposition: attachment; filename="client_report.pdf"');
    // exit; // Real PDF generation logic goes here
}

// Include your sidebar and header here (assuming this structure)
// require_once('sidebar.php'); 
// require_once('header.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Client Management | DENR App</title>

    <!-- Bootstrap core CSS (Assuming Bootstrap is used based on your sidebar.php) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- DataTables CSS for advanced table features -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">

    <style>
        :root {
            --denr-green: #22782c;
            --denr-light-green: #4CAF50;
            --denr-dark-green: #1a5c22;
        }
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .table-header-bg {
            background-color: var(--denr-green);
            color: white;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            padding: 20px;
        }
        .btn-action {
            padding: 5px 10px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .btn-action:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }
        .btn-edit { background-color: #ffc107; border-color: #ffc107; color: #333; }
        .btn-delete { background-color: #dc3545; border-color: #dc3545; color: white; }
        .btn-pdf { background-color: #0d6efd; border-color: #0d6efd; color: white; }
        .status-badge-active { background-color: var(--denr-light-green); color: white; padding: 5px 10px; border-radius: 50rem; font-weight: 600; }
        .status-badge-inactive { background-color: #6c757d; color: white; padding: 5px 10px; border-radius: 50rem; font-weight: 600; }
        
        /* Custom Search/Action Bar Styling */
        .search-action-bar {
            background-color: #ffffff;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
    </style>
</head>

<body>
    <!-- Assuming your main content area structure starts here -->
    <div class="container-fluid" style="padding-top: 20px;">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-header-bg d-flex justify-content-between align-items-center">
                        <h2 class="text-white mb-0"><i class="fas fa-users-cog me-2"></i> Client User Management</h2>
                        <button class="btn btn-outline-light rounded-pill" onclick="triggerPDFExport()">
                            <i class="fas fa-file-pdf me-2"></i> Export All to PDF
                        </button>
                    </div>

                    <div class="card-body">
                        
                        <!-- Search and Action Bar (Using the standard DataTables search is better) -->
                        <div class="search-action-bar d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0 text-secondary">Registered Clients (<?= count($client_data) ?>)</h5>
                            <!-- DataTables handles search, so we just provide a placeholder for manual search if needed -->
                            <!-- <form method="GET" class="d-flex">
                                <input type="text" name="search" class="form-control me-2 rounded-pill" placeholder="Search by Name or Address..." value="<?= htmlspecialchars($search_query) ?>">
                                <button type="submit" class="btn btn-outline-primary rounded-pill"><i class="fas fa-search"></i></button>
                            </form> -->
                        </div>

                        <!-- Data Table -->
                        <div class="table-responsive">
                            <table id="clientTable" class="table table-hover table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Client Name</th>
                                        <th>Email</th>
                                        <th>Contact No.</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($client_data as $client): 
                                        $fullName = htmlspecialchars($client['firstname'] . ' ' . $client['mid_name'] . ' ' . $client['lastname']);
                                        $address = trim(htmlspecialchars($client['brgy'] . ', ' . $client['citymun'] . ', ' . $client['province']), ', ');
                                        $statusText = $client['Status'] == 1 ? 'Active' : 'Pending';
                                        $statusClass = $client['Status'] == 1 ? 'status-badge-active' : 'status-badge-inactive';
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($client['client_id']) ?></td>
                                        <td><?= $fullName ?></td>
                                        <td><?= htmlspecialchars($client['email']) ?></td>
                                        <td><?= htmlspecialchars($client['mobilenum']) ?></td>
                                        <td><?= $address ?></td>
                                        <td><span class="<?= $statusClass ?>"><?= $statusText ?></span></td>
                                        <td>
                                            <!-- Edit Button -->
                                            <button class="btn btn-sm btn-edit btn-action" title="Edit Client" 
                                                onclick="editClient(<?= $client['client_id'] ?>)">
                                                <i class="fas fa-pencil-alt"></i> Edit
                                            </button>
                                            
                                            <!-- Delete Button -->
                                            <button class="btn btn-sm btn-delete btn-action" title="Delete Client" 
                                                onclick="deleteClient(<?= $client['client_id'] ?>, '<?= $fullName ?>')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                            
                                            <!-- Print Single PDF Button -->
                                            <button class="btn btn-sm btn-pdf btn-action" title="Print Client Details to PDF" 
                                                onclick="printSinglePDF(<?= $client['client_id'] ?>, '<?= $fullName ?>')">
                                                <i class="fas fa-print"></i> PDF
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /container-fluid -->

    <!-- Bootstrap and jQuery (DataTables dependency) JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <!-- JsPDF for client-side PDF generation (used for quick demos) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
    
    <script>
        // Initialize DataTables
        $(document).ready(function() {
            // DataTables handles searching, sorting, and pagination locally (client-side)
            $('#clientTable').DataTable({
                "columnDefs": [
                    // Disable sorting for the 'Actions' column
                    { "orderable": false, "targets": 6 } 
                ],
                "language": {
                    "search": "Quick Filter (Name, Email, Address):"
                },
                "dom": 
                    // Add the 'P' for search input on top
                    "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
            });
        });

        // --- Action Functions (Placeholders) ---

        function editClient(clientId) {
            // In a real application, this would redirect or open a modal for editing
            alert(`Redirecting to edit page for Client ID: ${clientId}`);
            // window.location.href = `edit_client.php?id=${clientId}`; 
        }

        function deleteClient(clientId, clientName) {
            // Use a custom modal in a full app, but alert is used here as a placeholder
            if (confirm(`Are you sure you want to delete client: ${clientName} (ID: ${clientId})?`)) {
                alert(`Client ID: ${clientId} (${clientName}) deleted successfully (simulated).`);
                // In a real application, you would make an AJAX call to delete_client.php
                // e.g., fetch('delete_client.php?id=' + clientId).then(response => { /* reload or remove row */ });
            }
        }
        
        // --- PDF Generation Functions ---
        
        // 1. Print ALL data to PDF (using jsPDF for client-side)
        function triggerPDFExport() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            doc.text("Client Management Report", 14, 20);
            doc.setFontSize(10);
            doc.text(`Generated on: ${new Date().toLocaleDateString()}`, 14, 26);
            
            // Collect visible table data (DataTables can filter this if needed, but we use the current DOM table)
            const table = document.getElementById('clientTable');
            const headerRows = table.querySelectorAll('thead tr th');
            const headers = Array.from(headerRows).map(th => th.textContent);
            
            // Only include the columns we want in the PDF (excluding Actions)
            const pdfHeaders = headers.slice(0, 6); 

            // Get data from visible rows
            const body = [];
            $('#clientTable tbody tr').each(function() {
                const rowData = [];
                // Loop through columns 0 to 5 (excluding Actions column index 6)
                $(this).find('td:lt(6)').each(function() {
                    let cellText = $(this).text().trim();
                    // Handle Status badge text
                    if ($(this).find('.status-badge-active, .status-badge-inactive').length) {
                        cellText = $(this).find('span').text().trim();
                    }
                    rowData.push(cellText);
                });
                body.push(rowData);
            });

            doc.autoTable({
                startY: 35,
                head: [pdfHeaders],
                body: body,
                theme: 'striped',
                headStyles: { fillColor: [34, 120, 44] } // DENR Green
            });

            doc.save('All_Clients_Report.pdf');
        }

        // 2. Print SINGLE client data to PDF
        function printSinglePDF(clientId, clientName) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            // Find the row data for the specific client
            const row = $(`#clientTable tbody tr`).filter(function() {
                return $(this).find('td:first').text().trim() == clientId;
            });

            if (row.length === 0) {
                alert("Client data not found for PDF generation.");
                return;
            }

            const data = {
                'Client ID': row.find('td:eq(0)').text().trim(),
                'Full Name': row.find('td:eq(1)').text().trim(),
                'Email': row.find('td:eq(2)').text().trim(),
                'Contact Number': row.find('td:eq(3)').text().trim(),
                'Address': row.find('td:eq(4)').text().trim(),
                'Status': row.find('td:eq(5) span').text().trim() // Get text from the badge
            };
            
            // Title and Header
            doc.setFontSize(16);
            doc.text(`Client Detail: ${clientName}`, 14, 20);
            doc.setFontSize(11);
            doc.text(`Client ID: ${data['Client ID']}`, 14, 28);
            
            // Prepare data for table
            const finalData = Object.entries(data).map(([key, value]) => [key, value]);

            doc.autoTable({
                startY: 35,
                body: finalData,
                theme: 'grid',
                columnStyles: {
                    0: { fontStyle: 'bold', fillColor: [240, 240, 240] },
                    1: { cellWidth: 'auto' }
                },
                styles: {
                    cellPadding: 3,
                    fontSize: 10
                }
            });

            doc.save(`${clientName.replace(/ /g, '_')}_Details.pdf`);
        }

    </script>
</body>
</html>

<!-- Note on Database Connection:
    This file assumes the '../processphp/config.php' file connects to the database 
    and exposes a variable named '$con' (the mysqli connection object), consistent 
    with standard PHP practices and the structure implied by the uploaded 'sidebar.php'.
-->