<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: user/login.php");
    exit();
}
require_once __DIR__ . '/db/connect.php';

$categories = [];
$cat_result = mysqli_query($conn, "SELECT * FROM category ORDER BY category_name ASC");
while ($row = mysqli_fetch_assoc($cat_result)) {
    $categories[] = $row;
}
$products = [];
$prod_result = mysqli_query($conn, "SELECT * FROM product ORDER BY product_name ASC");
while ($row = mysqli_fetch_assoc($prod_result)) {
    $products[] = $row;
}
$suppliers = [];
$sup_result = mysqli_query($conn, "SELECT * FROM supplier ORDER BY supplier_name ASC");
while ($row = mysqli_fetch_assoc($sup_result)) {
    $suppliers[] = $row;
}
$warehouses = [];
$wh_result = mysqli_query($conn, "SELECT * FROM warehouse ORDER BY warehouse_name ASC");
while ($row = mysqli_fetch_assoc($wh_result)) {
    $warehouses[] = $row;
}
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
                <div class="header-title">Purchases</div>
                <div class="header-sub">Manage your purchase records</div>
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
                <button class="add-btn" onclick="document.getElementById('addPurchaseModal').style.display='flex'">
                    <i data-lucide="plus" style="width:18px;"></i> Add Purchase
                </button>
            </div>
        </div>

        <div id="view-card" class="purchase-card-grid responsive-grid">
            <?php foreach ($purchases as $purchase): ?>
                <?php include __DIR__ . '/components/purchase_card.php'; ?>
            <?php endforeach; ?>
        </div>

        <div id="view-table" class="table-container" style="display:none;">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th class="col-hide-mobile">Date</th>
                        <th>Product</th>
                        <th>Supplier</th>
                        <th style="text-align:right;">Qty</th>
                        <th style="text-align:right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($purchases as $purchase): ?>
                    <tr>
                        <td class="col-hide-mobile" style="color:var(--text-sub); font-weight:500;"><?= date('M d, Y', strtotime($purchase['purchase_date'])) ?></td>
                        <td>
                            <div style="font-weight:600; color:var(--text-main);"><?= htmlspecialchars($purchase['product_name']) ?></div>
                        </td>
                        <td>
                            <?php if (!empty($purchase['supplier_name'])): ?>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div style="width:32px; height:32px; background:#f0fdf4; color:#166534; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:0.8rem;">
                                        <?= strtoupper(substr($purchase['supplier_name'], 0, 1)) ?>
                                    </div>
                                    <span style="font-weight:500;"><?= htmlspecialchars($purchase['supplier_name']) ?></span>
                                </div>
                            <?php else: ?>
                                <span style="color:#94a3b8; font-style:italic;">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:right; font-weight:600; color:var(--text-main);"><?= $purchase['quantity'] ?></td>
                        <td style="text-align:right;">
                            <span style="background:#fee2e2; color:#991b1b; padding:4px 8px; border-radius:6px; font-weight:700; font-size:0.9rem; white-space: nowrap;">
                                रु <?= number_format($purchase['total_price'], 2) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($purchases)): ?>
                    <tr><td colspan="5" style="padding:3rem; text-align:center; color:var(--text-sub);">No records found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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
    </script>

    <div id="addPurchaseModal" class="modal-bg">
        <div class="modal-content modal-content">
            <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Add Purchase</h2>
            <form method="POST" action="purchase/add.php">
                <div class="modal-fields modal-fields">
                    <label class="modal-label">Date
                        <input type="date" name="purchase_date" value="<?= date('Y-m-d') ?>" required>
                    </label>
                    
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 1rem;">
                        <label class="modal-label" style="flex-direction: row; gap: 10px; align-items: center; cursor: pointer;">
                            <input type="checkbox" id="is_new_product" name="is_new_product" onchange="toggleProductMode()">
                            <strong>Is this a NEW product?</strong>
                        </label>
                    </div>

                    <div id="existing_product_div">
                        <label class="modal-label">Select Product
                            <select name="product_id" id="product_select">
                                <option value="">-- Select Product --</option>
                                <?php foreach ($products as $prod): ?>
                                    <option value="<?= $prod['product_id'] ?>"><?= htmlspecialchars($prod['product_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>

                    <div id="new_product_div" style="display: none;">
                        <label class="modal-label">New Product Name
                            <input type="text" name="new_product_name" placeholder="Enter product name">
                        </label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                            <label class="modal-label">Category
                                <select name="category_id">
                                    <option value="">-- Category --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <label class="modal-label">Supplier
                                <select name="supplier_id">
                                    <option value="">-- Supplier --</option>
                                    <?php foreach ($suppliers as $sup): ?>
                                        <option value="<?= $sup['supplier_id'] ?>"><?= htmlspecialchars($sup['supplier_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px;">
                        <label class="modal-label">Quantity
                            <input type="number" name="quantity" placeholder="0" min="1" required>
                        </label>
                        <label class="modal-label">Unit Price (रु)
                            <input type="number" name="unit_price" id="unit_price" placeholder="0.00" step="0.01" oninput="calculateTotal()" required>
                        </label>
                    </div>

                    <label class="modal-label">Target Warehouse
                        <select name="warehouse_id" required>
                            <?php foreach ($warehouses as $wh): ?>
                                <option value="<?= $wh['warehouse_id'] ?>"><?= htmlspecialchars($wh['warehouse_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    
                    <label class="modal-label">Total Amount (रु)
                        <input type="number" name="total_price" id="total_price_input" placeholder="0.00" step="0.01" readonly required>
                    </label>

                    <div class="modal-actions modal-actions">
                        <button type="button" class="modal-cancel modal-cancel" onclick="document.getElementById('addPurchaseModal').style.display='none'">Cancel</button>
                        <button type="submit" class="add-btn add-btn">Confirm Purchase</button>
                    </div>
                </div>
            </form>
            <button class="modal-close" onclick="document.getElementById('addPurchaseModal').style.display='none'">&times;</button>
        </div>
    </div>
</div>

<script>
    function toggleProductMode() {
        const isNew = document.getElementById('is_new_product').checked;
        document.getElementById('existing_product_div').style.display = isNew ? 'none' : 'block';
        document.getElementById('new_product_div').style.display = isNew ? 'block' : 'none';
        
        const productSelect = document.getElementById('product_select');
        const newProdName = document.querySelector('input[name="new_product_name"]');
        
        if (isNew) {
            productSelect.removeAttribute('required');
            newProdName.setAttribute('required', 'required');
        } else {
            productSelect.setAttribute('required', 'required');
            newProdName.removeAttribute('required');
        }
    }

    function calculateTotal() {
        const qty = document.querySelector('input[name="quantity"]').value || 0;
        const price = document.getElementById('unit_price').value || 0;
        document.getElementById('total_price_input').value = (qty * price).toFixed(2);
    }

    if (window.lucide) {
        lucide.createIcons();
    }
</script>
</body>
</html>
