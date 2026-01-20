<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    $id = (int)$_POST['category_id'];
    $name = mysqli_real_escape_string($conn, trim($_POST['category_name']));

    if ($name !== '') {
        $query = "UPDATE category SET category_name = '$name' WHERE category_id = $id";
        if (mysqli_query($conn, $query)) {
            $_SESSION['msg'] = "Category updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating category: " . mysqli_error($conn);
        }
    }
}

header("Location: ../categories.php");
exit();
?>
