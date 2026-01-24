<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {
    $category_name = mysqli_real_escape_string($conn, trim($_POST['category_name']));
    
    if ($category_name !== '') {
        $query = "INSERT INTO category (category_name) VALUES ('$category_name')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['msg'] = "Category added successfully!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }
}
header("Location: ../categories.php");
exit();
?>
