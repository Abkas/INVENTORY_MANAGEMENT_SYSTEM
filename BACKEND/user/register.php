<?php
require_once __DIR__ . '/../db/connect.php';
require_once __DIR__ . '/../AUTH/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        header("Location: /INVENTORY_SYSTEM/BACKEND/user/register.php?error=Username and password are required.");
        exit();
    }

    // Check if user exists
    $check = mysqli_query($conn, "SELECT * FROM user WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        header("Location: /INVENTORY_SYSTEM/BACKEND/user/register.php?error=Username already exists.");
        exit();
    }

    $password = hashPassword($password);
    $result = mysqli_query($conn,
        "INSERT INTO user (username, password)
         VALUES ('$username', '$password')"
    );
    if (!$result) {
        header("Location: /INVENTORY_SYSTEM/BACKEND/user/register.php?error=Registration failed. Please try again.");
        exit();
    }

    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php?success=Registration successful! Please login.");
    exit();
} else {
    // Display the register form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register - Inventory Management</title>
    <link rel="stylesheet" href="/INVENTORY_SYSTEM/BACKEND/css/login.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2>Join Us</h2>
            <p class="form-subtitle">Create an account to start managing your business</p>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message" style="background: #dcfce7; color: #166534; padding: 0.75rem; border-radius: 0.75rem; margin-bottom: 1.5rem; text-align: center; font-size: 0.85rem; font-weight: 500; border: 1px solid #bcf0da;">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>

            <form action="/INVENTORY_SYSTEM/BACKEND/user/register.php" method="POST">
                <div class="input-group">
                    <label>Choose Username</label>
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <label>Create Password</label>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit">Create Account</button>
            </form>
            <p class="form-link">Already have an account? <a href="/INVENTORY_SYSTEM/BACKEND/user/login.php">Sign In</a></p>
        </div>
    </div>
    </body>
    </html>
    <?php
}
?>
