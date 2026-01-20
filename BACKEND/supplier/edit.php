<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supplier_id'])) {
    $id = (int)$_POST['supplier_id'];
    $name = mysqli_real_escape_string($conn, trim($_POST['supplier_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['supplier_email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['supplier_phone']));

    if ($name !== '') {
        $query = "UPDATE supplier SET supplier_name = '$name', supplier_email = '$email', supplier_phone = '$phone' WHERE supplier_id = $id";
        if (mysqli_query($conn, $query)) {
            $_SESSION['msg'] = "Supplier updated successfully!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }
}
header("Location: ../suppliers.php");
exit();
?>
