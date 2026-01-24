<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . '/../db/connect.php';
require_once __DIR__ . '/../AUTH/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        header("Location: ../user/login.php?error=Username and password are required.");
        exit();
    }

    $result = mysqli_query($conn, "SELECT * FROM user WHERE username='$username'");
    
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    $user = mysqli_fetch_assoc($result);

    if ($user && verifyPassword($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user'] = $user['username'];
        $_SESSION['role'] = $user['role']; // Store role in session
        header("Location: ../index.php");
        exit;
    } else {
        header("Location: ../user/login.php?error=Invalid username or password.");
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
        <link rel="stylesheet" href="../css/login.css">
    </head>
    <body>
        <div class="container">
            <div class="form-box">
                <h2>Welcome Back</h2>
                <p class="form-subtitle">Please sign in to manage your inventory</p>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <form action="../user/login.php" method="POST">
                    <div class="input-group">
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Enter your username" required>
                    </div>
                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <button type="submit">Sign In</button>
                </form>
                <p class="form-link">New here? <a href="../user/register.php">Create an account</a></p>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>
