<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $stock_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM stock WHERE warehouse_id = $id");
    $stock_row = mysqli_fetch_assoc($stock_check);
    
    if ($stock_row['count'] > 0) {
        $_SESSION['error'] = "Cannot delete this warehouse! It has {$stock_row['count']} stock record(s). Please move or clear the stock first.";
    } else {
        $query = "DELETE FROM warehouse WHERE warehouse_id = $id";
        if (mysqli_query($conn, $query)) {
            $_SESSION['msg'] = "Warehouse deleted successfully!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }
}
header("Location: ../warehouses.php");
exit();
?>
