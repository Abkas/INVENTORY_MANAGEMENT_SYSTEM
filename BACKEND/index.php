<?php
session_start();

// Protection logic
if (!isset($_SESSION['user'])) {
    header("Location: ../frontend/login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
</head>
<body>

<h2>Welcome, <?php echo $_SESSION['user']; ?></h2>

<p>This is the home page of the Inventory Management System.</p>

<ul>
    <li>View Products</li>
    <li>Add Products</li>
    <li>Manage Stock</li>
</ul>

<a href="logout.php">Logout</a>

</body>
</html>
