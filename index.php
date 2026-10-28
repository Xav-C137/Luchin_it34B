<?php
require_once 'config/config.php';   

if(isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/app/' . $_SESSION['user_role'] . '/index.php');
    exit;
}

$error='';

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    requireCsrfToken();

    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    $error = 'Invalid login credentials.';

        if ($login === '' || $password === '') {
            
            // Log incomplete login attempt
            logActivity($pdo,
                null,
                $login,
                'login',
                'failed'    
            );

        } else {

            $result = loginUser($pdo, $login, $password);

            if ($result===true){
                // Log successful login attempt
                logActivity(
                $pdo,$_SESSION['user_id'],
                $_SESSION['user_email'],
                'login',
                'success'
                );

            header('Location: ' . BASE_URL . '/app/' . $_SESSION['user_role'] . '/index.php');
            exit;

            } elseif ($result==='active_session'){

                echo 'This account is already logged in from another device.';
                $error = 'This account is already logged in from another device.';
                
                } else {
                $error = 'Invalid login credentials.';
            }
        }
        
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Website</title>

        <style>
            body {
                margin: 0;
                padding: 0;
                background-color: #f2f2f2;
                font-family: Arial, sans-serif;
            }

            .login-form {
                width: 300px;
                margin: 100px auto;
                padding: 30px;
                background-color: #fff;
                border-radius: 10px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            }

            .form-label {
                display: block;
                margin-bottom: 8px;
                font-weight: bold;
                color: #333;
            }

            .form-input {
                width: 100%;
                padding: 10px;
                margin-bottom: 20px;
                box-sizing: border-box;
                border: 1px solid #ccc;
                border-radius: 5px;
                font-size: 14px;
            }

            .form-input--password {
                margin-bottom: 25px;
            }

            .btn-submit {
                width: 100%;
                padding: 12px;
                border: none;
                border-radius: 5px;
                background-color: #007bff;
                color: #fff;
                font-size: 16px;
                font-weight: bold;
                cursor: pointer;
            }

            .timeout-message {
                text-align: center;
                color: red;
                font-weight: bold;
            }
        </style>
    </head>

    <body>

        <form method="POST" class="login-form">

            <label class="form-label">
                Username or Email
            </label>

            <input type="hidden" name="csrf_token" value="<?php  htmlspecialchars(generateCsrfToken()); ?>">

            <input type="text"
                    name="login"
                    class="form-input">

            <label class="form-label">
                Password
            </label>

            <input type="password"
                    name="password"
                    class="form-input form-input--password">

            <button type="submit" class="btn-submit">
                Sign In
            </button>

        </form>

        <?php if (isset($_GET['timeout'])): ?>
            <p class="timeout-message">
                Your session has expired due to inactivity. Please log in again.
            </p>
        <?php endif; ?>
    </body>
</html>

