<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['warehouse_id'])) {
    $id = (int)$_POST['warehouse_id'];
    $name = mysqli_real_escape_string($conn, trim($_POST['warehouse_name']));
    $location = mysqli_real_escape_string($conn, trim($_POST['location']));

    if ($name !== '') {
        $query = "UPDATE warehouse SET warehouse_name = '$name', location = '$location' WHERE warehouse_id = $id";
        if (mysqli_query($conn, $query)) {
            $_SESSION['msg'] = "Warehouse updated successfully!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }
}
header("Location: ../warehouses.php");
exit();
?>
