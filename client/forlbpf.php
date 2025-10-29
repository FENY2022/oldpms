<?php
$selectedRowId = $_GET['lumber_app_id'];

require_once "../processphp/config.php";

$sql = "SELECT * FROM order_of_payment WHERE lumber_app_id = '$selectedRowId'"; 
$result = $con->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $Amount_Decimal = $row['Amount_Decimal'];
        $payment_transaction = $row['payment_transaction'];
        $Serial_No = $row['Serial_No'];
        $Address_Office_of_Payor = $row['Address_Office_of_Payor'];
        $Name_of_Payor = $row['Name_of_Payor'];
        $Entity_Name = $row['Entity_Name'];
        $FundCluster = $row['FundCluster'];
        $Payment_Status = $row['Payment_Status'];
    }
} else {
    echo "No results found for the selected ID";
}

$sql = "SELECT bussiness_name FROM lumber_application WHERE lumber_app_id = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 's', $selectedRowId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $bussiness_name = $row['bussiness_name'];
    $bussiness_name = str_replace('&', 'AND', $bussiness_name);
}

mysqli_stmt_close($stmt);

$query = "SELECT code, acronym, description FROM reponsibilitycenters WHERE Office = ?";
$stmt = mysqli_prepare($con, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $Entity_Name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $code = $row['code'];
            $acronym = $row['acronym'];
            $description = $row['description'];
        } else {
            echo "No records found for the specified Office.";
        }
    } else {
        echo "Error: " . mysqli_error($con);
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Error: " . mysqli_error($con);
}

$trxnamt = $Amount_Decimal;
$merchantcode = "1344";
$bankcode = "B000";

$remove_chars = array(",", "'", "&");
$trxndetails = str_replace($remove_chars, "", $payment_transaction);
$trandetail1 = str_replace($remove_chars, "", $Serial_No);
$trandetail2 = str_replace($remove_chars, "", $Name_of_Payor);
$trandetail3 = str_replace($remove_chars, "", $_GET['email']);
$trandetail4 = str_replace($remove_chars, "", $Address_Office_of_Payor);
$trandetail5 = str_replace($remove_chars, "", $bussiness_name);
$trandetail6 = str_replace($remove_chars, "", $FundCluster);
$trandetail7 = str_replace($remove_chars, "", $code);
$trandetail8 = null;
$trandetail9 = null;
$trandetail10 = null;
$trandetail11 = 0;
$trandetail12 = 0;
$trandetail13 = 0;
$trandetail14 = 0;
$trandetail15 = 0;
$trandetail16 = 0;
$trandetail17 = 0;
$trandetail18 = 0;
$trandetail19 = 0;
$trandetail20 = null;
$merchantCode = "1344";

$concatenatedParams = $trxnamt . $merchantCode . $trxndetails . $trandetail1 . $trandetail2 . $trandetail3 . $trandetail4 . $trandetail5 . $trandetail6 . $trandetail7 . $trandetail8 . $trandetail9 . $trandetail10 . $trandetail11 . $trandetail12 . $trandetail13 . $trandetail14 . $trandetail15 . $trandetail16 . $trandetail17 . $trandetail18 . $trandetail19;
$username = "username";
$password = "password";
$secretKey = "N\\HWJUKFHQX";

$concatenatedParams .= $username . $password . $secretKey;

