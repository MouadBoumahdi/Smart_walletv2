<?php
    session_start();
    require "../db.php";


     if($_SERVER['REQUEST_METHOD']=="POST"){
        $user_id = $_SESSION['user_id'];
        $cardBank = $_POST["cardBank"];
        $cardHolder = $_POST["cardHolder"];
        $cardNumber = $_POST["cardNumber"];
        $cardExpiry = $_POST["cardExpiry"];
        $cardCVV = $_POST["cardCVV"];
        $initialBalance = $_POST["initialBalance"];
        $typecard = $_POST["typecard"];



        $sql = "INSERT INTO cards(user_id,bank_name,card_name,card_number,card_expiration,card_cvv,balance,type) VALUES ('$user_id','$cardBank','$cardHolder','$cardNumber','$cardExpiry','$cardCVV','$initialBalance','$typecard')";

        if(mysqli_query($connect,$sql)){
            header("Location: ../dashboard.php");
            exit();
        }else{
            echo "Error: " . mysqli_error($conn);
        }
    }
?>