<?php
session_start();

include('processphp/config.php');

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? null;
    $newPassword = $_POST['newPassword'] ?? null;
    $confirmPassword = $_POST['confirmPassword'] ?? null;

    if ($email === null || $newPassword === null || $confirmPassword === null) {
        error_log("Form data missing.");
        header("Location: forgot_password.php?error=Form data missing.");
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        header("Location: forgot_password.php?error=Passwords do not match.");
        exit();
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    if ($hashedPassword === false) {
        error_log("Password hashing failed.");
        header("Location: forgot_password.php?error=Password hashing failed.");
        exit();
    }

    // Check if the email exists in the database
    $checkEmailStmt = $con->prepare("SELECT email FROM user_client WHERE email = ?");
    if (!$checkEmailStmt) {
        error_log("Error preparing statement: " . $con->error);
        header("Location: forgot_password.php?error=Error preparing statement.");
        exit();
    }

    $checkEmailStmt->bind_param("s", $email);
    $checkEmailStmt->execute();
    $checkEmailStmt->store_result();

    if ($checkEmailStmt->num_rows === 0) {
        $checkEmailStmt->close();
        header("Location: forgot_password.php?error=Email does not exist.");
        exit();
    }

    $checkEmailStmt->close();

    $password_unhashed = $confirmPassword; // Store plain text (not recommended)

    $stmt = $con->prepare("UPDATE user_client SET password = ?, password_unhashed = ? WHERE email = ?");
    if (!$stmt) {
        error_log("Error preparing statement: " . $con->error);
        header("Location: forgot_password.php?error=Error preparing statement.");
        exit();
    }

    $stmt->bind_param("sss", $hashedPassword, $password_unhashed, $email);

    if ($stmt->execute()) {
        header("Location: login.php?success=Password reset successfully.");
        exit();
    } else {
        error_log("Error updating password: " . $stmt->error);
        header("Location: forgot_password.php?error=Error updating password.");
        exit();
    }

    $stmt->close();
}

$con->close();
?>