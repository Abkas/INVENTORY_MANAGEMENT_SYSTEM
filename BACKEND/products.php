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
    <title>Products</title>
    <link rel="stylesheet" href="css/products.css">
</head>
<body style="margin:0; padding:0;">
<?php include __DIR__ . '/comopnents/sidebar.php'; ?>
<div class="container" style="margin-left:220px;">
        <div class="header">
                <div>
                        <div class="header-title">Products</div>
                        <div class="header-sub">Manage your product catalog</div>
                </div>
                <button class="add-btn" onclick="document.getElementById('addProductModal').style.display='block'">Add Product</button>
        </div>


        <!-- Product Card Grid -->
        <div class="product-card-grid">
            <?php foreach ($products as $product) {
                include __DIR__ . '/components/product_card.php';
            } ?>
        </div>

        <!-- Add Product Modal -->
        <div id="addProductModal" class="modal-bg">
            <div class="modal-content modal-content-spacious">
                <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Add Product</h2>
                <form method="POST" action="products.php">
                    <div class="modal-fields modal-fields-spacious">
                        <label class="modal-label">Product Name
                            <input type="text" name="product_name" placeholder="Enter product name" required>
                        </label>
                        <label class="modal-label">Unit Price
                            <input type="number" name="unit_price" placeholder="Enter price" step="0.01" required>
                        </label>
                        <label class="modal-label">Category
                            <select name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="modal-label">Supplier
                            <select name="supplier_id" required>
                                <option value="">Select Supplier</option>
                                <?php foreach ($suppliers as $sup): ?>
                                    <option value="<?= $sup['supplier_id'] ?>"><?= htmlspecialchars($sup['supplier_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <div class="modal-actions modal-actions-spacious">
                            <button type="button" class="modal-cancel modal-cancel-spacious" onclick="document.getElementById('addProductModal').style.display='none'">Cancel</button>
                            <button type="submit" class="add-btn add-btn-spacious">Add</button>
                        </div>
                    </div>
                </form>
                <button class="modal-close" onclick="document.getElementById('addProductModal').style.display='none'">&times;</button>
            </div>
        </div>

</div>
</body>
</html>
