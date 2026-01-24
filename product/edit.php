<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $id = (int)$_POST['product_id'];
    $name = mysqli_real_escape_string($conn, trim($_POST['product_name']));
    $price = mysqli_real_escape_string($conn, $_POST['unit_price']);
    $cat_id = intval($_POST['category_id']);
    $sup_id = intval($_POST['supplier_id']);

    if ($name !== '' && $price !== '' && $cat_id > 0 && $sup_id > 0) {
        $query = "UPDATE product SET product_name = '$name', unit_price = '$price', category_id = $cat_id, supplier_id = $sup_id WHERE product_id = $id";
        if (mysqli_query($conn, $query)) {
            $_SESSION['msg'] = "Product updated successfully!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }
}
header("Location: ../products.php");
exit();
?>
