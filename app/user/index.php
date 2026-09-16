<?php

require '../../config/config.php';
require '../../config/function.php';

requireRole('user');

logActivity(   
    $pdo,
    $_SESSION['user_id'],
    $_SESSION['user_email'],
    'view_activity_logs',
    'success'
);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <h1>Welcome, User</h1>
        <a href="../../auth/signout.php">Sign Out</a>
    </body>
</html>