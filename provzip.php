<?php
include 'processphp/config.php';

$zip_id = $_POST["zip_data"];
// echo $province_id ;

$zip = "SELECT * FROM muncity where mun_code = $zip_id";

$zip_qry = mysqli_query($con, $zip);

$zip_row = mysqli_fetch_assoc($zip_qry);

// $output = $zip_row['zip_code'];


// $output = "<value="'.$zip_row['zip_code'].'">" ;



// $zipname = $zip_row['zip_code'];
// $output = '<option value=""> Select Barangay </option>';



// while($citymun_row = mysqli_fetch_assoc($zip_qry)){

    // $output .= '<option value="'.$citymun_row['brgy_code'].'">'.$citymun_row['brgy_name']. '</option>';

    $output = $zip_row['zip_code'];
// }

echo $output;

?>