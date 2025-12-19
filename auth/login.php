<?php
 session_start();

require "../db.php";
require "../autoload.php";

$ip = $_SERVER['REMOTE_ADDR'];

if(isset($_SESSION['user_id'])){
        header("Location: ../dashboard.php");
        exit();
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $email = $_POST["email"];
        $_SESSION['email'] = $email;
        $password =   $_POST["password"] ;

        $stmt = $connect->prepare("SELECT id,password,username FROM users WHERE email=?");
        $stmt->bind_param('s',$email);
        
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id,$hashed_password,$username);
        $stmt->fetch();
        
        $_SESSION['name'] = $username;
        $_SESSION['temp_user_id'] = $user_id;         

        
            if($stmt->num_rows > 0 && password_verify($password,$hashed_password)){
            // otp generator
            $otp = rand(100000,999999);
            // $_SESSION['otp'] = $otp;
            sendmail($email,$otp);
            $expires_at = date("Y-m-d H:i:s", strtotime("+1 minutes"));

            $otp_stmt = $connect->prepare("INSERT INTO otp (otp_code, user_id,expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE otp_code = VALUES(otp_code), expires_at = VALUES(expires_at) ");
            $otp_stmt->bind_param('iss',$otp,$user_id,$expires_at);
            $otp_stmt->execute();

            
           
            header("Location: otp.php");
        }else{
            echo "
                <script>
                    alert('Try another password');
                </script>
            ";
        }
        
        
           
       
    } 
?>    


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dashboard</title>
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

        .login-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        .login-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 40px;
            box-shadow: var(--box-shadow);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            font-size: 36px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .logo h1 span {
            color: var(--warning-color);
        }

        .logo p {
            color: #6c757d;
            margin-top: 5px;
            font-size: 14px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h2 {
            font-size: 28px;
            font-weight: 600;
            color: #212529;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #6c757d;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 16px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            font-size: 16px;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input {
            margin: 0;
        }

        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-password:hover {
            text-decoration: underline;
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

        .btn-google {
            background-color: white;
            color: #333;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-google:hover {
            background-color: #f8f9fa;
            border-color: #ccc;
        }

        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }

        .divider:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background-color: #eee;
        }

        .divider span {
            background-color: white;
            padding: 0 15px;
            color: #6c757d;
            font-size: 14px;
            position: relative;
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            font-size: 15px;
            color: #6c757d;
        }

        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }

        .success-message {
            color: #2ecc71;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 30px 25px;
            }
            
            .logo h1 {
                font-size: 30px;
            }
            
            .form-header h2 {
                font-size: 24px;
            }
            
            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>        
</head>
<body>
    <div class="login-container">
        <div class="login-card">
           

            <div class="form-header">
                <h2>Welcome Back</h2>
                <p>Sign in to access your dashboard</p>
            </div>    

            <form id="loginForm" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="text" id="email" name="email" placeholder="Enter your email" required>
                    </div>    
                    <div class="error-message" id="emailError">Please enter a valid email address</div>
                </div>    

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        
                    </div>    
                </div>    

                

                <button type="submit" class="btn btn-primary">Sign In</button>

               

                <div class="register-link">
                    Don't have an account? <a href="register.php">Sign up</a>
                </div>        
            </form>
        </div>    
    </div>    
</body>    
</html>



