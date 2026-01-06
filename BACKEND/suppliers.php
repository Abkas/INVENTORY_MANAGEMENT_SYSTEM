<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/FRONTEND/pages/login.html");
    exit();
}
require_once __DIR__ . '/db/connect.php';

// Handle add supplier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supplier_name'])) {
    $supplier_name = trim($_POST['supplier_name']);
    $supplier_email = trim($_POST['supplier_email']);
    $supplier_phone = trim($_POST['supplier_phone']);
    if ($supplier_name !== '') {
        mysqli_query($conn, "INSERT INTO supplier (supplier_name, supplier_email, supplier_phone) VALUES ('$supplier_name', '$supplier_email', '$supplier_phone')");
        header("Location: suppliers.php");
        exit();
    }
}
// Fetch suppliers
$result = mysqli_query($conn, "SELECT * FROM supplier ORDER BY supplier_id DESC");
$suppliers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $suppliers[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Suppliers</title>
    <style>
        body { background: #fff; color: #111; font-family: Arial, sans-serif; }
        .container { max-width: 700px; margin: 2.5rem auto; padding: 2rem; border: 1px solid #ddd; border-radius: 8px; }
        h2 { margin-top: 0; }
        form { margin-bottom: 2rem; display: grid; grid-template-columns: 2fr 2fr 2fr auto; gap: 1rem; }
        input[type="text"] { padding: 0.5rem; border: 1px solid #bbb; border-radius: 4px; }
        button { padding: 0.5rem 1.2rem; border: none; background: #111; color: #fff; border-radius: 4px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.7rem; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f4f4f4; }
        .top-bar { display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem; }
    </style>
</head>
<body>
<div class="container">
    <div class="top-bar">
        <h2>Suppliers</h2>
        <a href="/INVENTORY_SYSTEM/BACKEND/index.php"><button>Dashboard</button></a>
    </div>
    <form method="POST" action="suppliers.php">
        <input type="text" name="supplier_name" placeholder="Supplier name" required>
        <input type="text" name="supplier_email" placeholder="Email">
        <input type="text" name="supplier_phone" placeholder="Phone">
        <button type="submit">Add</button>
    </form>
    <table>
        <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th></tr>
        <?php foreach ($suppliers as $sup): ?>
            <tr>
                <td><?php echo $sup['supplier_id']; ?></td>
                <td><?php echo htmlspecialchars($sup['supplier_name']); ?></td>
                <td><?php echo htmlspecialchars($sup['supplier_email']); ?></td>
                <td><?php echo htmlspecialchars($sup['supplier_phone']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
