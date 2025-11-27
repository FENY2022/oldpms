<?php
/**
 * SECTION 1: INITIALIZATION, SESSION MANAGEMENT, AND CONFIGURATION
 * This block ensures the user is logged in and sets up the necessary environment.
 */
session_start();
// Assuming config.php provides both $connection (PDO) and $con (mysqli)
require_once "../processphp/config.php";

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// Generate a unique ID for the application session
$_SESSION["uniquid_lap"] = uniqid();

// Temporarily disable error reporting for cleaner output, though generally handled by display_errors setting
error_reporting(0);

// --- SECTION 2: CLIENT DATA FETCH (PDO) ---
// Fetch the logged-in client's personal details using PDO prepared statements.

$clientname = '';
$lastname = '';
$email = '';
$mobileno = '';

if (isset($_SESSION["client_id"])) {
    $id = $_SESSION["client_id"];

    try {
        $query = $connection->prepare("SELECT firstname, lastname, email, mobilenum FROM user_client WHERE client_id=:client_id");
        $query->bindParam("client_id", $id, PDO::PARAM_INT); // Assuming client_id is an integer or string
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $clientname = $result['firstname'];
            $lastname = $result['lastname'];
            $email = $result['email'];
            $mobileno = $result['mobilenum'];
        } else {
            // Handle case where client_id is in session but no user is found
            $em = "Client account information not found.";
            header("Location: univmodal.php?error=" . urlencode($em));
            exit;
        }
    } catch (PDOException $e) {
        // Log error and redirect
        error_log("Database error fetching client info: " . $e->getMessage());
        $em = "Database access error.";
        header("Location: univmodal.php?error=" . urlencode($em));
        exit;
    }
} else {
    // Should be caught by the initial session check, but added for safety
    header("location: ../login.php");
    exit;
}

// --- SECTION 3: LUMBER APPLICATION DATA FETCH (mysqli) ---
// This block runs when the user submits the registration number via the modal.
// Note: It uses the legacy mysqli connection ($con) as per the original code.

$reg = isset($_POST['reg']) ? $_POST['reg'] : '';

// Initialize variables for the form fields
$name = '';
$lname = '';
$Permit_Type = '';
$bussiness_name = '';
$prov_name = '';
$muncity_name = '';
$brgy_name = '';
$zip_code = '';
$purok = '';
$perm_email = '';
$perm_contact = '';
$prov_code_spc = '';
$muncity_code_spc = '';
$brgy_code_spc = '';