$checksum = hash('sha256', $concatenatedParams);
$checksumHex = strtoupper($checksum);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Payment Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .payment-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h3 {
            color: #198754; /* Bootstrap's success color */
            font-weight: bold;
        }
        .summary-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
        }
        .summary-card h5 {
            font-weight: bold;
            color: #343a40;
            border-bottom: 2px solid #198754;
            padding-bottom: 10px;
        }
        .summary-card p {
            margin-bottom: 8px;
            font-size: 1.1em;
        }
        .summary-card .label {
            font-weight: 600;
            color: #6c757d;
        }
        .btn-pay {
            background-color: #198754;
            border-color: #198754;
            font-size: 1.2em;
            padding: 10px 30px;
        }
        .btn-pay:hover {
            background-color: #157347;
            border-color: #157347;
        }
        .btn-back {
            background-color: #6c757d;
            border-color: #6c757d;
            font-size: 1.2em;
            padding: 10px 30px;
        }
        .btn-back:hover {
            background-color: #5a6268;
            border-color: #5a6268;
        }
        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="payment-container">
        <div class="header">
            <h3>Payment Confirmation</h3>
            <p class="text-muted">Please confirm the details below before proceeding to payment.</p>
        </div>

        <div class="summary-card">
            <h5>Payment Summary</h5>
            <p><span class="label">Amount:</span> ₱<?php echo number_format($trxnamt, 2); ?></p>
            <p><span class="label">Transaction Type:</span> <?php echo htmlspecialchars($payment_transaction); ?></p>
            <p><span class="label">Payor Name:</span> <?php echo htmlspecialchars($Name_of_Payor); ?></p>
            <p><span class="label">Reference No.:</span> <?php echo htmlspecialchars($Serial_No); ?></p>
        </div>

        <form action="server.php" method="post" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">Email Address</label>
                <input type="email" id="email" name="trandetail3" class="form-control" value="<?php echo htmlspecialchars($trandetail3); ?>" required readonly>
            </div>

            <input type="hidden" name="trxnamt" value="<?php echo htmlspecialchars($trxnamt); ?>">
            <input type="hidden" name="checksum" value="<?php echo htmlspecialchars($checksumHex); ?>">
            <input type="hidden" name="merchantcode" value="<?php echo htmlspecialchars($merchantcode); ?>">
            <input type="hidden" name="bankcode" value="<?php echo htmlspecialchars($bankcode); ?>">
            <input type="hidden" name="trxndetails" value="<?php echo htmlspecialchars($trxndetails); ?>">
            <input type="hidden" name="trandetail1" value="<?php echo htmlspecialchars($trandetail1); ?>">
            <input type="hidden" name="trandetail2" value="<?php echo htmlspecialchars($trandetail2); ?>">
            <input type="hidden" name="trandetail4" value="<?php echo htmlspecialchars($trandetail4); ?>">
            <input type="hidden" name="trandetail5" value="<?php echo htmlspecialchars($trandetail5); ?>">
            <input type="hidden" name="trandetail6" value="<?php echo htmlspecialchars($trandetail6); ?>">
            <input type="hidden" name="trandetail7" value="<?php echo htmlspecialchars($trandetail7); ?>">
            <input type="hidden" name="trandetail8" value="<?php echo htmlspecialchars($trandetail8); ?>">
            <input type="hidden" name="trandetail9" value="<?php echo htmlspecialchars($trandetail9); ?>">
            <input type="hidden" name="trandetail10" value="<?php echo htmlspecialchars($trandetail10); ?>">
            <input type="hidden" name="trandetail11" value="<?php echo htmlspecialchars($trandetail11); ?>">
            <input type="hidden" name="trandetail12" value="<?php echo htmlspecialchars($trandetail12); ?>">
            <input type="hidden" name="trandetail13" value="<?php echo htmlspecialchars($trandetail13); ?>">
            <input type="hidden" name="trandetail14" value="<?php echo htmlspecialchars($trandetail14); ?>">
            <input type="hidden" name="trandetail15" value="<?php echo htmlspecialchars($trandetail15); ?>">
            <input type="hidden" name="trandetail16" value="<?php echo htmlspecialchars($trandetail16); ?>">
            <input type="hidden" name="trandetail17" value="<?php echo htmlspecialchars($trandetail17); ?>">
            <input type="hidden" name="trandetail18" value="<?php echo htmlspecialchars($trandetail18); ?>">
            <input type="hidden" name="trandetail19" value="<?php echo htmlspecialchars($trandetail19); ?>">
            <input type="hidden" name="trandetail20" value="<?php echo htmlspecialchars($trandetail20); ?>">
            <input type="hidden" name="callbackurl" value="https://o-ldpms.denr.gov.ph/client/callbackurl.php">
            <input type="hidden" name="username" value="username">
            <input type="hidden" name="password" value="password">

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-pay btn-lg">Proceed to Payment</button>
                <button type="button" class="btn btn-secondary btn-back btn-lg" onclick="window.history.back();">Go Back</button>
            </div>
        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>