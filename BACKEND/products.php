<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/FRONTEND/pages/login.html");
    exit();
}
require_once __DIR__ . '/db/connect.php';

// Handle add product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_name'], $_POST['unit_price'], $_POST['category_id'], $_POST['supplier_id'])) {
    $product_name = trim($_POST['product_name']);
    $unit_price = trim($_POST['unit_price']);
    $category_id = intval($_POST['category_id']);
    $supplier_id = intval($_POST['supplier_id']);
    if ($product_name !== '' && $unit_price !== '' && $category_id > 0 && $supplier_id > 0) {
        mysqli_query($conn, "INSERT INTO product (product_name, unit_price, category_id, supplier_id) VALUES ('$product_name', '$unit_price', '$category_id', '$supplier_id')");
        header("Location: products.php");
        exit();
    }
}
// Fetch categories
$cat_result = mysqli_query($conn, "SELECT * FROM category ORDER BY category_name ASC");
$categories = [];
while ($row = mysqli_fetch_assoc($cat_result)) {
    $categories[] = $row;
}
// Fetch suppliers
$sup_result = mysqli_query($conn, "SELECT * FROM supplier ORDER BY supplier_name ASC");
$suppliers = [];
while ($row = mysqli_fetch_assoc($sup_result)) {
    $suppliers[] = $row;
}
// Fetch products with category and supplier
$prod_result = mysqli_query($conn, "SELECT p.*, c.category_name, s.supplier_name FROM product p JOIN category c ON p.category_id = c.category_id JOIN supplier s ON p.supplier_id = s.supplier_id ORDER BY p.product_id DESC");
$products = [];
while ($row = mysqli_fetch_assoc($prod_result)) {
    $products[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
    <style>
        body { background: #fff; color: #111; font-family: Arial, sans-serif; }
        .container { max-width: 800px; margin: 2.5rem auto; padding: 2rem; border: 1px solid #ddd; border-radius: 8px; }
        h2 { margin-top: 0; }
        form { margin-bottom: 2rem; display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 1rem; }
        input[type="text"], input[type="number"], select { padding: 0.5rem; border: 1px solid #bbb; border-radius: 4px; }
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
        <h2>Products</h2>
        <a href="/INVENTORY_SYSTEM/BACKEND/index.php"><button>Dashboard</button></a>
    </div>
    <form method="POST" action="products.php">
        <input type="text" name="product_name" placeholder="Product name" required>
        <input type="number" name="unit_price" placeholder="Unit price" step="0.01" required>
        <select name="category_id" required>
            <option value="">Category</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
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
        <tr><th>ID</th><th>Name</th><th>Price</th><th>Category</th><th>Supplier</th></tr>
        <?php foreach ($products as $prod): ?>
            <tr>
                <td><?php echo $prod['product_id']; ?></td>
                <td><?php echo htmlspecialchars($prod['product_name']); ?></td>
                <td><?php echo $prod['unit_price']; ?></td>
                <td><?php echo htmlspecialchars($prod['category_name']); ?></td>
                <td><?php echo htmlspecialchars($prod['supplier_name']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
