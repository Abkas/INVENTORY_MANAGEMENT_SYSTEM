<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/FRONTEND/pages/login.html");
    exit();
}
require_once __DIR__ . '/db/connect.php';
$error = '';

// Handle add purchase and update stock
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $purchase_date = trim($_POST['purchase_date'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 0);
    $total_price = trim($_POST['total_price'] ?? '');
    $product_id = intval($_POST['product_id'] ?? 0);
    $user_id = $_SESSION['user_id'] ?? 1;

    if ($purchase_date && $quantity > 0 && $total_price !== '' && $product_id > 0) {
        $stmt = $conn->prepare("INSERT INTO purchase (purchase_date, quantity, total_price, product_id, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sidii', $purchase_date, $quantity, $total_price, $product_id, $user_id);
        $stmt->execute();
        $stmt->close();

        // Add to stock (add to first warehouse, or create if not exists)
        $warehouse_result = mysqli_query($conn, "SELECT warehouse_id FROM warehouse ORDER BY warehouse_id ASC LIMIT 1");
        $warehouse_row = mysqli_fetch_assoc($warehouse_result);
        $warehouse_id = $warehouse_row ? intval($warehouse_row['warehouse_id']) : null;
        if ($warehouse_id) {
            $stock_result = mysqli_query($conn, "SELECT * FROM stock WHERE product_id = $product_id AND warehouse_id = $warehouse_id");
            if ($stock_row = mysqli_fetch_assoc($stock_result)) {
                $new_qty = $stock_row['quantity'] + $quantity;
                mysqli_query($conn, "UPDATE stock SET quantity = $new_qty WHERE stock_id = " . $stock_row['stock_id']);
            } else {
                mysqli_query($conn, "INSERT INTO stock (product_id, warehouse_id, quantity, user_id) VALUES ($product_id, $warehouse_id, $quantity, $user_id)");
            }
        }
        header("Location: purchases.php");
        exit();
    } else {
        $error = 'Please fill in all fields correctly.';
    }
}

// Fetch products
$products = [];
$prod_result = mysqli_query($conn, "SELECT * FROM product ORDER BY product_name ASC");
while ($row = mysqli_fetch_assoc($prod_result)) {
    $products[] = $row;
}
// Fetch suppliers
$suppliers = [];
$sup_result = mysqli_query($conn, "SELECT * FROM supplier ORDER BY supplier_name ASC");
while ($row = mysqli_fetch_assoc($sup_result)) {
    $suppliers[] = $row;
}
// Fetch purchases (with product and supplier name via product table)
$purchases = [];
$pur_sql = "SELECT p.*, pr.product_name, s.supplier_name FROM purchase p ".
    "JOIN product pr ON p.product_id = pr.product_id ".
    "LEFT JOIN supplier s ON pr.supplier_id = s.supplier_id ".
    "ORDER BY p.purchase_id DESC";
$pur_result = mysqli_query($conn, $pur_sql);
if ($pur_result === false) {
    die('Purchase query error: ' . mysqli_error($conn) . "<br>SQL: " . htmlspecialchars($pur_sql));
}
while ($row = mysqli_fetch_assoc($pur_result)) {
    $purchases[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchases</title>
    <link rel="stylesheet" href="css/purchases.css">
    <link rel="stylesheet" href="css/purchase_card.css">
</head>
<body>
<div class="container">
    <?php include __DIR__ . '/components/sidebar.php'; ?>
    <div class="header">
        <div>
            <div class="header-title">Purchases</div>
            <div class="header-sub">Manage your purchase records</div>
        </div>
        <button class="add-btn" onclick="document.getElementById('addPurchaseModal').style.display='block'">Add Purchase</button>
    </div>

    <?php if (!empty($error)): ?>
        <div style="color:red; margin-bottom:1rem; font-weight:bold;"> <?php echo $error; ?> </div>
    <?php endif; ?>

    <!-- Main Content Wrapper -->
    <div class="main-content">
        <div class="purchase-card-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;">
            <?php foreach ($purchases as $purchase): ?>
                <?php include __DIR__ . '/components/purchase_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add Purchase Modal -->
    <div id="addPurchaseModal" class="modal-bg">
        <div class="modal-content modal-content-spacious">
            <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Add Purchase</h2>
            <form method="POST" action="purchases.php">
                <div class="modal-fields modal-fields-spacious">
                    <label class="modal-label">Date
                        <input type="date" name="purchase_date" placeholder="Date" required>
                    </label>
                    <label class="modal-label">Quantity
                        <input type="number" name="quantity" placeholder="Quantity" required>
                    </label>
                    <label class="modal-label">Total Price
                        <input type="number" name="total_price" placeholder="Total Price" step="0.01" required>
                    </label>
                    <label class="modal-label">Product
                        <select name="product_id" required>
                            <option value="">Product</option>
                            <?php foreach ($products as $prod): ?>
                                <option value="<?= $prod['product_id'] ?>"><?= htmlspecialchars($prod['product_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <div class="modal-actions modal-actions-spacious">
                        <button type="button" class="modal-cancel modal-cancel-spacious" onclick="document.getElementById('addPurchaseModal').style.display='none'">Cancel</button>
                        <button type="submit" class="add-btn add-btn-spacious">Add</button>
                    </div>
                </div>
            </form>
            <button class="modal-close" onclick="document.getElementById('addPurchaseModal').style.display='none'">&times;</button>
        </div>
    </div>
</div>
</body>
</html>
