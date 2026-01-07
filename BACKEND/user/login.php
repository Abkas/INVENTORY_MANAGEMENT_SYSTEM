<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/index.php");
    exit();
}

require_once __DIR__ . '/../db/connect.php';
require_once __DIR__ . '/../AUTH/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php?error=Username and password are required.");
        exit();
    }

    $result = mysqli_query($conn, "SELECT * FROM user WHERE username='$username'");
    $user = mysqli_fetch_assoc($result);

    if ($user && verifyPassword($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user'] = $user['username'];
        header("Location: /INVENTORY_SYSTEM/BACKEND/index.php");
        exit;
    } else {
        header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php?error=Invalid username or password.");
        exit();
    }
} else {
    // Display the login form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Inventory Management</title>
        <link rel="stylesheet" href="/INVENTORY_SYSTEM/BACKEND/css/login.css">
    </head>
    <body>
        <div class="container">
            <div class="form-box">
                <h2>Login</h2>
                <?php if (isset($_GET['error'])): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>
                <form action="/INVENTORY_SYSTEM/BACKEND/user/login.php" method="POST">
                    <div class="input-group">
                        <input type="text" name="username" placeholder="Username" required>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit">Login</button>
                </form>
                <p class="form-link">Don't have an account? <a href="/INVENTORY_SYSTEM/BACKEND/user/register.php">Register here</a></p>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>
