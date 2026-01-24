<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $product_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM product WHERE category_id = $id");
    $product_row = mysqli_fetch_assoc($product_check);
    
    if ($product_row['count'] > 0) {
        $_SESSION['error'] = "Cannot delete this category! It has {$product_row['count']} product(s). Please reassign or delete those products first.";
    } else {
        $query = "DELETE FROM category WHERE category_id = $id";
        if (mysqli_query($conn, $query)) {
            $_SESSION['msg'] = "Category deleted successfully!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }
}

header("Location: ../categories.php");
exit();
?>
