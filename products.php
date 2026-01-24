<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: user/login.php");
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
    <link rel="stylesheet" href="css/global.css?v=<?= time() ?>">
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
            <div style="display:flex; gap:16px; align-items: center;">
                <div class="segment-group">
                    <button class="segment-btn active" onclick="toggleView('card')" id="btn-card" title="Grid View">
                        <i data-lucide="layout-grid" style="width:18px;"></i>
                    </button>
                    <div style="width:1px; background:#e2e8f0; margin:4px 0;"></div>
                    <button class="segment-btn" onclick="toggleView('table')" id="btn-table" title="Table View">
                        <i data-lucide="table" style="width:18px;"></i>
                    </button>
                </div>
                <button class="add-btn" onclick="document.getElementById('addProductModal').style.display='flex'">Add Product</button>
            </div>
        </div>

        <!-- Product Card Grid -->
        <div id="view-card" class="product-card-grid responsive-grid">
            <?php foreach ($products as $product) {
                include __DIR__ . '/components/product_card.php';
            } ?>
        </div>

        <!-- Table View -->
        <div id="view-table" class="table-container" style="display:none;">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Product</th>
                        <th>Category</th>
                        <th>Supplier</th>
                        <th style="text-align:right;">Price</th>
                        <th style="text-align:right;">Stock</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <div style="font-weight:600; color:var(--text-main);"><?= htmlspecialchars($product['product_name']) ?></div>
                        </td>
                        <td>
                            <span style="display:inline-flex; align-items:center; gap:6px; background:#f1f5f9; padding:4px 10px; border-radius:20px; font-size:0.85rem; color:#475569;">
                                <span style="width:6px; height:6px; background:#94a3b8; border-radius:50%;"></span>
                                <?= htmlspecialchars($product['category_name']) ?>
                            </span>
                        </td>
                        <td>
                            <div style="font-weight:500; color:var(--text-sub);"><?= htmlspecialchars($product['supplier_name']) ?></div>
                        </td>
                        <td style="text-align:right; font-weight:600; color:var(--text-main);">
                            रु <?= number_format($product['unit_price'], 2) ?>
                        </td>
                        <td style="text-align:right;">
                            <?php 
                            $stock = $product['total_stock'];
                            $stockClass = $stock < 10 ? 'background:#fee2e2; color:#991b1b;' : 'background:#dcfce7; color:#166534;';
                            ?>
                            <span style="padding:4px 10px; border-radius:6px; font-weight:700; font-size:0.9rem; white-space: nowrap; <?= $stockClass ?>">
                                <?= $stock ?> units
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:inline-flex; gap:8px;">
                                <button class="action-btn" title="Edit" onclick="openEditModal(<?= $product['product_id'] ?>, '<?= addslashes($product['product_name']) ?>', '<?= $product['unit_price'] ?>', '<?= $product['category_id'] ?>', '<?= $product['supplier_id'] ?>')" style="background:#eff6ff; color:#2563eb; border:none; width:32px; height:32px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                                    <i data-lucide="edit-2" style="width:16px;"></i>
                                </button>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <button class="action-btn" title="Delete" onclick="confirmDelete(<?= $product['product_id'] ?>)" style="background:#fee2e2; color:#dc2626; border:none; width:32px; height:32px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                                    <i data-lucide="trash-2" style="width:16px;"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <script>
            function toggleView(view) {
                const cardView = document.getElementById('view-card');
                const tableView = document.getElementById('view-table');
                const btnCard = document.getElementById('btn-card');
                const btnTable = document.getElementById('btn-table');

                if (view === 'card') {
                    cardView.style.display = 'grid';
                    tableView.style.display = 'none';
                    btnCard.classList.add('active');
                    btnTable.classList.remove('active');
                } else {
                    cardView.style.display = 'none';
                    tableView.style.display = 'block';
                    btnTable.classList.add('active');
                    btnCard.classList.remove('active');
                }
            }
            
            // Initialize icons when dom loads
            document.addEventListener('DOMContentLoaded', () => {
                if(window.lucide) lucide.createIcons();
            });
        </script>

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
    document.getElementById('editProductModal').style.display = 'flex';
}
function confirmDelete(id) {
    showConfirmModal({
        title: 'Delete Product?',
        message: 'Are you sure you want to delete this product? This will also affect stock records.',
        icon: '🗑️',
        iconType: 'danger',
        confirmText: 'Yes, Delete',
        confirmClass: 'confirm',
        onConfirm: () => {
            window.location.href = 'product/delete.php?id=' + id;
        }
    });
}
</script>
</body>
</html>
