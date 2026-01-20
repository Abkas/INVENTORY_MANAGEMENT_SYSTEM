<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supplier_name'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['supplier_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['supplier_email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['supplier_phone']));

    if ($name !== '') {
        $query = "INSERT INTO supplier (supplier_name, supplier_email, supplier_phone) VALUES ('$name', '$email', '$phone')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['msg'] = "Supplier added successfully!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }
}
header("Location: ../suppliers.php");
exit();
?>
