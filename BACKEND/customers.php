<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/FRONTEND/pages/login.html");
    exit();
}
require_once __DIR__ . '/db/connect.php';
// Handle add customer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_name'])) {
    $customer_name = trim($_POST['customer_name']);
    $customer_email = trim($_POST['customer_email']);
    $customer_phone = trim($_POST['customer_phone']);
    if ($customer_name !== '') {
        mysqli_query($conn, "INSERT INTO customer (customer_name, customer_email, customer_phone) VALUES ('$customer_name', '$customer_email', '$customer_phone')");
        header("Location: customers.php");
        exit();
    }
}
$result = mysqli_query($conn, "SELECT * FROM customer ORDER BY customer_id DESC");
$customers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $customers[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Customers</title>
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
<body style="margin:0; padding:0;">
<?php include __DIR__ . '/comopnents/sidebar.php'; ?>
<div class="container" style="margin-left:220px;">
    <div class="top-bar">
        <h2>Customers</h2>
        <a href="/INVENTORY_SYSTEM/BACKEND/index.php"><button>Dashboard</button></a>
    </div>
    <form method="POST" action="customers.php">
        <input type="text" name="customer_name" placeholder="Customer name" required>
        <input type="text" name="customer_email" placeholder="Email">
        <input type="text" name="customer_phone" placeholder="Phone">
        <button type="submit">Add</button>
    </form>
    <table>
        <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th></tr>
        <?php foreach ($customers as $cus): ?>
            <tr>
                <td><?php echo $cus['customer_id']; ?></td>
                <td><?php echo htmlspecialchars($cus['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($cus['customer_email']); ?></td>
                <td><?php echo htmlspecialchars($cus['customer_phone']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
