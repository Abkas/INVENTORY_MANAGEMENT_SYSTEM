<?php
require_once __DIR__ . '/../db/connect.php';
require_once __DIR__ . '/../AUTH/auth.php';


$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    http_response_code(400);
    echo '<script>alert("Username and password are required."); window.history.back();</script>';
    exit();
}

// Check if user exists
$check = mysqli_query($conn, "SELECT * FROM user WHERE username='$username'");
if (mysqli_num_rows($check) > 0) {
    echo '<script>alert("Username already exists."); window.history.back();</script>';
    exit();
}

$password = hashPassword($password);
$result = mysqli_query($conn,
    "INSERT INTO user (username, password)
     VALUES ('$username', '$password')"
);
if (!$result) {
    echo '<script>alert("Registration failed. Please try again."); window.history.back();</script>';
    exit();
}

echo '<script>alert("Registration successful! Please login."); window.location.href = "/INVENTORY_SYSTEM/FRONTEND/pages/login.html";</script>';
exit();
?>
