<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $sales_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM sales WHERE customer_id = $id");
    $sales_row = mysqli_fetch_assoc($sales_check);
    
    if ($sales_row['count'] > 0) {
        $_SESSION['error'] = "Cannot delete this customer! They have {$sales_row['count']} sales record(s). Historical data must be preserved.";
    } else {
        $query = "DELETE FROM customer WHERE customer_id = $id";
        if (mysqli_query($conn, $query)) {
            $_SESSION['msg'] = "Customer deleted successfully!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }
}
header("Location: ../customers.php");
exit();
?>
