<?php 

    $db_server = "localhost:3306";
    $db_user = "root";
    $db_pass = "12345";
    $db_name = "db_users";
    //$conn = ""; 

    //try{ 
    $conn  = mysqli_connect ($db_server, $db_user, $db_pass, $db_name );


    /*}
    catch( mysqli_sql_exception){
        echo "Connected.";
    }*/
    if ($conn ->connect_error) {
        die("Connection failed: " . $conn->connect_error);
     }
     echo "si";
     /* echo "Not connected, error: " . $mysqli_connection->connect_error;
     else {
        echo "Connected.";
     }*/
    
?>