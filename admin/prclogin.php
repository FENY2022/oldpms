

<?php


    if (session_status() == PHP_SESSION_NONE) {
        session_start();
       }
      


    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        // header("location: ../client/dashboard_requirement.php");
        // header("location: index.php");
        
        exit;
    }

    include('../processphp/config.php');
    
    if (isset($_POST['btn'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $query = $connection->prepare("SELECT * FROM denr_users WHERE username=:username");
        $query->bindParam("username", $username, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            // echo '<p class="error">Email and Password combination is wrong!</p>';

            // $em = "username and Password combination is wrong!";
            // header ("Location: univmodal.php?error=$em");



            function function_alert($message) {
      
                // Display the alert box 
                echo "<script type='text/javascript'>alert('Username and Password combination is wrong!');location='login.php';</script>";
            }
              
              
            // Function call
            function_alert("Username and Password combination is wrong!");
            
        } else {

       
            // $password_hash = password_hash($password, PASSWORD_BCRYPT);

            // echo(htmlentities($result['password']));


            // echo(htmlentities($password_hash));

            // echo(htmlentities(password_verify($password, $result['password'])));
            $id = $result['user_id'];
            $hash =  $result['password'];
            $_SESSION["user_role_id"] =  $result['user_role_id'];
            $name =  $result['name'];
  
      

if (password_verify($password, $result['password']))
{

 
    $_SESSION["loggedin"] = true;
    $_SESSION["user_id"] = $id;
    $_SESSION["username"] = $username;   
    $_SESSION["name"] = $name;
    $_SESSION["user_role_id"] =  $result['user_role_id'];
    

    if (($result['user_role_id']) == ('99')){

    header("location: index.php");

    }

    if (($result['user_role_id']) == ('1')){

        header("location: ../main/production/application.php");
    
        }


        if (($result['user_role_id']) == ('2')){

            header("location: ../main/production/application.php");
        
            }



        if (($result['user_role_id']) == ('4')){

            header("location: ../main/production/application.php");
        
            }

            if (($result['user_role_id']) == ('7')){

                header("location: ../main/production/application.php");
            
                }

                if (($result['user_role_id']) == ('8')){

                    header("location: ../main/production/application.php");
                
                    }

                    if (($result['user_role_id']) == ('9')){

                        header("location: ../main/production/application.php");
                    
                        }

                        if (($result['user_role_id']) == ('9.1')){

                            header("location: ../main/production/application.php");
                        
                            }


                        if (($result['user_role_id']) == ('10')){

                            header("location: ../main/production/application.php");
                        
                            }
    

                        if (($result['user_role_id']) == ('11')){

                            header("location: ../main/production/application.php");
                        
                            }


                            if (($result['user_role_id']) == ('12')){

                                header("location: ../main/production/application.php");
                            
                                }

                                
                            if (($result['user_role_id']) == ('12.5')){

                                header("location: ../main/action.php");
                            
                                }


                                
                            if (($result['user_role_id']) == ('13')){

                                header("location: ../main/action.php");
                            
                                }


                                if (($result['user_role_id']) == ('14')){

                                    header("location: ../main/action.php");
                                
                                    }

                                    
                                if (($result['user_role_id']) == ('15')){

                                    header("location: ../main/action.php");
                                
                                    }

                                                                        
                                if (($result['user_role_id']) == ('16')){

                                    header("location: ../main/action.php");
                                
                                    }
                                    
                                    if (($result['user_role_id']) == ('17')){

                                        header("location: ../main/records/action.php");
                                    
                                        }


                                        if (($result['user_role_id']) == ('19')){

                                            header("location: ../main/tableic.php");
                                        
                                            }


} 
else 
{





    function function_alert($message) {
      
        // Display the alert box 
        echo "<script type='text/javascript'>alert('Invalid Password.');location='login.php';</script>";
    }
      
      
    // Function call
    function_alert("Invalid Password.");
    






    //  $em = "Invalid password.";
    //  header ("Location: univmodal.php?error=$em");
    
    // echo 'Invalid password.';
    // header("Location: ../");
   
}

            // if (password_verify($password, $result['password'])) {
                // $_SESSION['user_id'] = $result['id'];
                // echo '<p class="success">Congratulations, you are logged in!</p>';
            // } else {
                // echo '<p class="error">Username password combination is wrong! 1</p>';
            // }
        }
    }
?>