if (isset($_POST['num']) && !empty($reg)) {

    // 1. Fetch lumber application details
    $lumber_app = "SELECT * FROM lumber_application WHERE Registration_Number = '$reg'";
    $lumber_app_qry = mysqli_query($con, $lumber_app);
    $lumber_ap_row = mysqli_fetch_assoc($lumber_app_qry);

    if ($lumber_ap_row) {
        // Populate application variables
        $name = $lumber_ap_row['perm_fname'];
        $lname = $lumber_ap_row['perm_lname'];
        $Permit_Type = $lumber_ap_row['Permit_Type'];
        $bussiness_name = $lumber_ap_row['bussiness_name'];
        $prov_code_spc = $lumber_ap_row['prov_code'];
        $muncity_code_spc = $lumber_ap_row['muncity_code'];
        $brgy_code_spc = $lumber_ap_row['brgy_code'];
        $purok = $lumber_ap_row['purok'];
        $perm_email = $lumber_ap_row['perm_email'];
        $perm_contact = $lumber_ap_row['perm_contact'];

        // 2. Fetch Province name
        $prov_code_q = "SELECT prov_name FROM province WHERE prov_code = '{$prov_code_spc}'";
        $prov_code_qry = mysqli_query($con, $prov_code_q);
        if ($prov_code_row = mysqli_fetch_assoc($prov_code_qry)) {
            $prov_name = $prov_code_row['prov_name'];
        }

        // 3. Fetch Municipality name and zip code
        $muncity_name_q = "SELECT muncity_name, zip_code FROM muncity WHERE mun_code = '{$muncity_code_spc}'";
        $muncity_name_qry = mysqli_query($con, $muncity_name_q);
        if ($muncity_name_row = mysqli_fetch_assoc($muncity_name_qry)) {
            $muncity_name = $muncity_name_row['muncity_name'];
            $zip_code = $muncity_name_row['zip_code'];
        }

        // 4. Fetch Barangay name
        $brgy_code_q = "SELECT brgy_name FROM brgy WHERE brgy_code = '{$brgy_code_spc}'";
        $brgy_code_qry = mysqli_query($con, $brgy_code_q);
        if ($brgy_code_row = mysqli_fetch_assoc($brgy_code_qry)) {
            $brgy_name = $brgy_code_row['brgy_name'];
        }
    } else {
        // No record found
        echo "<script type='text/javascript'>alert('Sorry no record found');</script>";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OLDPMS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="../fonts/css/all.css">
    <link href="css/custom_styles.css" rel="stylesheet">

    <style>
        /* Custom Styles for improved UI */
        .progress-step {
            padding: 5px 15px !important; /* Adjusted for better look */
        }
        .form-step {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
        }
        .form-step h3 {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .form-floating input:read-only {
            background-color: #f8f9fa !important;
        }
        .custom_upload_item {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            margin-bottom: 10px;
            padding: 10px;
        }
        .custom_upload_item .btn {
            min-width: 120px;
        }
        .custom_upload_item span {
            display: block;
        }
        .custom_upload_item i {
            margin-right: 5px;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="js/script.js" defer></script>
</head>

<body style="background: #ecedf0;">
    <div class="spinner-wrapper">
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>
    
    <form action="../processphp/prc_logout.php" method="post" role="form" id="logoutForm"></form>

    <div id="wrapper">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="padding: 5px;">
            <div class="container-fluid">
                <a href="index.php"><img src="../images/oldpmslogo.png" alt="oldpms" height="40"></a>
                <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
                    <a class="navbar-brand" href="#"><strong>ONLINE LUMBER DEALER PERMITTING & MONITORING SYSTEM</strong></a>
                </div>
            </div>
        </nav>

        <nav class="navbar navbar-inverse fixed-top" id="sidebar-wrapper" role="navigation">
            <ul class="nav sidebar-nav">
                <div class="sidebar-header">
                    <div class="sidebar-brand">
                        <a class="navbar-brand" style="font-family:Trebuchet MS; color:#312f31; font-size:20px; color: #fff; font-weight: bold;">
                            <i class="fa-solid fa-circle-user"></i> <?php echo "<b>{$clientname}</b>"; ?>
                        </a>
                    </div>
                </div>
                <li><a href="dashboard_requirement.php">Requirements</a></li>
                <li><a href="dashboard_doclist.php">Document Status</a></li>
                <li style="padding-left: 30px;">
                    <i style="color: white;" class="fa-solid fa-right-from-bracket"></i>
                    <button style="color: white;" class="btn" form="logoutForm" name="btn" data-target="#logoutModal" data-toggle="modal">Logout</button>
                </li><br><br>

                <div id='bodybox'>
                    <h5 style="color: white; font-weight: 600; font-size: 15px; padding: 5px; text-align: center;"> OLDPMS Support</h5>
                    <div id='chatborder'>
                        <p id="chatlog7" class="chatlog">&nbsp;</p>
                        <p id="chatlog1" class="chatlog">&nbsp;</p>

                        <div class="scrollmenu" style="overflow: auto; white-space: nowrap; background: #ecedf0; padding: 5px;">
                            <a type="button" onclick="myFunction()" id="suggest1" style="display: inline-block; text-decoration: none; color: #0078d4; background: #fff; padding: 5px; border-radius: 15px; font-weight: 600; font-size: 12px;">What is your name?</a>
                            <a type="button" onclick="myFunction2()" id="suggest2" style="display: inline-block; text-decoration: none; color: #0078d4; background: #fff; padding: 5px; border-radius: 15px; font-weight: 600; font-size: 12px;">Can you help me?</a>
                            <a type="button" onclick="myFunction3()" id="suggest3" style="display: inline-block; text-decoration: none; color: #0078d4; background: #fff; padding: 5px; border-radius: 15px; font-weight: 600; font-size: 12px;">How to file application?</a>
                        </div>
                        <input type="text" name="chat" id="chatbox" placeholder="Hi there! Type here to talk to me." onfocus="placeHolder()">
                    </div>
                </div>
            </ul>
        </nav>

        <div id="page-content-wrapper">
            <button type="button" class="hamburger animated fadeInLeft is-closed" data-toggle="offcanvas">
                <span class="hamb-top"></span>
                <span class="hamb-middle"></span>
                <span class="hamb-bottom"></span>
            </button>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <form action="../processphp/clientupload/renewal/prc_clientdashboard.php" class="form" method="post" role="form" enctype="multipart/form-data">

                            <input class="form-control" type="file" id="realfile" name="my_image" hidden="hidden" accept="Application/pdf" value="" />
                            <input type="file" id="realfile2" hidden="hidden" accept="Application/pdf" name="my_image2" value="upload" />
                            <input type="file" id="realfile3" hidden="hidden" name="my_image3" accept="Application/pdf" value="" />
                            <input type="file" id="realfile4" hidden="hidden" name="my_image4" accept="Application/pdf" value="" />
                            <input type="file" id="realfile5" hidden="hidden" name="my_image5" accept="Application/pdf" value="" />
                            <input type="file" id="realfile6" hidden="hidden" name="my_image6" accept="Application/pdf" value="" />
                            <input type="file" id="realfile7" hidden="hidden" name="my_image7" accept="Application/pdf" value="" />

                            <div class="progressbar">
                                <div class="progress" id="progress"></div>
                                <div class="progress-step progress-step-active" data-title="Basic Information"></div>
                                <div class="progress-step" data-title="Attachment"></div>
                            </div>

                            <div class="form-step form-step-active">
                                <div class="container mt-3 p-0">
                                    <h3 class="text-center" style="font-family: system-ui; font-weight: 600;"><i class="fa-regular fa-user" style="margin-right: 15px;"></i>Applicant's Basic Information</h3>
                                    <label class="d-block text-center mb-4" style="font-family: system-ui; color: #ff0000; font-weight: 600; font-size: 20px;"><i>(For Renewal)</i></label>
                                    <p id="refno" style="font-size: 14px; color: red;"><i>Please enter the reference no.</i></p>

                                    <div class="mb-3">
                                        <div class="form-floating">
                                            <input autofocus type="text" id="lumberd" class="form-control" placeholder="Lumber Dealer Registration No." aria-label="Enter Lumber Dealer No." name="num" data-toggle="modal" data-target="#exampleModal" value="<?php echo htmlspecialchars($reg); ?>" readonly>
                                            <label for="lumberd">Lumber Dealer Registration No. *</label>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <div class="form-floating">
                                                <input readonly type="text" class="form-control" id="perm_fname" placeholder="First Name*" aria-label="First name" name="perm_fname" value="<?php echo htmlspecialchars($name); ?>">
                                                <label for="perm_fname">First Name *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input readonly type="text" class="form-control" id="perm_lname" placeholder="Last Name*" aria-label="Last name" name="perm_lname" value="<?php echo htmlspecialchars($lname); ?>">
                                                <label for="perm_lname">Last Name *</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <div class="form-floating">
                                                <input readonly type="text" class="form-control" id="permit_type" placeholder="Permit Type*" aria-label="Application Type" name="permit_type" value="<?php echo htmlspecialchars($Permit_Type); ?>" required>
                                                <label for="permit_type">Permit Type *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input readonly type="text" class="form-control" id="bussiness_name" placeholder="Business Name*" aria-label="Business name" name="bussiness_name" value="<?php echo htmlspecialchars($bussiness_name); ?>">
                                                <label for="bussiness_name">Business Name *</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <div class="form-floating">
                                                <input readonly type="text" class="form-control" id="province_str" placeholder="Province*" aria-label="Province" name="province_str" value="<?php echo htmlspecialchars($prov_name); ?>">
                                                <label for="province_str">Province *</label>
                                                <input type="hidden" name="province" value="<?php echo htmlspecialchars($prov_code_spc); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input readonly type="text" class="form-control" id="citymun_str" placeholder="City/Municipality*" aria-label="City/Municipality" name="citymun_str" value="<?php echo htmlspecialchars($muncity_name); ?>">
                                                <label for="citymun_str">City/Municipality *</label>
                                                <input type="hidden" name="citymun" value="<?php echo htmlspecialchars($muncity_code_spc); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <div class="form-floating">
                                                <input readonly type="text" class="form-control" id="brgy_str" placeholder="Barangay*" aria-label="Barangay" name="brgy_str" value="<?php echo htmlspecialchars($brgy_name); ?>">
                                                <label for="brgy_str">Barangay *</label>
                                                <input type="hidden" name="brgy" value="<?php echo htmlspecialchars($brgy_code_spc); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input readonly type="text" class="form-control" id="zip_code" placeholder="Zip Code*" aria-label="Zip code" name="Zip_code" value="<?php echo htmlspecialchars($zip_code); ?>">
                                                <label for="zip_code">Zip Code *</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-floating">
                                            <input readonly type="text" class="form-control" id="purok" placeholder="Street/Corner/Purok*" aria-label="Street/corner/purok" name="purok" value="<?php echo htmlspecialchars($purok); ?>">
                                            <label for="purok">Street/Corner/Purok *</label>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <div class="form-floating">
                                                <input type="email" class="form-control" id="perm_email" placeholder="E-Mail (Optional)" aria-label="Email" name="perm_email" value="<?php echo htmlspecialchars($perm_email); ?>">
                                                <label for="perm_email">E-Mail (Optional)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="perm_contact" placeholder="Mobile No.*" aria-label="Mobile no" name="perm_contact" value="<?php echo htmlspecialchars($perm_contact); ?>">
                                                <label for="perm_contact">Mobile No. *</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <a href="#" class="btn btn-primary new-btn width-50" id="proceeds" style="font-family: system-ui; font-weight: 500; font-size: 16px;">Next<i class="fa-solid fa-circle-arrow-right" style="margin-left: 10px;"></i></a>
                                </div>
                            </div>

                            <div class="form-step">
                                <div class="mb-4">
                                    <h3 style="font-family: system-ui; font-weight: 600"><i class="fa-regular fa-file" style="margin-right: 13px;"></i>Upload Documents (RENEW)</h3>
                                    <p class="mb-0" style="font-size: 17px;">Click "Browse" to upload document.</p>
                                    <p style="color: red; font-size: 15px;"><i>Note: Only PDF File not larger than 10 MB is allowed.</i></p>
                                </div>
                                
                                <div class="list-group">
                                    
                                    <div class="list-group-item custom_upload_item d-flex justify-content-between align-items-center">
                                        <div class="me-3">
                                            <span id="custom-text" style="font-size: 13px; color: #808080;">1. Application form duly accomplished & sworn/notarized.<span style="color: red; font-weight: 500;"><i> *Required</i></span></span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span id="mb1" class="me-3" style="color: #808080; font-size: 15px;"></span>
                                            <button type="button" id="custom-button" class="btn btn-outline-primary btn-sm">
                                                <i class="fa-solid fa-cloud-arrow-up"></i> Browse..
                                            </button>
                                        </div>
                                    </div>

                                    <div class="list-group-item custom_upload_item d-flex justify-content-between align-items-center">
                                        <div class="me-3">
                                            <span id="custom-text2" style="font-size: 13px; color: #808080;">2. Lumber Supply Contract/Agreement from legitimate suppliers/subsisting lumber dealers.<span style="font-weight: 500; color: red;"><i> *Required</i></span><span style="font-weight: 500; color: black;"> (not required if the applicant is a mini-sawmill permittee)</span></span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span id="mb2" class="me-3" style="color: #808080; font-size: 15px;"></span>
                                            <button type="button" id="custom-button2" class="btn btn-outline-primary btn-sm">
                                                <i class="fa-solid fa-cloud-arrow-up"></i> Browse..
                                            </button>
                                        </div>
                                    </div>

                                    <div class="list-group-item custom_upload_item d-flex justify-content-between align-items-center">
                                        <div class="me-3">
                                            <span id="custom-text3" style="font-size: 13px; color: #808080;">3. Mayor's Permit/Business Permit<span style="font-weight: 500; color: red;"><i> *Required</i></span></span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span id="mb3" class="me-3" style="color: #808080; font-size: 15px;"></span>
                                            <button type="button" id="custom-button3" class="btn btn-outline-primary btn-sm">
                                                <i class="fa-solid fa-cloud-arrow-up"></i> Browse..
                                            </button>
                                        </div>
                                    </div>

                                    <div class="list-group-item custom_upload_item d-flex justify-content-between align-items-center">
                                        <div class="me-3">
                                            <span id="custom-text4" style="font-size: 13px; color: #808080;">4. Annual Business Plan/Program<span style="font-weight: 500; color: red;"><i> *Required</i></span></span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span id="mb4" class="me-3" style="color: #808080; font-size: 15px;"></span>
                                            <button type="button" id="custom-button4" class="btn btn-outline-primary btn-sm">
                                                <i class="fa-solid fa-cloud-arrow-up"></i> Browse..
                                            </button>
                                        </div>
                                    </div>

                                    <div class="list-group-item custom_upload_item d-flex justify-content-between align-items-center">
                                        <div class="me-3">
                                            <span id="custom-text5" style="font-size: 13px; color: #808080;">5. Latest Income Tax Return<span style="font-weight: 500; color: red;"><i> *Required</i></span></span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span id="mb5" class="me-3" style="color: #808080; font-size: 15px;"></span>
                                            <button type="button" id="custom-button5" class="btn btn-outline-primary btn-sm">
                                                <i class="fa-solid fa-cloud-arrow-up"></i> Browse..
                                            </button>
                                        </div>
                                    </div>

                                    <div class="list-group-item custom_upload_item d-flex justify-content-between align-items-center">
                                        <div class="me-3">
                                            <span id="custom-text6" style="font-size: 13px; color: #808080;">6. Ending stocked inventory report duly subscribed/sworn<span style="font-weight: 500; color: red;"><i> *Required</i></span></span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span id="mb6" class="me-3" style="color: #808080; font-size: 15px;"></span>
                                            <button type="button" id="custom-button6" class="btn btn-outline-primary btn-sm">
                                                <i class="fa-solid fa-cloud-arrow-up"></i> Browse..
                                            </button>
                                        </div>
                                    </div>

                                    <div class="list-group-item custom_upload_item d-flex justify-content-between align-items-center">
                                        <div class="me-3">
                                            <span id="custom-text7" style="font-size: 13px; color: #808080;">7. Summary reports showing the monthly lumber purchases, production, disposition/sales ending inventory report and other relevant information within the tenure of the permit duly attested by the CENRO concerned. <span style="font-weight: 500; color: red;"><i> *Required</i></span></span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span id="mb7" class="me-3" style="color: #808080; font-size: 15px;"></span>
                                            <button type="button" id="custom-button7" class="btn btn-outline-primary btn-sm">
                                                <i class="fa-solid fa-cloud-arrow-up"></i> Browse..
                                            </button>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="btns-group d-flex justify-content-between mt-4">
                                    <a href="#" class="btn btn-secondary btn-prev">Back</a>
                                    <button type="submit" class="btn btn-success" name="btn" data-toggle="modal" disabled="true" id="acceptBtn">
                                        <i class="fa-solid fa-paper-plane"></i> Submit
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="position-fixed top-0 end-0 p-3" style="z-index: 11;">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" style=" background-color: #d1e7dd;">
            <div class="toast-header" style=" background-color: #DFF0FA; color: #5C7585">
                <strong class="me-auto"><i class="fa-solid fa-circle-check text: #5C7585;"></i> File Submitted!</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Lumber Dealer Registration Number</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" enctype="multipart/form-data">
                        <table border="0" align="center">
                            <tr>
                                <td>Enter Your Registration Number </td>
                                <td><input style="margin-bottom: 5px;" type="text" class="form-control user_firstname" placeholder="Lumber Dealer Registration Number*" aria-label="Lumber Dealer Registration Number" name="reg"></td>
                            </tr>
                        </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button class="btn btn-success" name="num"> Search </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        // Restricts input to numbers (and hyphen, charCode 45)
        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode != 45 && charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }

        // --- File Upload UI Element Mapping ---
        const realFileBtnAccept = document.getElementById("acceptBtn");
        const realFileBtn = document.getElementById("realfile");
        const realFileBtn2 = document.getElementById("realfile2");
        const realFileBtn3 = document.getElementById("realfile3");
        const realFileBtn4 = document.getElementById("realfile4");
        const realFileBtn5 = document.getElementById("realfile5");
        const realFileBtn6 = document.getElementById("realfile6");
        const realFileBtn7 = document.getElementById("realfile7");

        const customBtn = document.getElementById("custom-button");
        const customBtn2 = document.getElementById("custom-button2");
        const customBtn3 = document.getElementById("custom-button3");
        const customBtn4 = document.getElementById("custom-button4");
        const customBtn5 = document.getElementById("custom-button5");
        const customBtn6 = document.getElementById("custom-button6");
        const customBtn7 = document.getElementById("custom-button7");

        const customTxt = document.getElementById("custom-text");
        const customTxt2 = document.getElementById("custom-text2");
        const customTxt3 = document.getElementById("custom-text3");
        const customTxt4 = document.getElementById("custom-text4");
        const customTxt5 = document.getElementById("custom-text5");
        const customTxt6 = document.getElementById("custom-text6");
        const customTxt7 = document.getElementById("custom-text7");

        const customTxtMB = document.getElementById("mb1");
        const customTxtMB2 = document.getElementById("mb2");
        const customTxtMB3 = document.getElementById("mb3");
        const customTxtMB4 = document.getElementById("mb4");
        const customTxtMB5 = document.getElementById("mb5");
        const customTxtMB6 = document.getElementById("mb6");
        const customTxtMB7 = document.getElementById("mb7");

        // Helper function for file upload logic (cleaned up and combined)
        function setupFileHandler(fileInput, customButton, customTextElement, customTextMBElement, originalText, shortLabel) {
            const MAX_SIZE_BYTES = 10 * 1024 * 1024; // 10 MB
            const requiredText = customTextElement.querySelector('span:last-child') ? customTextElement.querySelector('span:last-child').outerHTML : '';
            const originalFullText = customTextElement.innerHTML;

            customButton.addEventListener("click", function() {
                fileInput.click();
            });

            fileInput.addEventListener("change", function() {
                const files = this.files;
                
                if (files.length === 0) {
                    customTextElement.innerHTML = originalFullText;
                    customTextElement.style.color = "#808080";
                    customTextMBElement.innerHTML = '';
                    customButton.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i> Browse..';
                    checkAllFilesUploaded();
                    return;
                }

                const file = files[0];
                const totalBytes = file.size;

                if (totalBytes > MAX_SIZE_BYTES) {
                    customTextMBElement.innerHTML = 'File exceed 10 MB';
                    customTextMBElement.style.color = "red";
                    customTextElement.style.color = "red";
                    customTextElement.innerHTML = `File too large: ${file.name}`;
                    customButton.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Error';
                    customButton.classList.remove('btn-outline-primary');
                    customButton.classList.add('btn-danger');
                    fileInput.value = ''; // Clear file input
                } else {
                    // Calculate size string
                    const _size = totalBytes < 1000000 ? 
                                  `${Math.floor(totalBytes / 1000)} KB` : 
                                  `${Math.floor(totalBytes / 1000000)} MB`;

                    // Update UI on success
                    customTextMBElement.innerHTML = _size;
                    customTextMBElement.style.color = "green";
                    customTextElement.style.color = "#4285F4"; // Blue color for uploaded file
                    customTextElement.innerHTML = `<span style='color:black; font-weight: 600;'>${shortLabel}</span><br>File: <i class='fa-solid fa-file-pdf'></i> ${file.name} ${requiredText}`;
                    customButton.innerHTML = "<span style='font-size: 12px;'><i class='fa-solid fa-check'></i> Uploaded</span>";
                    customButton.classList.remove('btn-outline-primary', 'btn-danger');
                    customButton.classList.add('btn-success');
                }
                
                checkAllFilesUploaded();
            });
        }

        function checkAllFilesUploaded() {
            const MAX_SIZE_BYTES = 10 * 1024 * 1024;
            const fileInputs = [realFileBtn, realFileBtn2, realFileBtn3, realFileBtn4, realFileBtn5, realFileBtn6, realFileBtn7];
            let allFilesPresentAndValid = true;

            for (const input of fileInputs) {
                if (input.files.length === 0) {
                    allFilesPresentAndValid = false;
                    break;
                }
                if (input.files[0].size > MAX_SIZE_BYTES) {
                    allFilesPresentAndValid = false;
                    break;
                }
            }
            
            realFileBtnAccept.disabled = !allFilesPresentAndValid;
        }

        // Initialize handlers on page load
        document.addEventListener('DOMContentLoaded', () => {
            // Restore original HTML content of the text spans for proper display and reference
            customTxt.innerHTML = "1. Application form duly accomplished & sworn/notarized.<span style=\"color: red; font-weight: 500;\"><i> *Required</i></span>";
            customTxt2.innerHTML = "2. Lumber Supply Contract/Agreement from legitimate suppliers/subsisting lumber dealers.<span style=\"font-weight: 500; color: red;\"><i> *Required</i></span><span style=\"font-weight: 500; color: black;\"> (not required if the applicant is a mini-sawmill permittee)</span>";
            customTxt3.innerHTML = "3. Mayor's Permit/Business Permit<span style=\"font-weight: 500; color: red;\"><i> *Required</i></span>";
            customTxt4.innerHTML = "4. Annual Business Plan/Program<span style=\"font-weight: 500; color: red;\"><i> *Required</i></span>";
            customTxt5.innerHTML = "5. Latest Income Tax Return<span style=\"font-weight: 500; color: red;\"><i> *Required</i></span>";
            customTxt6.innerHTML = "6. Ending stocked inventory report duly subscribed/sworn<span style=\"font-weight: 500; color: red;\"><i> *Required</i></span>";
            customTxt7.innerHTML = "7. Summary reports showing the monthly lumber purchases, production, disposition/sales ending inventory report and other relevant information within the tenure of the permit duly attested by the CENRO concerned. <span style=\"font-weight: 500; color: red;\"><i> *Required</i></span>";

            const fileData = [
                { input: realFileBtn, button: customBtn, text: customTxt, mb: customTxtMB, original: customTxt.innerHTML, short: "Application form duly accomplished & sworn/notarized." },
                { input: realFileBtn2, button: customBtn2, text: customTxt2, mb: customTxtMB2, original: customTxt2.innerHTML, short: "Lumber Supply Contract/Agreement from legitimate suppliers/subsisting lumber dealers." },
                { input: realFileBtn3, button: customBtn3, text: customTxt3, mb: customTxtMB3, original: customTxt3.innerHTML, short: "Mayor's Permit/Business Permit" },
                { input: realFileBtn4, button: customBtn4, text: customTxt4, mb: customTxtMB4, original: customTxt4.innerHTML, short: "Annual Business Plan/Program" },
                { input: realFileBtn5, button: customBtn5, text: customTxt5, mb: customTxtMB5, original: customTxt5.innerHTML, short: "Latest Income Tax Return" },
                { input: realFileBtn6, button: customBtn6, text: customTxt6, mb: customTxtMB6, original: customTxt6.innerHTML, short: "Ending stocked inventory report duly subscribed/sworn" },
                { input: realFileBtn7, button: customBtn7, text: customTxt7, mb: customTxtMB7, original: customTxt7.innerHTML, short: "Summary reports showing the monthly lumber purchases, production, disposition/sales ending inventory report and other relevant information within the tenure of the permit duly attested by the CENRO concerned." },
            ];

            fileData.forEach(data => {
                setupFileHandler(data.input, data.button, data.text, data.mb, data.original, data.short);
            });
            
            // Re-bind file change listener for general submit button check (keeping original logic structure)
            $('input[type="file"]').change(checkAllFilesUploaded);
            
            // Final check on load (in case of cached inputs)
            checkAllFilesUploaded();
        });

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/jquery.easing.min.js"></script>
    <script src="js/swiper.min.js"></script>
    <script src="js/jquery.magnific-popup.js"></script>
    <script src="js/validator.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>