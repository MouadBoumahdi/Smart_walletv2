<?php 
 
    $servername = "localhost";
    $username = "root";
    $password = "";
    $db_name = "Smart_wallet";

    $connect = mysqli_connect($servername,$username,$password,$db_name);

    if(!$connect){
        die("Connection failed: " . mysqli_connect_error());
    }
?>