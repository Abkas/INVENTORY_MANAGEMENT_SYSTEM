<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Check if supplier has any products
    $product_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM product WHERE supplier_id = $id");
    $product_row = mysqli_fetch_assoc($product_check);
    
    // Check if supplier has any purchase history
    $purchase_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM purchase WHERE supplier_id = $id");
    $purchase_row = mysqli_fetch_assoc($purchase_check);
    
    if ($product_row['count'] > 0 || $purchase_row['count'] > 0) {
        $_SESSION['error'] = "Cannot delete this supplier! It has {$product_row['count']} product(s) and {$purchase_row['count']} purchase record(s).";
    } else {
        $query = "DELETE FROM supplier WHERE supplier_id = $id";
        if (mysqli_query($conn, $query)) {
            $_SESSION['msg'] = "Supplier deleted successfully!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }
}
header("Location: ../suppliers.php");
exit();
?>
