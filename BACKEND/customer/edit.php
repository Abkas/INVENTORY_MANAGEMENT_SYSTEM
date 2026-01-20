<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_id'])) {
    $id = (int)$_POST['customer_id'];
    $name = mysqli_real_escape_string($conn, trim($_POST['customer_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['customer_email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['customer_phone']));

    if ($name !== '') {
        $query = "UPDATE customer SET customer_name = '$name', customer_email = '$email', customer_phone = '$phone' WHERE customer_id = $id";
        if (mysqli_query($conn, $query)) {
            $_SESSION['msg'] = "Customer updated successfully!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }
}
header("Location: ../customers.php");
exit();
?>
