<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['warehouse_name'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['warehouse_name']));
    $location = mysqli_real_escape_string($conn, trim($_POST['location']));

    if ($name !== '') {
        $query = "INSERT INTO warehouse (warehouse_name, location) VALUES ('$name', '$location')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['msg'] = "Warehouse added successfully!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }
}
header("Location: ../warehouses.php");
exit();
?>
