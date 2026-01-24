<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
    exit();
}
require_once __DIR__ . '/db/connect.php';

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
// Fetch products with category, supplier, and total stock
$prod_result = mysqli_query($conn, "
    SELECT p.*, c.category_name, s.supplier_name, COALESCE(SUM(st.quantity), 0) as total_stock 
    FROM product p 
    JOIN category c ON p.category_id = c.category_id 
    JOIN supplier s ON p.supplier_id = s.supplier_id 
    LEFT JOIN stock st ON p.product_id = st.product_id 
    GROUP BY p.product_id 
    ORDER BY p.product_id DESC
");
$products = [];
while ($row = mysqli_fetch_assoc($prod_result)) {
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/shared_cards.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
<div class="container">
    <?php include __DIR__ . '/components/sidebar.php'; ?>
    <?php include __DIR__ . '/components/toast_notifications.php'; ?>

    <div class="main-content">
        <div class="header">
            <div>
                <div class="header-title">Products</div>
                <div class="header-sub">Manage your product catalog</div>
            </div>
            <button class="add-btn" onclick="document.getElementById('addProductModal').style.display='flex'">Add Product</button>
        </div>

        <!-- Product Card Grid -->
        <div class="product-card-grid" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:25px;">
            <?php foreach ($products as $product) {
                include __DIR__ . '/components/product_card.php';
            } ?>
        </div>

        <!-- Add Product Modal -->
        <div id="addProductModal" class="modal-bg" style="display:none;">
            <div class="modal-content modal-content">
                <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Add Product</h2>
                <form method="POST" action="product/add.php">
                    <div class="modal-fields modal-fields">
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
                        <div class="modal-actions modal-actions">
                            <button type="button" class="modal-cancel modal-cancel" onclick="document.getElementById('addProductModal').style.display='none'">Cancel</button>
                            <button type="submit" class="add-btn add-btn">Add</button>
                        </div>
                    </div>
                </form>
                <button class="modal-close" onclick="document.getElementById('addProductModal').style.display='none'">&times;</button>
            </div>
        </div>

        <!-- Edit Product Modal -->
        <div id="editProductModal" class="modal-bg" style="display:none;">
            <div class="modal-content modal-content">
                <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Edit Product</h2>
                <form method="POST" action="product/edit.php">
                    <input type="hidden" name="product_id" id="edit_product_id">
                    <div class="modal-fields modal-fields">
                        <label class="modal-label">Product Name
                            <input type="text" name="product_name" id="edit_product_name" required>
                        </label>
                        <label class="modal-label">Unit Price
                            <input type="number" name="unit_price" id="edit_unit_price" step="0.01" required>
                        </label>
                        <label class="modal-label">Category
                            <select name="category_id" id="edit_category_id" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="modal-label">Supplier
                            <select name="supplier_id" id="edit_supplier_id" required>
                                <?php foreach ($suppliers as $sup): ?>
                                    <option value="<?= $sup['supplier_id'] ?>"><?= htmlspecialchars($sup['supplier_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <div class="modal-actions modal-actions">
                            <button type="button" class="modal-cancel modal-cancel" onclick="document.getElementById('editProductModal').style.display='none'">Cancel</button>
                            <button type="submit" class="add-btn add-btn">Update</button>
                        </div>
                    </div>
                </form>
                <button class="modal-close" onclick="document.getElementById('editProductModal').style.display='none'">&times;</button>
            </div>
        </div>

        </div>
</div>
<script>
function openEditModal(id, name, price, cat_id, sup_id) {
    document.getElementById('edit_product_id').value = id;
    document.getElementById('edit_product_name').value = name;
    document.getElementById('edit_unit_price').value = price;
    document.getElementById('edit_category_id').value = cat_id;
    document.getElementById('edit_supplier_id').value = sup_id;
    document.getElementById('editProductModal').style.display = 'block';
}
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        window.location.href = 'product/delete.php?id=' + id;
    }
}
</script>
</body>
</html>
