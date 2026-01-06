<?php
require_once __DIR__ . '/../db/connect.php';
require_once __DIR__ . '/../AUTH/auth.php';


$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    http_response_code(400);
    exit('Username and password are required.');
}

$password = hashPassword($password);

mysqli_query($conn,
    "INSERT INTO user (username, password)
     VALUES ('$username', '$password')"
);

header("Location: /INVENTORY_MANAGEMENT/FRONTEND/pages/login.html");
exit;
?>
