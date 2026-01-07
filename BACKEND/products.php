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
    <style>
        body { background: #f8fafc; color: #18181b; font-family: Arial, sans-serif; margin:0; padding:0; }
        .container { max-width: 900px; margin: 2.5rem auto; padding: 2.5rem 2rem; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; box-shadow: 0 2px 8px #0001; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; }
        .header-title { font-size: 2rem; font-weight: bold; margin: 0; }
        .header-sub { color: #555; font-size: 1rem; margin-top: 0.3rem; }
        .add-btn { background: #6366f1; color: #fff; border: none; border-radius: 5px; padding: 0.6rem 1.5rem; font-size: 1rem; font-weight: 500; cursor: pointer; transition: background 0.2s; }
        .add-btn:hover { background: #4f46e5; }
        table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; background: #fff; }
        th, td { padding: 0.9rem 0.7rem; border-bottom: 1px solid #f1f1f1; text-align: left; }
        th { background: #f3f4f6; font-weight: 600; color: #222; }
        tr:last-child td { border-bottom: none; }
        .action-btns { display: flex; gap: 0.5rem; }
        .action-btn { border: none; background: #f3f4f6; color: #444; border-radius: 4px; padding: 0.3rem 0.8rem; font-size: 0.95rem; cursor: pointer; transition: background 0.2s; }
        .action-btn:hover { background: #6366f1; color: #fff; }
    </style>
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
        <!-- Add Product Modal -->
        <div id="addProductModal" class="modal-bg">
            <div class="modal-content">
                <h2>Add Product</h2>
                <form method="POST" action="products.php">
                    <div class="modal-fields">
                        <input type="text" name="product_name" placeholder="Product Name" required>
                        <input type="number" name="unit_price" placeholder="Unit Price" step="0.01" required>
                        <select name="category_id" required>
                            <option value="">Select Category</option>
                            <?php
                            $cat_result = mysqli_query($conn, "SELECT * FROM category ORDER BY category_name ASC");
                            while ($cat = mysqli_fetch_assoc($cat_result)):
                            ?>
                                <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <select name="supplier_id" required>
                            <option value="">Select Supplier</option>
                            <?php
                            $sup_result = mysqli_query($conn, "SELECT * FROM supplier ORDER BY supplier_name ASC");
                            while ($sup = mysqli_fetch_assoc($sup_result)):
                            ?>
                                <option value="<?= $sup['supplier_id'] ?>"><?= htmlspecialchars($sup['supplier_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="modal-actions">
                            <button type="button" class="modal-cancel" onclick="document.getElementById('addProductModal').style.display='none'">Cancel</button>
                            <button type="submit" class="add-btn">Add</button>
                        </div>
                    </div>
                </form>
                <button class="modal-close" onclick="document.getElementById('addProductModal').style.display='none'">&times;</button>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="product-table">
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Supplier</th>
                    <th>Actions</th>
                </tr>
                <?php
                $prod_result = mysqli_query($conn, "SELECT p.*, c.category_name, s.supplier_name FROM product p JOIN category c ON p.category_id = c.category_id JOIN supplier s ON p.supplier_id = s.supplier_id ORDER BY p.product_id DESC");
                while ($prod = mysqli_fetch_assoc($prod_result)):
                ?>
                <tr>
                    <td><?= htmlspecialchars($prod['product_name']) ?></td>
                    <td>$<?= number_format($prod['unit_price'], 2) ?></td>
                    <td><?= htmlspecialchars($prod['category_name']) ?></td>
                    <td><?= htmlspecialchars($prod['supplier_name']) ?></td>
                    <td class="action-btns">
                        <button class="action-btn" onclick="alert('Viewing: <?= htmlspecialchars($prod['product_name']) ?>')">View</button>
                        <button class="action-btn" onclick="alert('Editing: <?= htmlspecialchars($prod['product_name']) ?>')">Edit</button>
                        <button class="action-btn" onclick="if(confirm('Delete <?= htmlspecialchars($prod['product_name']) ?>?')){ alert('Would delete: <?= htmlspecialchars($prod['product_name']) ?>') }" style="background:#fee2e2;color:#b91c1c;">Delete</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
</div>
<style>
@media (max-width: 1100px) {
    .container { max-width: 100vw; padding: 1rem; }
}
@media (max-width: 700px) {
    .container { margin-left: 0 !important; padding: 0.5rem; }
    .header { flex-direction: column; align-items: flex-start; gap: 1rem; }
    .product-table th, .product-table td { padding: 0.5rem 0.3rem; font-size: 0.98rem; }
    .modal-content { max-width: 98vw !important; padding: 1.2rem !important; }
}
.modal-bg {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0; top: 0; width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.25);
}
.modal-bg[style*='display:block'] { display: block; }
.modal-content {
    background: #fff;
    max-width: 420px;
    margin: 6vh auto;
    padding: 2rem 2rem 1.5rem 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 16px #0002;
    position: relative;
}
.modal-fields { display: flex; flex-direction: column; gap: 1rem; }
.modal-actions { display: flex; gap: 1rem; justify-content: flex-end; }
.modal-cancel { background: #eee; color: #333; padding: 0.5rem 1.2rem; border: none; border-radius: 4px; }
.modal-close { position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 1.3rem; color: #888; cursor: pointer; }
.product-table { width: 100%; border-collapse: collapse; background: #fff; }
.product-table th, .product-table td { padding: 0.9rem 0.7rem; border-bottom: 1px solid #f1f1f1; text-align: left; }
.product-table th { background: #f3f4f6; font-weight: 600; color: #222; }
.product-table tr:last-child td { border-bottom: none; }
.action-btns { display: flex; gap: 0.5rem; }
.action-btn { border: none; background: #f3f4f6; color: #444; border-radius: 4px; padding: 0.3rem 0.8rem; font-size: 0.95rem; cursor: pointer; transition: background 0.2s; }
.action-btn:hover { background: #6366f1; color: #fff; }
.add-btn { background: #6366f1; color: #fff; border: none; border-radius: 5px; padding: 0.6rem 1.5rem; font-size: 1rem; font-weight: 500; cursor: pointer; transition: background 0.2s; }
.add-btn:hover { background: #4f46e5; }
</style>
</div>
</body>
</html>
