<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/FRONTEND/pages/login.html");
    exit();
}
require_once __DIR__ . '/db/connect.php';
// Handle add stock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['warehouse_id'], $_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $warehouse_id = intval($_POST['warehouse_id']);
    $quantity = intval($_POST['quantity']);
    $user_id = $_SESSION['user_id'];
    if ($product_id > 0 && $warehouse_id > 0 && $quantity > 0) {
        mysqli_query($conn, "INSERT INTO stock (product_id, warehouse_id, quantity, user_id) VALUES ('$product_id', '$warehouse_id', '$quantity', '$user_id')");
        header("Location: stock.php");
        exit();
    }
}
// Fetch products
$prod_result = mysqli_query($conn, "SELECT * FROM product ORDER BY product_name ASC");
$products = [];
while ($row = mysqli_fetch_assoc($prod_result)) {
    $products[] = $row;
}
// Fetch warehouses
$wh_result = mysqli_query($conn, "SELECT * FROM warehouse ORDER BY warehouse_name ASC");
$warehouses = [];
while ($row = mysqli_fetch_assoc($wh_result)) {
    $warehouses[] = $row;
}
// Fetch stock
$stock_result = mysqli_query($conn, "SELECT s.*, p.product_name, w.warehouse_name FROM stock s JOIN product p ON s.product_id = p.product_id JOIN warehouse w ON s.warehouse_id = w.warehouse_id ORDER BY s.stock_id DESC");
$stocks = [];
while ($row = mysqli_fetch_assoc($stock_result)) {
    $stocks[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Stock</title>
    <style>
        body { background: #fff; color: #111; font-family: Arial, sans-serif; }
        .container { max-width: 900px; margin: 2.5rem auto; padding: 2rem; border: 1px solid #ddd; border-radius: 8px; }
        h2 { margin-top: 0; }
        form { margin-bottom: 2rem; display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem; }
        input[type="number"], select { padding: 0.5rem; border: 1px solid #bbb; border-radius: 4px; }
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
        <h2>Stock</h2>
        <a href="/INVENTORY_SYSTEM/BACKEND/index.php"><button>Dashboard</button></a>
    </div>
    <form method="POST" action="stock.php">
        <select name="product_id" required>
            <option value="">Product</option>
            <?php foreach ($products as $prod): ?>
                <option value="<?php echo $prod['product_id']; ?>"><?php echo htmlspecialchars($prod['product_name']); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="warehouse_id" required>
            <option value="">Warehouse</option>
            <?php foreach ($warehouses as $wh): ?>
                <option value="<?php echo $wh['warehouse_id']; ?>"><?php echo htmlspecialchars($wh['warehouse_name']); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="quantity" placeholder="Quantity" required>
        <button type="submit">Add</button>
    </form>
    <table>
        <tr><th>ID</th><th>Product</th><th>Warehouse</th><th>Quantity</th></tr>
        <?php foreach ($stocks as $stock): ?>
            <tr>
                <td><?php echo $stock['stock_id']; ?></td>
                <td><?php echo htmlspecialchars($stock['product_name']); ?></td>
                <td><?php echo htmlspecialchars($stock['warehouse_name']); ?></td>
                <td><?php echo $stock['quantity']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
