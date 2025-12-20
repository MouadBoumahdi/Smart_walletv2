<?php
    session_start();
    require "../db.php";



    if($_SERVER['REQUEST_METHOD']=="POST"){

        // $user_id = $_SESSION['user_id'];
        $montant = $_POST["amount"];
        $person_id = $_POST["id"];
        $card_id = $_POST["card_id"];
        
        $check_user = "SELECT id from users";
        $checkresult  = mysqli_query($connect,$check_user);
        while($row = mysqli_fetch_assoc($checkresult)){
            if($person_id == $row['id']){
                $usercheck = true;
               
            }else{
                $usercheck = false;
            }
        } 

        if($usercheck){
             $checkmontant = "SELECT balance FROM cards WHERE id = '$card_id' ";
                $result = mysqli_query($connect,$checkmontant);
                $row = mysqli_fetch_assoc($result);
                
                if($montant > $row['balance']){
                    echo "Error: Insufficient funds.";
                    exit();
                }else{
                        $addsql = "INSERT INTO sendmoney(amount,person_id,card_id) values('$montant','$person_id','$card_id')";
                        $updatemycard = "UPDATE cards SET  balance = balance - $montant WHERE id = $card_id ";
                        $sendsql = "UPDATE cards SET balance = balance + $montant where user_id = '$person_id' and type='Primary' ";



                if(mysqli_query($connect,$sendsql) && mysqli_query($connect,$addsql) && mysqli_query($connect,$updatemycard)){
                    header("Location: ../dashboard.php");
                    exit();
                }else{
                    echo "error : " . mysqli_error($connect);
                }
                }
        }else{
            echo "Error: User ID does not exist.";
            header("Location: ../dashboard.php");
            exit();
        }
     
        

    
     
}
?>