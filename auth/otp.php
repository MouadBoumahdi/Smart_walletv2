<?php
session_start();
    require "../db.php";

    require "../autoload.php";
    
// print_r($_SESSION);
        

$temp_user_id = $_SESSION['temp_user_id'];

    if($_SERVER["REQUEST_METHOD"]== "POST"){
        $stmt = $connect->prepare("SELECT otp_code,expires_at from otp where user_id='$temp_user_id' ");
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($otp_code,$expires_at);
        $stmt->fetch();


if(isset($_POST["verifyBtn"])){
 $otp = $_POST["otpInput"];
 
    if(date("Y-m-d H:i:s") < $expires_at){
        if($otp === $otp_code){
                $_SESSION['user_id'] = $temp_user_id;
                header("Location:../dashboard.php");
            }else{
                echo "<script> alert('OTP IS WRONG TRY AGAIN') </script>";
            }
    }else{
        echo "<script> alert('OTP EXPIRED TRY AGAIN') </script>";
    }
        
}


if(isset($_POST["resend"])){
       // otp generator
            $otp = rand(100000,999999);
            sendmail($_SESSION['email'],$otp);
            $expires_at = date("Y-m-d H:i:s", strtotime("+1 minutes"));

            $otp_stmt = $connect->prepare("INSERT INTO otp (otp_code, user_id,expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE otp_code = VALUES(otp_code), expires_at = VALUES(expires_at) ");
            $otp_stmt->bind_param('iss',$otp,$user_id,$expires_at);
            $otp_stmt->execute();

            
            $_SESSION['temp_user_id'] = $user_id;
            header("Location: otp.php");
    }

    }
    

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --success-color: #4cc9f0;
            --warning-color: #f72585;
            --border-radius: 12px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f5f7fb;
            color: #212529;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .otp-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .otp-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 40px;
            box-shadow: var(--box-shadow);
            text-align: center;
        }

        .logo {
            margin-bottom: 25px;
        }

        .logo h1 {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .logo h1 span {
            color: var(--warning-color);
        }

        .form-header {
            margin-bottom: 25px;
        }

        .form-header h2 {
            font-size: 24px;
            font-weight: 600;
            color: #212529;
            margin-bottom: 8px;
        }

        .form-header p {
            color: #6c757d;
            font-size: 14px;
            line-height: 1.5;
        }

        .email-display {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 10px 15px;
            margin: 15px 0;
            border: 1px solid #e9ecef;
            font-weight: 500;
            color: #495057;
            font-size: 14px;
        }

        .email-display i {
            color: var(--primary-color);
            margin-right: 8px;
        }

        .otp-single-container {
            margin: 25px 0;
        }

        .otp-single-input {
            width: 100%;
            padding: 16px 20px;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 10px;
            text-align: center;
            border: 2px solid #ddd;
            border-radius: 8px;
            background-color: white;
            transition: all 0.3s ease;
        }

        .otp-single-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .otp-single-input.valid {
            border-color: var(--primary-color);
            background-color: rgba(67, 97, 238, 0.05);
        }

        .timer {
            margin: 15px 0;
            font-size: 14px;
            color: #6c757d;
        }

        .timer span {
            color: var(--primary-color);
            font-weight: 600;
        }

        .timer.expired {
            color: #e74c3c;
        }

        .timer.expired span {
            color: #e74c3c;
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #3a56d4;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(67, 97, 238, 0.2);
        }

        .btn-primary:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .resend-link {
            margin-top: 20px;
            font-size: 14px;
            color: #6c757d;
        }

        .resend-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .resend-link a:hover {
            text-decoration: underline;
        }

        .resend-link a.disabled {
            color: #cccccc;
            cursor: not-allowed;
            text-decoration: none;
        }

        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin: 10px 0;
            display: none;
        }

        .success-message {
            color: #2ecc71;
            font-size: 14px;
            margin: 10px 0;
            display: none;
        }

        .back-link {
            margin-top: 15px;
            font-size: 14px;
            color: #6c757d;
        }

        .back-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .otp-card {
                padding: 30px 25px;
            }
            
            .logo h1 {
                font-size: 28px;
            }
            
            .form-header h2 {
                font-size: 22px;
            }
            
            .otp-single-input {
                font-size: 20px;
                letter-spacing: 8px;
                padding: 14px 16px;
            }
        }
    </style>        
</head>
<body>
    <div class="otp-container">
        <div class="otp-card">
           

            <div class="form-header">
                <h2>OTP Verification</h2>
                <p>Enter the 6-digit code sent to your email</p>
            </div>

           

            <form id="otpForm" method="POST">
                <div class="otp-single-container">
                    <input 
                        type="text" 
                        name="otpInput" 
                        class="otp-single-input" 
                        maxlength="6" 
                        inputmode="numeric" 
                        pattern="[0-9]*"
                        placeholder="000000"
                      >
                </div>

                
           
 

                <button type="submit" class="btn btn-primary" name="verifyBtn" >Verify OTP</button>

                <div class="resend-link">
                    Didn't receive the code? 
                    <button type="submit" class="btn btn-primary" name="resend" style="background-color: #6c757d; margin-top: 0;">Resend OTP</button>
                </div>

                <div class="back-link">
                    <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
                </div>
            </form>
        </div>
    </div>

  
</body>
</html>