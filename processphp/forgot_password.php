<?php
session_start();
include('config.php');

if (isset($_POST['send_code'])) {
    $email = $_POST['email'];


    // Check if the email exists in the database
    $query = $connection->prepare("SELECT email, password_unhashed FROM user_client WHERE email=:email");
    $query->bindParam("email", $email, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
        $user = $query->fetch(PDO::FETCH_ASSOC);

        // Check if the password_unhashed column has a value
        if (!empty($user['password_unhashed'])) {
            // Generate a verification code
            $verification_code = rand(100000, 999999);

            // Store the verification code in the session
            $_SESSION['verification_code'] = $verification_code;
            $_SESSION['email'] = $email;

            // Send the verification code to the user's email
            $to = $email;
            $subject = "Password Reset Verification Code";
            $message = "Your verification code is: " . $verification_code;
            $headers = "From: noreply@yourdomain.com\r\n";
            $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
            $yourname = 'O-LDPMS PASSWORD';

            // Construct the URL correctly
            $url = 'https://o-ldpms.denr.gov.ph/sendemail/send.php?send=1&email=' . urlencode($email) . '&Subject=' . urlencode($subject) . '&message=' . urlencode($message) . '&yourname=' . urlencode($yourname);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for testing
            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                echo 'cURL error: ' . curl_error($ch);
            } else {
                echo 'Response from server: ' . $response;
            }
            curl_close($ch);

            // Check the response and redirect accordingly
            if ($response == 'Message has been sent successfully') {
                header("Location: ../forgot_password.php?code=1&email=" . urlencode($email) . "&success=Verification code sent to your email.");
                exit();
            } else {
                header("Location: ../forgot_password.php?error=Failed to send verification code.");
                exit();
            }
        } else {
            header("Location: ../forgot_password.php?error=Email address not found.");
            exit();
        }
    } else {
        header("Location: ../forgot_password.php?error=Email address not found.");
        exit();
    }
}




if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['code'])) {
    if ($_SESSION['verification_code'] == $_POST['code']) {


        
        // Redirect to the forgot_password.php page with a query parameter
        header("Location: ../forgot_password.php?showModal=true&email=");
        exit();
    } else {
        header("Location: ../forgot_password.php?error=Error Verification Code.");
    }
}



?>



