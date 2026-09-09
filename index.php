<?php
require_once 'config/config.php';
require_once 'config/function.php';

if(isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/app/' . $_SESSION['user_role'] . '/index.php');
    exit;
}

$error='';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if(loginUser($pdo, $login, $password)) {
        echo 'Location: ' . BASE_URL . '/app/' . $_SESSION['user_role'] . '/index.php';
        header('Location: ' . BASE_URL . '/app/' . $_SESSION['user_role'] . '/index.php');
        exit;

    } 
        $error = 'Invalid login credentials.';
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Website</title>
    </head>

    <body style="margin: 0; padding: 0; background-color: #f2f2f2; font-family: Arial, sans-serif;">

        <form method="POST"
              style="width: 300px; margin: 100px auto; padding: 30px; background-color: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">

            <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">
                Username or Email
            </label>

            <input type="text"
                   name="login"
                   required
                   style="width: 100%; padding: 10px; margin-bottom: 20px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 5px; font-size: 14px;">

            <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">
                Password
            </label>

            <input type="password"
                   name="password"
                   required
                   style="width: 100%; padding: 10px; margin-bottom: 25px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 5px; font-size: 14px;">

            <button type="submit"
                    style="width: 100%; padding: 12px; border: none; border-radius: 5px; background-color: #007bff; color: white; font-size: 16px; font-weight: bold; cursor: pointer;">
                Sign In
            </button>

        </form>

    </body>
</html>

