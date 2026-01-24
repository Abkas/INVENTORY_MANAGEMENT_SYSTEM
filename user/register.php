<?php
require_once __DIR__ . '/../db/connect.php';
require_once __DIR__ . '/../AUTH/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'staff'; 

    if ($username === '' || $password === '') {
        header("Location: ../user/register.php?error=Username and password are required.");
        exit();
    }
    
    if (!in_array($role, ['admin', 'staff'])) {
        $role = 'staff';
    }

    $check = mysqli_query($conn, "SELECT * FROM user WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        header("Location: ../user/register.php?error=Username already exists.");
        exit();
    }

    $password = hashPassword($password);
    $result = mysqli_query($conn,
        "INSERT INTO user (username, password, role)
         VALUES ('$username', '$password', '$role')"
    );
    if (!$result) {
        header("Location: ../user/register.php?error=Registration failed. Please try again.");
        exit();
    }

    header("Location: ../user/login.php?success=Registration successful! Please login.");
    exit();
} else {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register - Inventory Management</title>
    <link rel="stylesheet" href="../css/login.css">
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

            <form action="../user/register.php" method="POST">
                <div class="input-group">
                    <label>Choose Username</label>
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <label>Create Password</label>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-group">
                    <label>Select Role</label>
                    <select name="role" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 1rem; color: #334155; background-color: #f8fafc;">
                        <option value="staff">Staff (Restricted Access)</option>
                        <option value="admin">Admin (Full Access)</option>
                    </select>
                </div>
                <button type="submit">Create Account</button>
            </form>
            <p class="form-link">Already have an account? <a href="../user/login.php">Sign In</a></p>
        </div>
    </div>
    </body>
    </html>
    <?php
}
?>
