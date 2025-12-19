<?php
     require "db.php";
        $user_id = $_SESSION["user_id"];
        $query = "SELECT * FROM cards where user_id = '$user_id' ";


    $result = mysqli_query($connect,$query);

   

  
?>