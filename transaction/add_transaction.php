<?php
if($_SERVER['REQUEST_METHOD']=="POST"){
    session_start();
    require "../db.php";

    $user_id = $_SESSION['user_id'];
    $amount = $_POST["amount"];
    $transaction_type = $_POST["type"];
    $source = $_POST["source"];
    $category = $_POST["category"];

 
     if($transaction_type == "revenue"){
         $card_query = "SELECT id FROM cards WHERE user_id = '$user_id' AND type='Primary' LIMIT 1";
            $card_result = mysqli_query($connect, $card_query);
            $card_row = mysqli_fetch_assoc($card_result);
            $card_id = $card_row['id'];


            $updatecard = 'UPDATE cards SET balance = balance + '.$amount.' WHERE id = '.$card_id;
            mysqli_query($connect, $updatecard);


         $insert_transaction_sql = "INSERT INTO transaction(card_id, amount, type, description) VALUES ( '$card_id', '$amount', '$transaction_type' , '$source') ";
        
         if(mysqli_query($connect, $insert_transaction_sql)){
                header("Location: ../dashboard.php");
                exit();
            } else {
                echo "Error inserting transaction: " . mysqli_error($connect);
            }
    }
        else {
            $montant_category = 0.00;
            switch($category){
                case "Nourriture":
                    $montant_category = 1500.00;
                    break;
                case "Transport":
                    $montant_category = 800.00;
                    break;
                case "sante":
                    $montant_category = 2500.00;
                    break;
            }

            $sum_amount_month = "SELECT sum(amount) as Summ from transaction where type = 'depense' and description = '$category' and card_id = '$card_id' ";
            $sum_result = mysqli_query($connect, $sum_amount_month); 
            $sum_row = mysqli_fetch_assoc($sum_result);
            $sum = $sum_row['Sum'];
            if($sum > $montant_category){
                echo "<script>
                        alert('Transaction amount exceeds the limit for the selected category.');
                        window.location.href = '../dashboard.php';
                    </script>";
                exit();
            }else{
                $card_id = $_POST["card_id"];
                $updatecard = 'UPDATE cards SET balance = balance - '.$amount.' WHERE id = '.$card_id;
                mysqli_query($connect, $updatecard);

            $insert_transaction_sql = "INSERT INTO transaction(card_id, amount, type, description) VALUES ( '$card_id', '$amount', '$transaction_type' , '$category') ";

            if(mysqli_query($connect, $insert_transaction_sql)){
                header("Location: ../dashboard.php");
                exit();
            } else {
                echo "Error inserting transaction: " . mysqli_error($connect);
            }
            }
        
    }



     
       
    
}              
?>