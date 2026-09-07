<?php
session_start();

require_once(__DIR__ . '/../includes/activity-logger.php');

define('BASE_URL', 'http://localhost/dev/it34b');

define('DB_HOST', 'localhost');
define('DB_NAME', 'it34-lab-dcb');
define('DB_USER', 'root');
define('DB_PASS', '');


try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<?php
function redirect($path) {
    header("Location: " . BASE_URL . $path);
    exit(); 
}

function loginUser($pdo,$login,$password) {
    #Query 2
    $sql = "
         SELECT
            user_id,
            user_email,
            user_username,
            user_password,
            user_role
        FROM users
        WHERE user_email = :login
            OR user_username = :login
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['login' => $login]);

    $user = $stmt->fetch();

    if(!$user) {
        return false;
    }

    if(!password_verify($password, $user['user_password'])) {
        return false;
    }

    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_email'] = $user['user_email'];
    $_SESSION['user_username'] = $user['user_username'];
    $_SESSION['user_role'] = $user['user_role'];
    
    return true;
}

function requireLogin() {
    if(!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function requireRole($role) {
    requireLogin();

    if($_SESSION['user_role'] !== $role) {
        http_response_code(403);
        die('Access Denied.');
    }
}

?>