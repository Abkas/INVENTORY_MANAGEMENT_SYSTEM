<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Check if product has any current stock
    $stock_check = mysqli_query($conn, "SELECT SUM(quantity) as total_stock FROM stock WHERE product_id = $id");
    $stock_row = mysqli_fetch_assoc($stock_check);
    $total_stock = $stock_row['total_stock'] ?? 0;
    
    if ($total_stock > 0) {
        $_SESSION['error'] = "Cannot delete this product! It has {$total_stock} units in stock. Please clear the stock first.";
    } else {
        // Safe to delete - no current stock (historical sales/purchase records will be preserved)
        $query = "DELETE FROM product WHERE product_id = $id";
        if (mysqli_query($conn, $query)) {
            $_SESSION['msg'] = "Product deleted successfully! Historical records have been preserved.";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }
}
header("Location: ../products.php");
exit();
?>
