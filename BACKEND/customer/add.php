<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_name'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['customer_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['customer_email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['customer_phone']));

    if ($name !== '') {
        $query = "INSERT INTO customer (customer_name, customer_email, customer_phone) VALUES ('$name', '$email', '$phone')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['msg'] = "Customer added successfully!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }
}
header("Location: ../customers.php");
exit();
?>
