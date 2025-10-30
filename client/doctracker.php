<?php

require_once "../processphp/config.php";
$l_id = $_GET['lumber_app_id'];



       

?>

<?php

if (isset($_GET['lumber_app_id'])) {
    $l_id = $_GET['lumber_app_id'];

    $sql = "SELECT Office, office_under FROM lumber_application WHERE lumber_app_id = :id";
    $stmt = $connection->prepare($sql);
    $stmt->execute([':id' => $l_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $CENRO_Office = $row['Office'] ;
        $PENRO_Office = $row['office_under'] ;
    } else {
        echo "No record found.";
    }
}
?>


<!doctype html>
<html lang="en">
  <head>
      <meta charset="UTF-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta http-equiv="cache-control" content="no-cache" />
      <meta http-equiv="Pragma" content="no-cache" />
      <meta http-equiv="Expires" content="-1" />
            
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

   <title>OLDPMS</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="../fonts/css/all.css">
    <script src="js/script.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
 
    <link href="css/custom_styles.css" rel="stylesheet">
    <!--<link rel="stylesheet" href="../main/css/sb-admin-2.css"> -->
  </head>
  
<body style="background: #ecedf0;">
<div class="spinner-wrapper">
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>
  <form action="../processphp/prc_logout.php"  method="post" role="form" >
  <div id="wrapper">
             <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="padding: 5px;"> 
              <div class="container-fluid">
                <a href="index.php"><img src="../images/oldpmslogo.png" alt="oldpms" height="40"></a>
                <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
                <a class="navbar-brand" href="#"><strong>ONLINE LUMBER DEALER PERMITTING & MONITORING SYSTEM</strong></a>
                </div>
              </div>
            </nav>

        <!-- Sidebar -->
    <nav class="navbar navbar-inverse fixed-top" id="sidebar-wrapper" role="navigation">
  <ul class="nav sidebar-nav">
       <div class="sidebar-header">
       <div class="sidebar-brand">
        <a class="navbar-brand" style="font-family:Trebuchet MS; color:#312f31; font-size:20px; color: #fff; font-weight: bold;" ><i class="fa-solid fa-circle-user"></i> </div></div>
        <li><a href="dashboard_requirement.php">Home</a></li>
        <li><a href="dashboard_requirement.php">Requirements</a></li>
       <li><a href="dashboard_doclist.php">Document Status</a></li>
      <!--  <li><a href="doctracker.php" style="font-size: 15px;">Track your Application</a></li> -->
       <li style="padding-left: 30px;"><i style="color: white;" class="fa-solid fa-right-from-bracket"></i><button style="color: white;" class="btn"  name="btn" data-target="#logoutModal" data-toggle="modal">Logout</button></li><br><br>
     </ul><br><br>
   </form>
<div id='bodybox'>
  <h5 style="color: white; font-weight: 600; font-size: 15px; padding: 5px; text-align: center;"> OLDPMS Support</h5>
  <div id='chatborder'>
    <p id="chatlog7" class="chatlog">&nbsp;</p>
    <p id="chatlog6" class="chatlog">&nbsp;</p>
    <p id="chatlog5" class="chatlog">&nbsp;</p>
    <p id="chatlog4" class="chatlog">&nbsp;</p>
    <p id="chatlog3" class="chatlog">&nbsp;</p>
    <p id="chatlog2" class="chatlog">&nbsp;</p>
    <p id="chatlog1" class="chatlog">&nbsp;</p>

    <div class="scrollmenu" style="overflow: auto;
  white-space: nowrap; background: #ecedf0; padding: 5px;">
  <a type="button" onclick="myFunction()" id="suggest1" style="display: inline-block; text-decoration: none; color: #0078d4; background: #fff; padding: 5px; border-radius: 15px; font-weight: 600; font-size: 12px;">What is your name?</a>
  <a type="button" onclick="myFunction2()" id="suggest2" style="display: inline-block; text-decoration: none; color: #0078d4; background: #fff; padding: 5px; border-radius: 15px; font-weight: 600; font-size: 12px;">Can you help me?</a>
  <a type="button" onclick="myFunction3()" id="suggest3" style="display: inline-block; text-decoration: none; color: #0078d4; background: #fff; padding: 5px; border-radius: 15px; font-weight: 600; font-size: 12px;">How to file application?</a>
</div>
    <input type="text" name="chat" id="chatbox" placeholder="Hi there! Type here to talk to me." onfocus="placeHolder()">
  </div>
 
</div>

</nav>
        <!-- Page Content -->
        <div id="page-content-wrapper">
            <button type="button" class="hamburger animated fadeInLeft is-closed" data-toggle="offcanvas">
                <span class="hamb-top"></span>
          <span class="hamb-middle"></span>
        <span class="hamb-bottom"></span>
            </button>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                      <!-- START -->

                      <!-- CONTENT -->
                              <!-- END -->
                    </div>
                </div>
            </div>
            
        </div>
        <!-- /#page-content-wrapper -->
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <input type="file" id="realfile"  hidden="hidden" accept="Application/pdf" value=""/>
    <input type="file" id="realfile2" hidden="hidden" accept="Application/pdf" value=""/>
    <input type="file" id="realfile3" hidden="hidden" accept="Application/pdf" value=""/>
    <input type="file" id="realfile4" hidden="hidden" accept="Application/pdf" value=""/>
    <input type="file" id="realfile5" hidden="hidden" accept="Application/pdf" value=""/>
    <input type="file" id="realfile6" hidden="hidden" accept="Application/pdf" value=""/>

    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->


<?php

if ( isset($_POST['submit1'])) {

$l_id = $_GET['lumber_app_id'];

  $stat_uss = 'For Validation Information';
  $Flow_stats = '6.1';
  
  $sql = "UPDATE lumber_application SET Status = :Status, Flow_stat = :Flow_stat
  WHERE lumber_app_id = $l_id";
  $stmt = $connection->prepare($sql);
  $stmt->execute(array(
  ':Status' => $stat_uss,
  ':Flow_stat' => $Flow_stats,));


  $sql = "UPDATE client_client_document_history SET Action_ = :Action_
  WHERE lumber_app_id = $l_id";
  $stmt = $connection->prepare($sql);
  $stmt->execute(array(
  // ':Status' => $stat_uss,
  ':Action_' => 'YES',));

// header("Location: doctracker.php");


  function function_alert($message) {
      
    // Display the alert box 
	echo "<script type='text/javascript'>alert('Your site visit schedule has been affirmed for site validation.');location='dashboard_doclist.php';</script>";
}
  
  
// Function call
function_alert("Your site visit schedule has been affirmed for site validation.");


}


?>






                              <div class="bodytime">
                                <div class="timeline">
                                   <p style="font-size: 30px; font-weight: 600; color: #222222;"><i class="fa-solid fa-file"></i> Application Status</p>
                                   <div class="scroll-bg">
                                    <div class="scroll-div"style="padding: 10px; width: 780px; height: 800px; overflow: hidden; overflow-y: scroll;">
                                    <div class="scroll-object">
                                     <ul>
<?php 


function getFullMonthNameFromDate($date){
  $monthName = date('F d, Y', strtotime($date));
  return $monthName;
       }





$stmt = $connection->query("SELECT Date, Title, Details, Time, Action_
FROM client_client_document_history 
where lumber_app_id = $l_id Order by id DESC");

echo "<script>
function checkForUpdates() {
    fetch(window.location.href)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const currentContent = document.querySelector('.scroll-object').innerHTML;
            const newContent = doc.querySelector('.scroll-object').innerHTML;
            
            if (currentContent !== newContent) {
                document.querySelector('.scroll-object').innerHTML = newContent;
            }
        });
}

