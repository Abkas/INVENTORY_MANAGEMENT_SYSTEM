<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $query = "DELETE FROM supplier WHERE supplier_id = $id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['msg'] = "Supplier deleted successfully!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
}
header("Location: ../suppliers.php");
exit();
?>
