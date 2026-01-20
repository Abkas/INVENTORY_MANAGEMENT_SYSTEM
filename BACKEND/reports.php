<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
    exit();
}
require_once __DIR__ . '/db/connect.php';

// 1. Total Sales
$sales_query = mysqli_query($conn, "SELECT SUM(total_price) as total_sales, COUNT(*) as sales_count FROM sales");
$sales_data = mysqli_fetch_assoc($sales_query);

// 2. Total Purchases
$purchase_query = mysqli_query($conn, "SELECT SUM(total_price) as total_purchases, COUNT(*) as purchase_count FROM purchase");
$purchase_data = mysqli_fetch_assoc($purchase_query);

// 3. Stock Summary
$stock_query = mysqli_query($conn, "SELECT SUM(s.quantity * p.unit_price) as stock_value, SUM(s.quantity) as total_items FROM stock s JOIN product p ON s.product_id = p.product_id");
$stock_data = mysqli_fetch_assoc($stock_query);

// 4. Low Stock Alert (Less than 10)
$low_stock_query = mysqli_query($conn, "SELECT p.product_name, SUM(s.quantity) as total_qty FROM stock s JOIN product p ON s.product_id = p.product_id GROUP BY s.product_id HAVING total_qty < 10");
$low_stock_items = [];
while($row = mysqli_fetch_assoc($low_stock_query)) {
    $low_stock_items[] = $row;
}

// 5. Recent Sales
$recent_sales = mysqli_query($conn, "SELECT s.*, p.product_name, c.customer_name FROM sales s JOIN product p ON s.product_id = p.product_id JOIN customer c ON s.customer_id = c.customer_id ORDER BY s.sales_id DESC LIMIT 5");

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reports & Analytics</title>
    <link rel="stylesheet" href="css/categories.css"> <!-- Reusing container/header styles -->
    <style>
        .report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .report-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            border: 1px solid #f1f5f9;
        }
        .report-card h3 { margin-top: 0; color: #64748b; font-size: 0.9rem; text-transform: uppercase; }
        .report-card .value { font-size: 1.8rem; font-weight: 700; color: #1e293b; margin: 10px 0; }
        .report-card .subtext { color: #94a3b8; font-size: 0.85rem; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; border-radius: 8px; overflow: hidden; }
        th, td { text-align: left; padding: 12px 15px; border-bottom: 1px solid #f1f5f9; }
        th { background: #f8fafc; color: #64748b; font-weight: 600; }
        
        .alert { background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 5px; font-size: 0.9rem; }
    </style>
</head>
<body>
<div class="container">
    <?php include __DIR__ . '/components/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <div>
                <div class="header-title">Reports & Analytics</div>
                <div class="header-sub">Overview of your business performance</div>
            </div>
        </div>

        <div class="report-grid">
            <div class="report-card">
                <h3>Total Sales</h3>
                <div class="value">रु <?= number_format($sales_data['total_sales'] ?? 0, 2) ?></div>
                <div class="subtext"><?= $sales_data['sales_count'] ?> Total Transactions</div>
            </div>
            <div class="report-card">
                <h3>Total Purchases</h3>
                <div class="value">रु <?= number_format($purchase_data['total_purchases'] ?? 0, 2) ?></div>
                <div class="subtext"><?= $purchase_data['purchase_count'] ?> Total Restocks</div>
            </div>
            <div class="report-card">
                <h3>Inventory Value</h3>
                <div class="value">रु <?= number_format($stock_data['stock_value'] ?? 0, 2) ?></div>
                <div class="subtext"><?= $stock_data['total_items'] ?? 0 ?> Items in Stock</div>
            </div>
            <div class="report-card">
                <h3>Net Profit (Est.)</h3>
                <div class="value" style="color: #059669;">रु <?= number_format(($sales_data['total_sales'] - $purchase_data['total_purchases']), 2) ?></div>
                <div class="subtext">Sales minus Purchases</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <div>
                <h3>Recent Sales</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($sale = mysqli_fetch_assoc($recent_sales)): ?>
                        <tr>
                            <td><?= $sale['sales_date'] ?></td>
                            <td><?= htmlspecialchars($sale['customer_name']) ?></td>
                            <td><?= htmlspecialchars($sale['product_name']) ?></td>
                            <td><?= $sale['quantity'] ?></td>
                            <td>रु <?= number_format($sale['total_price'], 2) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div>
                <h3>Low Stock Alerts</h3>
                <?php if(empty($low_stock_items)): ?>
                    <p style="color: #64748b;">All items well stocked.</p>
                <?php else: ?>
                    <?php foreach($low_stock_items as $item): ?>
                        <div class="alert">
                            <strong><?= htmlspecialchars($item['product_name']) ?></strong> is low: <?= $item['total_qty'] ?> left
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
