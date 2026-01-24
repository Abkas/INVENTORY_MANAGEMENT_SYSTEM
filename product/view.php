<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header("Location: ../products.php");
    exit();
}

$product_query = "SELECT p.*, c.category_name, s.supplier_name, COALESCE(SUM(st.quantity), 0) as total_stock 
                  FROM product p 
                  JOIN category c ON p.category_id = c.category_id 
                  JOIN supplier s ON p.supplier_id = s.supplier_id 
                  LEFT JOIN stock st ON p.product_id = st.product_id 
                  WHERE p.product_id = $product_id
                  GROUP BY p.product_id";
$product_res = mysqli_query($conn, $product_query);
$product = mysqli_fetch_assoc($product_res);

if (!$product) {
    echo "Product not found.";
    exit();
}

$stock_query = "SELECT st.quantity, w.warehouse_name, w.location 
                FROM stock st 
                JOIN warehouse w ON st.warehouse_id = w.warehouse_id 
                WHERE st.product_id = $product_id AND st.quantity > 0";
$stock_res = mysqli_query($conn, $stock_query);
$warehouse_stock = [];
while ($row = mysqli_fetch_assoc($stock_res)) {
    $warehouse_stock[] = $row;
}

$sales_query = "SELECT sa.*, c.customer_name 
                FROM sales sa 
                JOIN customer c ON sa.customer_id = c.customer_id 
                WHERE sa.product_id = $product_id 
                ORDER BY sa.sales_date DESC LIMIT 5";
$sales_res = mysqli_query($conn, $sales_query);
$sales_history = [];
while ($row = mysqli_fetch_assoc($sales_res)) {
    $sales_history[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['product_name']) ?> - Details</title>
    <link rel="stylesheet" href="../css/global.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../css/shared_cards.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .detail-header {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .detail-title { font-size: 1.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem; }
        .detail-meta { color: var(--text-sub); display: flex; gap: 1rem; align-items: center; }
        
        .stock-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .warehouse-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .wh-icon {
            width: 48px;
            height: 48px;
            background: #eff6ff;
            color: #2563eb;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .section-title { font-size: 1.2rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-main); }
    </style>
</head>
<body>
<div class="container">
    <?php $path_prefix = '../'; include __DIR__ . '/../components/sidebar.php'; ?>
    
    <div class="main-content">
        <div style="margin-bottom: 1rem;">
            <a href="../products.php" style="text-decoration: none; color: var(--text-sub); display: inline-flex; align-items: center; gap: 5px;">
                <i data-lucide="arrow-left" style="width:16px;"></i> Back to Products
            </a>
        </div>

        <div class="detail-header">
            <div>
                <div class="detail-title"><?= htmlspecialchars($product['product_name']) ?></div>
                <div class="detail-meta">
                    <span style="background: #f1f5f9; padding: 4px 10px; border-radius: 20px; font-size: 0.9rem;">
                        <?= htmlspecialchars($product['category_name']) ?>
                    </span>
                    <span>•</span>
                    <span>Supplier: <?= htmlspecialchars($product['supplier_name']) ?></span>
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.9rem; color: var(--text-sub);">Price</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--success);">रु <?= number_format($product['unit_price'], 2) ?></div>
            </div>
        </div>

        <div class="section-title">🏭 Warehouse Distribution</div>
        <div class="stock-grid">
            <?php if (count($warehouse_stock) > 0): ?>
                <?php foreach ($warehouse_stock as $wh): ?>
                <div class="warehouse-card">
                    <div class="wh-icon"><i data-lucide="warehouse"></i></div>
                    <div>
                        <div style="font-weight: 600; font-size: 1rem; color: var(--text-main);"><?= htmlspecialchars($wh['warehouse_name']) ?></div>
                        <div style="font-size: 0.85rem; color: var(--text-sub); margin-bottom: 4px;"><?= htmlspecialchars($wh['location']) ?></div>
                        <div style="font-weight: 700; color: var(--primary);"><?= $wh['quantity'] ?> units</div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; background: #fff; padding: 2rem; text-align: center; border-radius: 8px; color: var(--text-sub);">
                    No stock currently available in any warehouse.
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent Sales (Optional, but good for context) -->
        <div class="section-title">📉 Recent Sales</div>
        <div class="table-container">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th style="text-align:right;">Quantity</th>
                        <th style="text-align:right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales_history as $sale): ?>
                    <tr>
                        <td><?= date('M d, Y', strtotime($sale['sales_date'])) ?></td>
                        <td><?= htmlspecialchars($sale['customer_name']) ?></td>
                        <td style="text-align:right; font-weight: 600;"><?= $sale['quantity'] ?></td>
                        <td style="text-align:right;">रु <?= number_format($sale['total_price'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (count($sales_history) === 0): ?>
                        <tr><td colspan="4" style="text-align:center; padding: 1rem; color: var(--text-sub);">No sales recorded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    if(window.lucide) lucide.createIcons();
</script>
</body>
</html>
