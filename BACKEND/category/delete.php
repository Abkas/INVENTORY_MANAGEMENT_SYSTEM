<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $query = "DELETE FROM category WHERE category_id = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['msg'] = "Category deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting category: " . mysqli_error($conn);
    }
}

header("Location: ../categories.php");
exit();
?>
