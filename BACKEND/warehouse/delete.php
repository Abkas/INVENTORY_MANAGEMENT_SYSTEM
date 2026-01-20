<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Check if warehouse has stock before deleting
    $check_stock = mysqli_query($conn, "SELECT COUNT(*) as count FROM stock WHERE warehouse_id = $id");
    $row = mysqli_fetch_assoc($check_stock);
    
    if ($row['count'] > 0) {
        $_SESSION['error'] = "Cannot delete warehouse. It still contains stock!";
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
