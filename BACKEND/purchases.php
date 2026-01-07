<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/FRONTEND/pages/login.html");
    exit();
}
require_once __DIR__ . '/db/connect.php';
// Handle add purchase
// Handle add purchase and update stock
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_date'], $_POST['quantity'], $_POST['total_price'], $_POST['product_id'], $_POST['supplier_id'])) {
   
    $purchase_date = trim($_POST['purchase_date']);
    $quantity = intval($_POST['quantity']);
    $total_price = trim($_POST['total_price']);
    $product_id = intval($_POST['product_id']);
    $supplier_id = intval($_POST['supplier_id']);
    $user_id = $_SESSION['user_id'];

    if ($purchase_date !== '' && $quantity > 0 && $total_price !== '' && $product_id > 0 && $supplier_id > 0) {
        // Insert purchase
        mysqli_query(
            $conn, 
            "INSERT INTO purchase (purchase_date, quantity, total_price, product_id, supplier_id, user_id) 
            VALUES ('$purchase_date', '$quantity', '$total_price', '$product_id', '$supplier_id', '$user_id')"
            );

        // Add to stock (add to first warehouse, or create if not exists)
        $warehouse_result = mysqli_query(
            $conn, 
            "SELECT warehouse_id 
            FROM warehouse 
            ORDER BY warehouse_id ASC LIMIT 1"
            );

        $warehouse_row = mysqli_fetch_assoc($warehouse_result);

        $warehouse_id = $warehouse_row ? intval($warehouse_row['warehouse_id']) : null;

        if ($warehouse_id) {
            // Check if stock row exists for this product+warehouse
            $stock_result = mysqli_query(
                $conn, 
                "SELECT * FROM stock 
                WHERE product_id = $product_id 
                AND warehouse_id = $warehouse_id"
                );
                
            if ($stock_row = mysqli_fetch_assoc($stock_result)) {
                $new_qty = $stock_row['quantity'] + $quantity;
                mysqli_query($conn, "UPDATE stock SET quantity = $new_qty WHERE stock_id = " . $stock_row['stock_id']);
            } else {
                mysqli_query($conn, "INSERT INTO stock (product_id, warehouse_id, quantity, user_id) VALUES ($product_id, $warehouse_id, $quantity, $user_id)");
            }
        }
        header("Location: purchases.php");
        exit();
    }
}
// Fetch products
$prod_result = mysqli_query(
    $conn, 
    "SELECT * FROM product 
    ORDER BY product_name ASC"
    );

if (!$prod_result) { die('Product query error: ' . mysqli_error($conn)); }
$products = [];

while ($row = mysqli_fetch_assoc($prod_result)) {
    $products[] = $row;
}
// Fetch suppliers
$sup_result = mysqli_query(
    $conn, 
    "SELECT * FROM supplier 
    ORDER BY supplier_name ASC"
    );

if (!$sup_result) { die('Supplier query error: ' . mysqli_error($conn)); }
$suppliers = [];
while ($row = mysqli_fetch_assoc($sup_result)) {
    $suppliers[] = $row;
}
// Fetch purchases
$pur_result = mysqli_query(
    $conn, 
    "SELECT p.*, pr.product_name 
    FROM purchase p JOIN product pr ON p.product_id = pr.product_id 
    ORDER BY p.purchase_id DESC");

if (!$pur_result) { die('Purchase query error: ' . mysqli_error($conn)); }
$purchases = [];
while ($row = mysqli_fetch_assoc($pur_result)) {
    $purchases[] = $row;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Purchases</title>
    <style>
        body { background: #fff; color: #111; font-family: Arial, sans-serif; }
        .container { max-width: 800px; margin: 2.5rem auto; padding: 2rem; border: 1px solid #ddd; border-radius: 8px; }
        h2 { margin-top: 0; }
        form { margin-bottom: 2rem; display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 1rem; }
        input[type="text"], input[type="number"], input[type="date"], select { padding: 0.5rem; border: 1px solid #bbb; border-radius: 4px; }
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
        <h2>Purchases</h2>
        <a href="/INVENTORY_SYSTEM/BACKEND/index.php"><button>Dashboard</button></a>
    </div>
    <?php if (!empty($error)): ?>
        <div style="color:red; margin-bottom:1rem; font-weight:bold;"> <?php echo $error; ?> </div>
    <?php endif; ?>
    <form method="POST" action="purchases.php">
        <input type="date" name="purchase_date" required>
        <input type="number" name="quantity" placeholder="Quantity" required>
        <input type="number" name="total_price" placeholder="Total Price" step="0.01" required>
        <select name="product_id" required>
            <option value="">Product</option>
            <?php foreach ($products as $prod): ?>
                <option value="<?php echo $prod['product_id']; ?>"><?php echo htmlspecialchars($prod['product_name']); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="supplier_id" required>
            <option value="">Supplier</option>
            <?php foreach ($suppliers as $sup): ?>
                <option value="<?php echo $sup['supplier_id']; ?>"><?php echo htmlspecialchars($sup['supplier_name']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Add</button>
    </form>
    <table>
        <tr><th>ID</th><th>Date</th><th>Quantity</th><th>Total Price</th><th>Product</th></tr>
        <?php foreach ($purchases as $pur): ?>
            <tr>
                <td><?php echo $pur['purchase_id']; ?></td>
                <td><?php echo $pur['purchase_date']; ?></td>
                <td><?php echo $pur['quantity']; ?></td>
                <td><?php echo $pur['total_price']; ?></td>
                <td><?php echo htmlspecialchars($pur['product_name']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
