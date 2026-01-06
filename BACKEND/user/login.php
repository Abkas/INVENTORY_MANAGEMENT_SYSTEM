<?php

session_start();
require_once __DIR__ . '/../db/connect.php';
require_once __DIR__ . '/../AUTH/auth.php';


$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    http_response_code(400);
    exit('Username and password are required.');
}


$result = mysqli_query($conn,
    "SELECT * FROM user WHERE username='$username'"
);
$user = mysqli_fetch_assoc($result);

if ($user && verifyPassword($password, $user['password'])) {
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    header("Location: index.php");
    exit;
} else {
    echo "Invalid login";
}
?>
