<?php
    session_start();
    require "../db.php";


    if(isset($_SESSION['user_id'])){
        header("Location: ../dashboard.php");
        exit();
    }

     

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $username = $_POST['username'];



        $stmt = $connect->prepare("INSERT INTO users (email,password,username) VALUES(?,?,?)");
        $stmt->bind_param('sss',$email,$password,$username);
       

        if($stmt->execute()){
            header("Location: ../auth/login.php");
            exit();
        }else{
            echo "Error: " . mysqli_error($connect);
        }

    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Dashboard</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
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

        .register-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        .register-card {
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
            right: 50px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            font-size: 16px;
        }

        .terms-group {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .terms-group input {
            margin-right: 10px;
            margin-top: 3px;
        }

        .terms-group label {
            font-size: 14px;
            color: #6c757d;
            line-height: 1.5;
        }

        .terms-group a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .terms-group a:hover {
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

        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 15px;
            color: #6c757d;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
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
            .register-card {
                padding: 30px 25px;
            }
            
            .logo h1 {
                font-size: 30px;
            }
            
            .form-header h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">

            <div class="form-header">
                <h2>Create Account</h2>
                <p>Sign up to access your dashboard</p>
            </div>

            <form id="registerForm" method="POST" >
                

                <div class="form-group">
                    <label for="username">username</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="text" id="username" name="username" placeholder="Enter your username" required>
                    </div>
                    <div class="error-message" id="usernameError">Please enter a valid username</div>
                </div>
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
                        <input type="password" id="password" name="password" placeholder="Create a password" required>
                    </div>
                    <div class="error-message" id="passwordError">Password must be at least 8 characters</div>
                </div>

                

                

                <button type="submit" class="btn btn-primary">Create Account</button>

                <div class="login-link">
                    Already have an account? <a href="login.php">Sign in</a>
                </div>

                <div class="success-message" id="successMessage">
                    Account created successfully! Redirecting to dashboard...
                </div>
            </form>
        </div>
    </div>

</body>
</html>