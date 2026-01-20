<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock</title>
    <link rel="stylesheet" href="css/stock.css">
    <link rel="stylesheet" href="css/stock_card.css">
</head>
<body>
<div class="container">
    <?php include __DIR__ . '/components/sidebar.php'; ?>
    <div class="header">
        <div>
            <div class="header-title">Stock</div>
            <div class="header-sub">Manage your stock records</div>
        </div>
        <button class="add-btn" onclick="document.getElementById('addStockModal').style.display='block'">Add Stock</button>
    </div>

    <!-- Main Content Wrapper -->
    <div class="main-content">
        <div class="stock-card-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;">
            <?php foreach ($stocks as $stock): ?>
                <?php include __DIR__ . '/components/stock_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add Stock Modal -->
    <div id="addStockModal" class="modal-bg">
        <div class="modal-content modal-content-spacious">
            <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Add Stock</h2>
            <form method="POST" action="stock.php">
                <div class="modal-fields modal-fields-spacious">
                    <label class="modal-label">Product
                        <select name="product_id" required>
                            <option value="">Product</option>
                            <?php foreach ($products as $prod): ?>
                                <option value="<?php echo $prod['product_id']; ?>"><?php echo htmlspecialchars($prod['product_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="modal-label">Warehouse
                        <select name="warehouse_id" required>
                            <option value="">Warehouse</option>
                            <?php foreach ($warehouses as $wh): ?>
                                <option value="<?php echo $wh['warehouse_id']; ?>"><?php echo htmlspecialchars($wh['warehouse_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="modal-label">Quantity
                        <input type="number" name="quantity" placeholder="Quantity" required>
                    </label>
                    <div class="modal-actions modal-actions-spacious">
                        <button type="button" class="modal-cancel modal-cancel-spacious" onclick="document.getElementById('addStockModal').style.display='none'">Cancel</button>
                        <button type="submit" class="add-btn add-btn-spacious">Add</button>
                    </div>
                </div>
            </form>
            <button class="modal-close" onclick="document.getElementById('addStockModal').style.display='none'">&times;</button>
        </div>
    </div>
</div>
</body>
</html>
