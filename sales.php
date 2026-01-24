<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: user/login.php");
    exit();
}
require_once __DIR__ . '/db/connect.php';

$customers = [];
$cus_result = mysqli_query($conn, "SELECT * FROM customer ORDER BY customer_name ASC");
while ($row = mysqli_fetch_assoc($cus_result)) {
    $customers[] = $row;
}


$products = [];
$prod_result = mysqli_query($conn, "
    SELECT p.product_id, p.product_name, p.unit_price, COALESCE(SUM(s.quantity), 0) as total_stock 
    FROM product p 
    LEFT JOIN stock s ON p.product_id = s.product_id 
    GROUP BY p.product_id 
    ORDER BY p.product_name ASC
");
while ($row = mysqli_fetch_assoc($prod_result)) {
    $products[] = $row;
}

$sales = [];
$sales_result = mysqli_query($conn, "
    SELECT s.*, c.customer_name, p.product_name, cat.category_name 
    FROM sales s 
    JOIN customer c ON s.customer_id = c.customer_id 
    JOIN product p ON s.product_id = p.product_id 
    JOIN category cat ON p.category_id = cat.category_id 
    ORDER BY s.sales_id DESC
");
while ($row = mysqli_fetch_assoc($sales_result)) {
    $sales[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales | Inventory Manager</title>
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
                <div class="header-title">Sales</div>
                <div class="header-sub">Record and track customer sales</div>
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
                <button class="add-btn" onclick="document.getElementById('addSalesModal').style.display='flex'">
                    <i data-lucide="plus" style="width:18px;"></i> New Sale
                </button>
            </div>
        </div>

        <div id="view-card" class="sales-card-grid responsive-grid">
            <?php foreach ($sales as $sale): ?>
                <?php include __DIR__ . '/components/sales_card.php'; ?>
            <?php endforeach; ?>
            <?php if(empty($sales)): ?>
                <p style="grid-column: 1/-1; text-align: center; padding: 3rem; color: #64748b; background: white; border-radius: 12px; border: 1px dashed #cbd5e1;">
                    No sales recorded yet. Click "New Sale" to start.
                </p>
            <?php endif; ?>
        </div>

        <div id="view-table" class="table-container" style="display:none;">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th class="col-hide-mobile">Date</th>
                        <th>Product</th>
                        <th>Customer</th>
                        <th style="text-align:right;">Qty</th>
                        <th style="text-align:right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td class="col-hide-mobile" style="color:var(--text-sub); font-weight:500;"><?= date('M d, Y', strtotime($sale['sales_date'])) ?></td>
                        <td>
                            <div style="font-weight:600; color:var(--text-main);"><?= htmlspecialchars($sale['product_name']) ?></div>
                            <div style="font-size:0.8rem; color:var(--text-sub); margin-top:2px; display:flex; align-items:center; gap:4px;">
                                <span style="display:inline-block; width:6px; height:6px; background:#cbd5e1; border-radius:50%;"></span>
                                <?= htmlspecialchars($sale['category_name']) ?>
                            </div>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <div style="width:32px; height:32px; background:#eff6ff; color:#2563eb; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:0.8rem;">
                                    <?= strtoupper(substr($sale['customer_name'], 0, 1)) ?>
                                </div>
                                <span style="font-weight:500;"><?= htmlspecialchars($sale['customer_name']) ?></span>
                            </div>
                        </td>
                        <td style="text-align:right; font-weight:600; color:var(--text-main);"><?= $sale['quantity'] ?></td>
                        <td style="text-align:right;">
                            <span style="background:#ecfdf5; color:#059669; padding:4px 8px; border-radius:6px; font-weight:700; font-size:0.9rem; white-space: nowrap;">
                                रु <?= number_format($sale['total_price'], 2) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($sales)): ?>
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

    <div id="addSalesModal" class="modal-bg">
        <div class="modal-content modal-content" style="max-width: 550px;">
            <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Record New Sale</h2>
            <form method="POST" action="sales/add.php" onsubmit="return validateStock()">
                <div class="modal-fields modal-fields">
                    <label class="modal-label">Sale Date
                        <input type="date" name="sales_date" value="<?= date('Y-m-d') ?>" required>
                    </label>

                    <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 0.5rem;">
                        <label class="modal-label" style="flex-direction: row; gap: 10px; align-items: center; cursor: pointer;">
                            <input type="checkbox" id="is_new_customer" name="is_new_customer" onchange="toggleCustomerMode()">
                            <strong>Is this a NEW customer?</strong>
                        </label>
                    </div>

                    <div id="existing_customer_div">
                        <label class="modal-label">Customer
                            <select name="customer_id" id="customer_select" required>
                                <option value="">-- Select Customer --</option>
                                <?php foreach ($customers as $cus): ?>
                                    <option value="<?= $cus['customer_id'] ?>"><?= htmlspecialchars($cus['customer_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>

                    <div id="new_customer_div" style="display: none;">
                        <label class="modal-label">New Customer Name
                            <input type="text" name="new_customer_name" id="new_customer_name" placeholder="Enter customer name">
                        </label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                            <label class="modal-label">Email
                                <input type="email" name="customer_email" placeholder="Email (optional)">
                            </label>
                            <label class="modal-label">Phone
                                <input type="text" name="customer_phone" placeholder="Phone (optional)">
                            </label>
                        </div>
                    </div>

                    <label class="modal-label">Product to Sell
                        <select name="product_id" id="sale_product_select" required onchange="updatePrice()">
                            <option value="">-- Select Product --</option>
                            <?php foreach ($products as $prod): ?>
                                <option value="<?= $prod['product_id'] ?>" data-price="<?= $prod['unit_price'] ?>" data-stock="<?= $prod['total_stock'] ?>">
                                    <?= htmlspecialchars($prod['product_name']) ?> (Stk: <?= $prod['total_stock'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px;">
                        <label class="modal-label">Quantity
                            <input type="number" name="quantity" id="sale_quantity" placeholder="0" min="1" required oninput="calculateSaleTotal()">
                        </label>
                        <label class="modal-label">Unit Price (रु)
                            <input type="number" id="sale_unit_price" placeholder="0.00" step="0.01" readonly style="background: #f1f5f9;">
                        </label>
                    </div>
                    
                    <div id="stock_error" style="display:none; color: #dc2626; font-size: 0.9rem; margin-top: -10px; font-weight: 500;">
                        ⚠️ Not enough stock available!
                    </div>

                    <label class="modal-label">Total Sale Amount (रु)
                        <input type="number" name="total_price" id="sale_total_price" placeholder="0.00" step="0.01" readonly required style="background: #f1f5f9; font-weight: bold; color: #059669; font-size: 1.2rem;">
                    </label>

                    <div class="modal-actions modal-actions">
                        <button type="button" class="modal-cancel modal-cancel" onclick="document.getElementById('addSalesModal').style.display='none'">Cancel</button>
                        <button type="submit" id="complete_sale_btn" class="add-btn add-btn">Complete Sale</button>
                    </div>
                </div>
            </form>
            <button class="modal-close" onclick="document.getElementById('addSalesModal').style.display='none'">&times;</button>
        </div>
    </div>
</div>

<script>
    function toggleCustomerMode() {
        const isNew = document.getElementById('is_new_customer').checked;
        document.getElementById('existing_customer_div').style.display = isNew ? 'none' : 'block';
        document.getElementById('new_customer_div').style.display = isNew ? 'block' : 'none';
        
        const customerSelect = document.getElementById('customer_select');
        const newCustomerName = document.getElementById('new_customer_name');
        
        if (isNew) {
            customerSelect.removeAttribute('required');
            newCustomerName.setAttribute('required', 'required');
        } else {
            customerSelect.setAttribute('required', 'required');
            newCustomerName.removeAttribute('required');
        }
    }

    function updatePrice() {
        const select = document.getElementById('sale_product_select');
        const selectedOption = select.options[select.selectedIndex];
        const price = selectedOption.getAttribute('data-price') || 0;
        document.getElementById('sale_unit_price').value = price;
        calculateSaleTotal();
    }

    function calculateSaleTotal() {
        const qtyInput = document.getElementById('sale_quantity');
        const qty = parseFloat(qtyInput.value) || 0;
        const price = parseFloat(document.getElementById('sale_unit_price').value) || 0;
        document.getElementById('sale_total_price').value = (qty * price).toFixed(2);
        
        const select = document.getElementById('sale_product_select');
        const selectedOption = select.options[select.selectedIndex];
        const stock = parseFloat(selectedOption.getAttribute('data-stock')) || 0;
        const errorDiv = document.getElementById('stock_error');
        const btn = document.getElementById('complete_sale_btn');

        if (qty > stock) {
            qtyInput.style.borderColor = '#dc2626';
            errorDiv.style.display = 'block';
            errorDiv.innerHTML = `⚠️ Only ${stock} items available in stock!`;
            btn.disabled = true;
            btn.style.opacity = '0.5';
            btn.style.cursor = 'not-allowed';
        } else {
            qtyInput.style.borderColor = '#e2e8f0'; 
            errorDiv.style.display = 'none';
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
        }
    }
    
    function validateStock() {
        const qty = parseFloat(document.getElementById('sale_quantity').value) || 0;
        const select = document.getElementById('sale_product_select');
        const selectedOption = select.options[select.selectedIndex];
        const stock = parseFloat(selectedOption.getAttribute('data-stock')) || 0;
        
        if (qty > stock) {
             alert("Cannot sell more than available stock!");
             return false;
        }
        return true;
    }

    if (window.lucide) {
        lucide.createIcons();
    }
</script>

</body>
</html>