setInterval(checkForUpdates, 5000); // Check every 5 seconds
</script>";
           while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
         



            $date = $row['Date'];
           getFullMonthNameFromDate($date);



         if ($row['Title'] == 'CENR OFFICER') {
              $row['Title'] = 'CENR Officer/Head DENR Satellite Office';
          }   

          if ($row['Title'] == 'CENR Officer') {
            $row['Title'] = 'CENR Officer/Head DENR Satellite Office';
        }   

          if (stripos($row['Details'], 'CENRO') !== false || stripos($row['Details'], 'Cenro') !== false) {
              $row['Details'] = str_replace(['CENRO', 'Cenro'], 'CENR Officer/Head DENR Satellite Office', $row['Details']);
          }     

          

        if (strtotime($row['Time']) !== false) {
            $row['Time'] = date('h:i A', strtotime($row['Time']));
        }     
        
        

                                    if (($row['Title']) == ('On Site Validation Schedule')){

// condition
                                      echo   '<li style="background: #fff;">' ;
                                      echo   '<span style="background: #0d6efd;" id="latestTime"><i class="fa-regular fa-calendar"></i>'.getFullMonthNameFromDate($date).'</span><br>';
                                      echo   '<div class="content">' ;
                                      echo   '<p style="font-size: 12px; margin-left: 2px; color: #888;"><i class="fa-regular fa-clock"></i>'.$row['Time'].'</p><h5 style="color: #222; font-weight: 600;">'.$row['Title'].'</h5>' ;
          
                                      echo   '<p style="color: #222">';
                                      echo   '  '.$row['Details'].'<br><br><a href="#" style="text-decoration: none; color: #0d6efd; font-weight: 600; "></a>';
                                     
                                      if (('YES') == ($row['Action_'])){
                                      echo   '<form method="POST"><button   class="btn btn-rounded btn-success disabled" name="submit1" >Yes</button> &nbsp; &nbsp;'; 
                                      echo   '<button   class="btn btn-rounded btn-warning disabled" >No</button> </form>';
                                                                        }
                                      else 
                                      {
                                        echo   '<form method="POST"><button  class="btn btn-rounded btn-success" name="submit1" >Yes</button> &nbsp; &nbsp;'; 
                                        echo   '<button  class="btn btn-rounded btn-warning" >No</button> </form>';
                                      }
                                      echo   '  </p>';
                                      echo   '</div>';
                                      echo   '</li>' ;

  } elseif (($row['Title']) == ('Credit  Officer')) {

    // condition
    echo '<li style="background: #fff;">';
    echo '<span style="background: #0d6efd;" id="latestTime"><i class="fa-regular fa-calendar"></i>' . getFullMonthNameFromDate($date) . '</span><br>';
    echo '<div class="content">';
    echo '<p style="font-size: 12px; margin-left: 2px; color: #888;"><i class="fa-regular fa-clock"></i>' . $row['Time'] . '</p><h5 style="color: #222; font-weight: 600;">' . $row['Title'] . '</h5>';

    echo '<p style="color: #222">';
    echo '  ' . $row['Details'] . '<br><br><a href="#" style="text-decoration: none; color: #0d6efd; font-weight: 600; "></a>';
    // echo '<a  class="btn btn-rounded btn-warning"     href="clientpayment.php?lumber_app_id='.$l_id.'"   target="_blank"  name="submit1" >Pay Here</a> &nbsp; &nbsp;';
    echo '<a  class="btn btn-rounded btn-warning"     href="forlbpiq.php?lumber_app_id='.$l_id.'"   target="_blank"  name="submit1" >Pay Here</a> &nbsp; &nbsp;';
    



    echo '</p>';
    echo '</div>';
    echo '</li>';


      } elseif (($row['Details']) == ('Successfully Paid')) {

    // condition
    echo '<li style="background: #fff;">';
    echo '<span style="background: #0d6efd;" id="latestTime"><i class="fa-regular fa-calendar"></i>' . getFullMonthNameFromDate($date) . '</span><br>';
    echo '<div class="content">';
    echo '<p style="font-size: 12px; margin-left: 2px; color: #888;"><i class="fa-regular fa-clock"></i>' . $row['Time'] . '</p><h5 style="color: #222; font-weight: 600;">' . 'Client Successfully Paid'. '</h5>';

    echo '<p style="color: #222">';
    echo '  ' . $row['Details'] . '<br><br><a href="#" style="text-decoration: none; color: #0d6efd; font-weight: 600; "></a>';
    echo '<a  class="btn btn-rounded btn-success"     href="clientpayment.php?lumber_app_id='.$l_id.'"    name="submit1" >View</a> &nbsp; &nbsp;';



    echo '  </p>';
    echo '</div>';
    echo '</li>';




          }elseif (($row['Title']) == ('Client')) {

              // condition
              echo '<li style="background: #fff;">';
              echo '<span style="background: #0d6efd;" id="latestTime"><i class="fa-regular fa-calendar"></i>' . getFullMonthNameFromDate($date) . '</span><br>';
              echo '<div class="content">';
              echo '<p style="font-size: 12px; margin-left: 2px; color: #888;"><i class="fa-regular fa-clock"></i>' . $row['Time'] . '</p><h5 style="color: #222; font-weight: 600;">' . $row['Title'] . '</h5>';
          
              echo '<p style="color: #222">';
              echo '  ' . $row['Details'] . '<br><br><a href="#" style="text-decoration: none; color: #0d6efd; font-weight: 600; "></a>';
              echo '<a class="btn btn-rounded btn-success" href="css_2025/css_2025.php?lumber_app_id='.$l_id.'" target="_blank" name="submit1"><i class="fa-solid fa-star"></i> Click here for CSS</a> &nbsp; &nbsp;';
              echo '  </p>';
              echo '</div>';
              echo '</li>';
          






                        }elseif (($row['Title']) == ('Client')) {

                        // condition
                        echo '<li style="background: #fff;">';
                        echo '<span style="background: #0d6efd;" id="latestTime"><i class="fa-regular fa-calendar"></i>' . getFullMonthNameFromDate($date) . '</span><br>';
                        echo '<div class="content">';
                        echo '<p style="font-size: 12px; margin-left: 2px; color: #888;"><i class="fa-regular fa-clock"></i>' . $row['Time'] . '</p><h5 style="color: #222; font-weight: 600;">' . $row['Title'] . '</h5>';
                    
                        echo '<p style="color: #222">';
                        echo '  ' . $row['Details'] . '<br><br><a href="#" style="text-decoration: none; color: #0d6efd; font-weight: 600; "></a>';
                        echo '<a  class="btn btn-rounded btn-success"  href="clientcss_s.php?lumber_app_id='.$l_id.'" target="_blank"  name="submit1" >Click here for CSS</a> &nbsp; &nbsp;';
                    
                          
                    
                        echo '  </p>';
                        echo '</div>';
                        echo '</li>';


                        
                        }elseif (strpos($row['Details'], 'Accomplished Client Satisfaction Survey (CSS)') !== false) {
                          // condition for Accomplished Client Satisfaction Survey (CSS)
                          echo '<li style="background: #fff;">';
                          echo '<span style="background: #0d6efd;" id="latestTime"><i class="fa-regular fa-calendar"></i>' . getFullMonthNameFromDate($date) . '</span><br>';
                          echo '<div class="content">';
                          echo '<p style="font-size: 12px; margin-left: 2px; color: #888;"><i class="fa-regular fa-clock"></i>' . $row['Time'] . '</p><h5 style="color: #222; font-weight: 600;">' . $row['Title'] . '</h5>';
                        
                          echo '<p style="color: #222">';
                            echo '  ' . $row['Details'] . '<br><br><a href="#" style="text-decoration: none; color: #0d6efd; font-weight: 600; "></a>';
                            echo '<a class="btn btn-rounded btn-warning" href="docstatus_released.php?lumber_app_id='.$l_id.'" target="_blank" name="submit1"><i class="fa-solid fa-file"></i> Open Document</a> &nbsp; &nbsp;';
                          echo '</p>';
                          echo '</div>';
                          echo '</li>';

  

                        
            




              }  else{
// main




// 1. Create a single timestamp from your date and time fields
$fullTimestamp = strtotime($row['Date'] . ' ' . $row['Time']);

// 2. Format the Date part (e.g., "October 29, 2025")
// This replaces the need for a custom getFullMonthNameFromDate() function
$formattedDate = date('F j, Y', $fullTimestamp);

// 3. Calculate the adjusted time as you requested
$adjustedTime = date('h:i A', strtotime($row['Time'] . ' +1 minutes'));


// 4. Create the final string to display.
// I've wrapped it in a span to make it slightly smaller and gray for readability.
$timestampDisplay = ' <span style="font-size: 12px; color: #888;">' . $formattedDate . ' at ' . $adjustedTime . '</span>';




// --- END OF ADDED LOGIC ---


if ($row['Title'] == 'FUU' && strpos($row['Details'], 'Your application has been evaluated and officially received.') !== false) {
    echo '<p style="font-size: 14px; color: #4A90E2;">';
    echo '<i class="fa-solid fa-check-circle" style="color: #4A90E2;"></i> ';
    echo '<span class="badge" style="background:#4A90E2;color:#fff;">Forwarded </span> to ' . $CENRO_Office . ' Credit Officer' . $timestampDisplay . '</p>';

} else if ($row['Title'] == 'Credit Officer' && strpos($row['Details'], 'The credit officer will check and verify the order of payment prepared by FUU and Forward to CENR Officer/Head DENR Satellite') !== false) {
    echo '<p style="font-size: 14px; color: #4A90E2;">';
    echo '<i class="fa-solid fa-check-circle" style="color: #4A90E2;"></i> ';
    echo '<span class="badge" style="background:#4A90E2;color:#fff;">Forwarded </span> to ' . $CENRO_Office . ' CENR Officer/Head DENR Satellite Office' . $timestampDisplay . '</p>';


} else if ($row['Title'] == 'CENR Officer/Head DENR Satellite Office' && strpos($row['Details'], 'The CENR Officer/Head DENR Satellite Office will review and approve the Order of Payment. Forward to the Client for Payment.') !== false) {
    echo '<p style="font-size: 14px; color: #4A90E2;">';
    echo '<i class="fa-solid fa-check-circle" style="color: #4A90E2;"></i> ';
    echo '<span class="badge" style="background:#4A90E2;color:#fff;">Forwarded </span> to ' . $CENRO_Office . ' Client for Payment' . $timestampDisplay . '</p>';

} else if ($row['Title'] == 'Credit Officer' && strpos($row['Details'], 'Payment confirmed') !== false) {
    echo '<p style="font-size: 14px; color: #4A90E2;">';
    echo '<i class="fa-solid fa-check-circle" style="color: #4A90E2;"></i> ';
    echo '<span class="badge" style="background:#4A90E2;color:#fff;">Forwarded </span> to ' . $CENRO_Office . ' FUU' . $timestampDisplay . '</p>';

} else if ($row['Title'] == 'FUU' && strpos($row['Details'], 'On-site validation was successfully conducted.') !== false) {
    echo '<p style="font-size: 14px; color: #4A90E2;">';
    echo '<i class="fa-solid fa-check-circle" style="color: #4A90E2;"></i> ';
    echo '<span class="badge" style="background:#4A90E2;color:#fff;">Forwarded </span> to ' . $CENRO_Office . ' Chief, RPS' . $timestampDisplay . '</p>';

} else if ($row['Title'] == 'Chief, RPS' && strpos($row['Details'], 'Document reviewed and application endorsed to the Deputy CENR Officer/Head DENR Satellite Office.') !== false) {
    echo '<p style="font-size: 14px; color: #4A90E2;">';
    echo '<i class="fa-solid fa-check-circle" style="color: #4A90E2;"></i> ';
    echo '<span class="badge" style="background:#4A90E2;color:#fff;">Forwarded </span> to ' . $CENRO_Office . ' Deputy CENRO ' . $timestampDisplay . '</p>';

} else if ($row['Title'] == 'Deputy CENRO' && strpos($row['Details'], 'Document reviewed and application recommended to the CENR Officer for endorsement to the concerned PENR Office.') !== false) {
    echo '<p style="font-size: 14px; color: #4A90E2;">';
    echo '<i class="fa-solid fa-check-circle" style="color: #4A90E2;"></i> ';
    echo '<span class="badge" style="background:#4A90E2;color:#fff;">Forwarded </span> to ' . $CENRO_Office . ' CENR Officer/Head DENR Satellite Office ' . $timestampDisplay . '</p>';

// PENRO 
    
} else if ($row['Title'] == 'CENR Officer/Head DENR Satellite Office' && strpos($row['Details'], 'Documents final review, certification and endorsement approved.') !== false) {
    echo '<p style="font-size: 14px; color: #4A90E2;">';
    echo '<i class="fa-solid fa-check-circle" style="color: #4A90E2;"></i> ';
    echo '<span class="badge" style="background:#4A90E2;color:#fff;">Forwarded </span> to ' . $PENRO_Office . ' PENRO FUU ' . $timestampDisplay . '</p>';

} else if ($row['Title'] == 'PENRO FUU' && strpos($row['Details'], 'Evaluated the endorsed application from the concerned CENR Officer/Head DENR Satellite Offices.') !== false) {
    echo '<p style="font-size: 14px; color: #4A90E2;">';
    echo '<i class="fa-solid fa-check-circle" style="color: #4A90E2;"></i> ';
    echo '<span class="badge" style="background:#4A90E2;color:#fff;">Forwarded </span> to ' . $PENRO_Office . ' PENRO Chief, RPS <br>' . $timestampDisplay . '</p>';

} else if ($row['Title'] == 'PENRO Chief, RPS' && strpos($row['Details'], 'Documents reviewed. Application endorsed to the Chief, TSD.') !== false) {
    echo '<p style="font-size: 14px; color: #4A90E2;">';
    echo '<i class="fa-solid fa-check-circle" style="color: #4A90E2;"></i> ';
    echo '<span class="badge" style="background:#4A90E2;color:#fff;">Forwarded </span> to ' . $PENRO_Office . ' PENRO Chief TSD ' . $timestampDisplay . '</p>';

} else if ($row['Title'] == 'PENRO Chief TSD' && strpos($row['Details'], 'Document reviewed and application recommended to the PENR Officer for endorsement to the Regional Office.') !== false) {
    echo '<p style="font-size: 14px; color: #4A90E2;">';
    echo '<i class="fa-solid fa-check-circle" style="color: #4A90E2;"></i> ';
    echo '<span class="badge" style="background:#4A90E2;color:#fff;">Forwarded </span> to ' . $PENRO_Office . ' PENR Officer ' . $timestampDisplay . '</p>';


    // REGIONAL OFFICE 
    
} else if ($row['Title'] == 'PENR Officer' && strpos($row['Details'], 'Final documents review and approved the endorsement of the application to the RED thru ARD TS.') !== false) {
    echo '<p style="font-size: 14px; color: #4A90E2;">';
    echo '<i class="fa-solid fa-check-circle" style="color: #4A90E2;"></i> ';
    echo '<span class="badge" style="background:#4A90E2;color:#fff;">Forwarded </span> to  Regional Office RO LPDD FUS ' . $timestampDisplay . '</p>';

} else if ($row['Title'] == 'RO FUS' && strpos($row['Details'], 'Evaluated the endorsed application from the concerned PENROs.') !== false) {
    echo '<p style="font-size: 14px; color: #4A90E2;">';
    echo '<i class="fa-solid fa-check-circle" style="color: #4A90E2;"></i> ';
    echo '<span class="badge" style="background:#4A90E2;color:#fff;">Forwarded </span> to Regional Office Chief, LPDD <br>' . $timestampDisplay . '</p>';

} else if ($row['Title'] == 'Chief, LPDD' && strpos($row['Details'], 'Documents reviewed and recommend to ARD TS the approval of the endorsement for the RED to approve the E-Permit.') !== false) {
    echo '<p style="font-size: 14px; color: #4A90E2;">';
    echo '<i class="fa-solid fa-check-circle" style="color: #4A90E2;"></i> ';
    echo '<span class="badge" style="background:#4A90E2;color:#fff;">Forwarded </span> to Regional Office ARD TS ' . $timestampDisplay . '</p>';

} else if ($row['Title'] == 'Regional Executive Director' && strpos($row['Details'], 'Final document review, approval of the Lumber Dealer E-Permit,') !== false) {
    echo '<p style="font-size: 14px; color: #4A90E2;">';
    echo '<i class="fa-solid fa-check-circle" style="color: #4A90E2;"></i> ';
    echo '<span class="badge" style="background:#4A90E2;color:#fff;">Forwarded </span> to Regional Office Records Unit ' . $timestampDisplay . '</p>';

} else if ($row['Title'] == 'Records Unit' && strpos($row['Details'], 'Released the approved Lumber Dealer E-Permit,') !== false) {
    echo '<p style="font-size: 14px; color: #4A90E2;">';
    echo '<i class="fa-solid fa-check-circle" style="color: #4A90E2;"></i> ';
    echo '<span class="badge" style="background:#4A90E2;color:#fff;">Forwarded </span> to Client for CSS ' . $timestampDisplay . '</p>';

} else if ($row['Title'] == 'ARD TS' && strpos($row['Details'], 'Reviewed all the documents and approved the endorsement for the RED to approve the Lumber Dealer E-Permit.') !== false) {
    echo '<p style="font-size: 14px; color: #4A90E2;">';
    echo '<i class="fa-solid fa-check-circle" style="color: #4A90E2;"></i> ';
    echo '<span class="badge" style="background:#4A90E2;color:#fff;">Forwarded </span> to Regional Office Regional Executive Director ' . $timestampDisplay . '</p>';

} else {
    // echo '<p style="font-size: 14px; color: #555;">Additional details or information can be added here.</p>';
}

                              echo   '<br>';
                              echo   '<li style="background: #fff;">' ;
                              echo   '<span style="background: #0d6efd;" id="latestTime"><i class="fa-regular fa-calendar"></i>'.getFullMonthNameFromDate($date).'</span><br>';
                              echo   '<div class="content">' ;
                              echo   '<p style="font-size: 12px; margin-left: 2px; color: #888;"><i class="fa-regular fa-clock"></i>'.$row['Time'].'</p><h5 style="color: #222; font-weight: 600;">'.$row['Title'].'</h5>' ;

                              echo   '<p style="color: #222">';
                              echo   '  '.$row['Details'].'<br><br><a href="#" style="text-decoration: none; color: #0d6efd; font-weight: 600; "></a>';
                              echo   '  </p>';

                              


 


                              echo   '</div>';


                              echo   '</li>' ;


// 1. Create a single timestamp from your date and time fields
$fullTimestamp = strtotime($row['Date'] . ' ' . $row['Time']);

// 2. Format the Date part (e.g., "October 29, 2025")
// This replaces the need for a custom getFullMonthNameFromDate() function
$formattedDate = date('F j, Y', $fullTimestamp);

// 3. Calculate the adjusted time as you requested
$adjustedTime = date('h:i A', strtotime($row['Time'] . ' -1 minutes'));


// 4. Create the final string to display.
// I've wrapped it in a span to make it slightly smaller and gray for readability.
$timestampDisplay = ' <span style="font-size: 12px; color: #888;">' . $formattedDate . ' at ' . $adjustedTime . '</span>';




// --- END OF ADDED LOGIC ---


if ($row['Title'] == 'FUU' && strpos($row['Details'], 'Your application has been evaluated and officially received.') !== false) {
    echo '<p style="font-size: 14px; color: #28a745;">'; // Changed color
    echo '<i class="fa-solid fa-check-circle" style="color: #28a745;"></i> '; // Changed color
        echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>' . $CENRO_Office . ' FUU' . $timestampDisplay . '</p>'; // Changed text & color


} else if ($row['Title'] == 'Credit Officer' && strpos($row['Details'], 'The credit officer will check and verify the order of payment prepared by FUU and Forward to CENR Officer/Head DENR Satellite') !== false) {
    echo '<p style="font-size: 14px; color: #28a745;">'; // Changed color
    echo '<i class="fa-solid fa-check-circle" style="color: #28a745;"></i> '; // Changed color
    echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>' . $CENRO_Office . ' Credit Officer' . $timestampDisplay . '</p>'; // Changed text & color


} else if ($row['Title'] == 'CENR Officer/Head DENR Satellite Office' && strpos($row['Details'], 'The CENR Officer/Head DENR Satellite Office will review and approve the Order of Payment. Forward to the Client for Payment.') !== false) {
    echo '<p style="font-size: 14px; color: #28a745;">'; // Changed color
    echo '<i class="fa-solid fa-check-circle" style="color: #28a745;"></i> '; // Changed color
        echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>' . $CENRO_Office . ' CENR Officer/Head DENR Satellite Office' . $timestampDisplay . '</p>'; // Changed text & color


} else if ($row['Title'] == 'Credit Officer' && strpos($row['Details'], 'Payment confirmed') !== false) {
    echo '<p style="font-size: 14px; color: #28a745;">'; // Changed color
    echo '<i class="fa-solid fa-check-circle" style="color: #28a745;"></i> '; // Changed color
        echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>' . $CENRO_Office . ' Client for Payment' . $timestampDisplay . '</p>'; // Changed text & color


} else if ($row['Title'] == 'FUU' && strpos($row['Details'], 'On-site validation was successfully conducted.') !== false) {
    echo '<p style="font-size: 14px; color: #28a745;">'; // Changed color
    echo '<i class="fa-solid fa-check-circle" style="color: #28a745;"></i> '; // Changed color
        echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>' . $CENRO_Office . ' FUU' . $timestampDisplay . '</p>'; // Changed text & color


} else if ($row['Title'] == 'Chief, RPS' && strpos($row['Details'], 'Document reviewed and application endorsed to the Deputy CENR Officer/Head DENR Satellite Office.') !== false) {
    echo '<p style="font-size: 14px; color: #28a745;">'; // Changed color
    echo '<i class="fa-solid fa-check-circle" style="color: #28a745;"></i> '; // Changed color
        echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>' . $CENRO_Office . ' Chief, RPS' . $timestampDisplay . '</p>'; // Changed text & color


} else if ($row['Title'] == 'Deputy CENRO' && strpos($row['Details'], 'Document reviewed and application recommended to the CENR Officer for endorsement to the concerned PENR Office.') !== false) {
    echo '<p style="font-size: 14px; color: #28a745;">'; // Changed color
    echo '<i class="fa-solid fa-check-circle" style="color: #28a745;"></i> '; // Changed color
        echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>' . $CENRO_Office . ' Deputy CENRO ' . $timestampDisplay . '</p>'; // Changed text & color


// PENRO 
    
} else if ($row['Title'] == 'CENR Officer/Head DENR Satellite Office' && strpos($row['Details'], 'Documents final review, certification and endorsement approved.') !== false) {
    echo '<p style="font-size: 14px; color: #28a745;">'; // Changed color
    echo '<i class="fa-solid fa-check-circle" style="color: #28a745;"></i> '; // Changed color
    echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>' . $CENRO_Office . ' CENR Officer/Head DENR Satellite Office ' . $timestampDisplay . '</p>'; // Changed text & color

} else if ($row['Title'] == 'PENRO FUU' && strpos($row['Details'], 'Evaluated the endorsed application from the concerned CENR Officer/Head DENR Satellite Offices.') !== false) {
    echo '<p style="font-size: 14px; color: #28a745;">'; // Changed color
    echo '<i class="fa-solid fa-check-circle" style="color: #28a745;"></i> '; // Changed color
    echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>' . $PENRO_Office . ' PENRO FUU ' . $timestampDisplay . '</p>'; // Changed text & color

} else if ($row['Title'] == 'PENRO Chief, RPS' && strpos($row['Details'], 'Documents reviewed. Application endorsed to the Chief, TSD.') !== false) {
    echo '<p style="font-size: 14px; color: #28a745;">'; // Changed color
    echo '<i class="fa-solid fa-check-circle" style="color: #28a745;"></i> '; // Changed color
    echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>' . $PENRO_Office . ' PENRO Chief, RPS <br>' . $timestampDisplay . '</p>'; // Changed text & color

} else if ($row['Title'] == 'PENRO Chief TSD' && strpos($row['Details'], 'Document reviewed and application recommended to the PENR Officer for endorsement to the Regional Office.') !== false) {
    echo '<p style="font-size: 14px; color: #28a745;">'; // Changed color
    echo '<i class="fa-solid fa-check-circle" style="color: #28a745;"></i> '; // Changed color
    echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>' . $PENRO_Office . ' PENRO Chief TSD ' . $timestampDisplay . '</p>'; // Changed text & color


    // REGIONAL OFFICE 
    
} else if ($row['Title'] == 'PENR Officer' && strpos($row['Details'], 'Final documents review and approved the endorsement of the application to the RED thru ARD TS.') !== false) {
    echo '<p style="font-size: 14px; color: #28a745;">'; // Changed color
    echo '<i class="fa-solid fa-check-circle" style="color: #28a745;"></i> '; // Changed color
        echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>' . $PENRO_Office . ' PENR Officer ' . $timestampDisplay . '</p>'; // Changed text & color


} else if ($row['Title'] == 'RO FUS' && strpos($row['Details'], 'Evaluated the endorsed application from the concerned PENROs.') !== false) {
    echo '<p style="font-size: 14px; color: #28a745;">'; // Changed color
    echo '<i class="fa-solid fa-check-circle" style="color: #28a745;"></i> '; // Changed color
        echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span> Regional Office RO LPDD FUS ' . $timestampDisplay . '</p>'; // Changed text & color


} else if ($row['Title'] == 'Chief, LPDD' && strpos($row['Details'], 'Documents reviewed and recommend to ARD TS the approval of the endorsement for the RED to approve the E-Permit.') !== false) {
    echo '<p style="font-size: 14px; color: #28a745;">'; // Changed color
    echo '<i class="fa-solid fa-check-circle" style="color: #28a745;"></i> '; // Changed color
        echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>Regional Office Chief, LPDD <br>' . $timestampDisplay . '</p>'; // Changed text & color


} else if ($row['Title'] == 'Regional Executive Director' && strpos($row['Details'], 'Final document review, approval of the Lumber Dealer E-Permit,') !== false) {
    echo '<p style="font-size: 14px; color: #28a745;">'; // Changed color
    echo '<i class="fa-solid fa-check-circle" style="color: #28a745;"></i> '; // Changed color
    // echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>Regional Office ARD TS ' . $timestampDisplay . '</p>'; // Changed text & color    // echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>Regional Office Regional Executive Director ' . $timestampDisplay . '</p>'; // Changed text & color
    echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>Regional Office Regional Executive Director ' . $timestampDisplay . '</p>'; // Changed text & color


} else if ($row['Title'] == 'Records Unit' && strpos($row['Details'], 'Released the approved Lumber Dealer E-Permit,') !== false) {
    echo '<p style="font-size: 14px; color: #28a745;">'; // Changed color
    echo '<i class="fa-solid fa-check-circle" style="color: #28a745;"></i> '; // Changed color
        echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>Regional Office Records Unit ' . $timestampDisplay . '</p>'; // Changed text & color


} else if ($row['Title'] == 'ARD TS' && strpos($row['Details'], 'Reviewed all the documents and approved the endorsement for the RED to approve the Lumber Dealer E-Permit.') !== false) {
    echo '<p style="font-size: 14px; color: #28a745;">'; // Changed color
    echo '<i class="fa-solid fa-check-circle" style="color: #28a745;"></i> '; // Changed color
    // echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>Regional Office RO LPDD Client for CSS ' . $timestampDisplay . '</p>'; // Changed text & color
        echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>Regional Office ARD TS ' . $timestampDisplay . '</p>'; // Changed text & color
    // echo '<span class="badge" style="background:#28a745;color:#fff;">Received by </span>Regional Office Regional Executive Director ' . $timestampDisplay . '</p>'; // Changed text & color

} else {
    // echo '<p style="font-size: 14px; color: #555;">Additional details or information can be added here.</p>';
}

                            }


                                    
                                  }
?>                                  
                                        </div>
                                      </li>
                                         </ul>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>

    </div>
      <script src="js/jquery.min.js"></script> <!-- jQuery for Bootstrap's JavaScript plugins -->
    <script src="js/popper.min.js"></script> <!-- Popper tooltip library for Bootstrap -->
    <script src="js/bootstrap.min.js"></script> <!-- Bootstrap framework -->
    <script src="js/jquery.easing.min.js"></script> <!-- jQuery Easing for smooth scrolling between anchors -->
    <script src="js/swiper.min.js"></script> <!-- Swiper for image and text sliders -->
    <script src="js/jquery.magnific-popup.js"></script> <!-- Magnific Popup for lightboxes -->
    <script src="js/validator.min.js"></script> <!-- Validator.js - Bootstrap plugin that validates forms -->
    <script src="js/scripts.js"></script> <!-- Custom scripts -->
  </body>
</html>



<!-- where Flow_stat >= $l_id  -->

<?php 
include "trackercheckpoint.php";
?>
