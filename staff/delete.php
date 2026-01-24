<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../user/login.php");
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php?error=Access Denied");
    exit();
}

require_once __DIR__ . '/../db/connect.php';

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    if ($user_id === $_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot delete your own account!";
        header("Location: ../staff.php");
        exit();
    }
    
    $check = mysqli_query($conn, "SELECT * FROM user WHERE user_id = $user_id");
    if (mysqli_num_rows($check) === 0) {
        $_SESSION['error'] = "User not found!";
        header("Location: ../staff.php");
        exit();
    }
    
    $result = mysqli_query($conn, "DELETE FROM user WHERE user_id = $user_id");
    
    if ($result) {
        $_SESSION['msg'] = "Staff member deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete staff member: " . mysqli_error($conn);
    }
}

header("Location: ../staff.php");
exit();
?>
