<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('config.php');

if (isset($_POST['btn'])) {

    // 1. Collect Form Fields
    $firstname   = $_POST['firstname']  ?? '';
    $middlename  = $_POST['mid_name']   ?? '';
    $lastname    = $_POST['lastname']   ?? '';
    $mobilenum   = $_POST['mobilenum']  ?? '';
    $email       = $_POST['email']      ?? '';
    $Cemail      = $_POST['Cemail']     ?? '';
    $password    = $_POST['password']   ?? '';
    $CPassword   = $_POST['Cpassword']  ?? '';
    $province    = $_POST['province']   ?? '';
    $citymun     = $_POST['citymun']    ?? '';
    $brgy        = $_POST['brgy']       ?? '';
    $zips        = $_POST['zips']       ?? '';

    // 2. Get office_cover from city/municipality
    $lumber_app     = "SELECT * FROM muncity WHERE mun_code = $citymun";
    $lumber_app_qry = mysqli_query($con, $lumber_app);
    $lumber_ap_row3 = mysqli_fetch_assoc($lumber_app_qry);
    $office_cover   = $lumber_ap_row3['office_cover'] ?? '';

    // 3. Validate Required Fields
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($CPassword)) {
        $_SESSION['toast_message'] = 'Please fill out all required fields.';
        $_SESSION['toast_type'] = 'error';
        header('Location: ../register2.php'); // Redirect back to the registration page
        exit;
    }

    // 4. Validate Matching Email and Password
    if ($password !== $CPassword) {
        $_SESSION['toast_message'] = 'Passwords do not match.';
        $_SESSION['toast_type'] = 'error';
        header('Location: ../register2.php');
        exit;
    }

    if ($email !== $Cemail) {
        $_SESSION['toast_message'] = 'Email and Confirm Email do not match.';
        $_SESSION['toast_type'] = 'error';
        header('Location: ../register2.php');
        exit;
    }

    // 5. Hash the password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // 6. Check if email already exists
    $query = $connection->prepare("SELECT * FROM user_client WHERE email=:email");
    $query->bindParam("email", $email, PDO::PARAM_STR);
    $query->execute();
    if ($query->rowCount() > 0) {
        $_SESSION['toast_message'] = 'The email address is already registered!';
        $_SESSION['toast_type'] = 'error';
        header('Location: ../register2.php');
        exit;
    }

    // 7. Handle Optional File Uploads
    $comp_id_upload = ''; // Default to an empty string if no file is uploaded
    if (isset($_FILES['my_image1']) && $_FILES['my_image1']['error'] === 0) {
        $img_name  = $_FILES['my_image1']['name'];
        $img_size  = $_FILES['my_image1']['size'];
        $tmp_name  = $_FILES['my_image1']['tmp_name'];

        if ($img_size > 100000000) {
            $_SESSION['toast_message'] = 'Sorry, your ID file is too large.';
            $_SESSION['toast_type'] = 'error';
            header('Location: ../register2.php');
            exit;
        }

        $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
        $img_ex_lc = strtolower($img_ex);
        $allowed_exs = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array($img_ex_lc, $allowed_exs)) {
            $_SESSION['toast_message'] = 'Invalid file type for ID upload.';
            $_SESSION['toast_type'] = 'error';
            header('Location: ../register2.php');
            exit;
        }

        $new_img_name = uniqid("PDF-", true) . '.' . $img_ex_lc;
        $img_upload_path = 'uploads/' . $new_img_name;
        move_uploaded_file($tmp_name, $img_upload_path);

        $comp_id_upload = $new_img_name;
    }

    $auth_letter = ''; // Default to an empty string if no file is uploaded
    if (isset($_FILES['my_image3']) && $_FILES['my_image3']['error'] === 0) {
        $img_name3  = $_FILES['my_image3']['name'];
        $img_size3  = $_FILES['my_image3']['size'];
        $tmp_name3  = $_FILES['my_image3']['tmp_name'];

        if ($img_size3 > 100000000) {
            $_SESSION['toast_message'] = 'Sorry, your Auth Letter file is too large.';
            $_SESSION['toast_type'] = 'error';
            header('Location: ../register2.php');
            exit;
        }

        $img_ex3 = pathinfo($img_name3, PATHINFO_EXTENSION);
        $img_ex_lc3 = strtolower($img_ex3);
        $allowed_exs3 = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array($img_ex_lc3, $allowed_exs3)) {
            $_SESSION['toast_message'] = 'Invalid file type for Auth Letter.';
            $_SESSION['toast_type'] = 'error';
            header('Location: ../register2.php');
            exit;
        }

        $new_img_name3 = uniqid("AUTH-", true) . '.' . $img_ex_lc3;
        $img_upload_path3 = 'uploads/' . $new_img_name3;
        move_uploaded_file($tmp_name3, $img_upload_path3);

        $auth_letter = $new_img_name3;
    }

    // 8. Insert into Database
    $insert = $connection->prepare("
        INSERT INTO user_client 
            (firstname, mid_name, lastname, mobilenum, password, email, comp_id_upload, auth_letter, password_unhashed, province, citymun, brgy, zips)
        VALUES 
            (:firstname, :mid_name, :lastname, :mobilenum, :password_hash, :email, :comp_id_upload, :auth_letter, :password_unhashed, :province, :citymun, :brgy, :zips)
    ");

    $insert->bindParam(":firstname",       $firstname,      PDO::PARAM_STR);
    $insert->bindParam(":mid_name",        $middlename,     PDO::PARAM_STR);
    $insert->bindParam(":lastname",        $lastname,       PDO::PARAM_STR);
    $insert->bindParam(":mobilenum",       $mobilenum,      PDO::PARAM_STR);
    $insert->bindParam(":password_hash",   $password_hash,  PDO::PARAM_STR);
    $insert->bindParam(":email",           $email,          PDO::PARAM_STR);
    $insert->bindParam(":comp_id_upload",  $comp_id_upload, PDO::PARAM_STR);
    $insert->bindParam(":auth_letter",     $auth_letter,    PDO::PARAM_STR);
    $insert->bindParam(":password_unhashed", $password,     PDO::PARAM_STR);
    $insert->bindParam(":province",        $province,       PDO::PARAM_STR);
    $insert->bindParam(":citymun",         $citymun,        PDO::PARAM_STR);
    $insert->bindParam(":brgy",            $brgy,           PDO::PARAM_STR);
    $insert->bindParam(":zips",            $office_cover,   PDO::PARAM_STR);

    if ($insert->execute()) {
        $_SESSION['toast_message'] = 'Your registration is successful. Please wait for administrator confirmation to validate your uploaded documents.';
        $_SESSION['toast_type'] = 'success';
        header('Location: ../login.php');
    } else {
        $_SESSION['toast_message'] = 'Something went wrong with your registration.';
        $_SESSION['toast_type'] = 'error';
        header('Location: ../register2.php');
    }
} else {
    $_SESSION['toast_message'] = 'Invalid request. Form was not submitted properly.';
    $_SESSION['toast_type'] = 'error';
    header('Location: ../register2.php');
}
?>