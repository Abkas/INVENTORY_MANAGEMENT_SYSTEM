<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
    exit();
}
require_once __DIR__ . '/db/connect.php';

// 1. Total Sales & Purchases
$sales_query = mysqli_query($conn, "SELECT SUM(total_price) as total_sales, COUNT(*) as sales_count FROM sales");
$sales_data = mysqli_fetch_assoc($sales_query);

$purchase_query = mysqli_query($conn, "SELECT SUM(total_price) as total_purchases, COUNT(*) as purchase_count FROM purchase");
$purchase_data = mysqli_fetch_assoc($purchase_query);

// 2. Stock Summary
$stock_query = mysqli_query($conn, "SELECT SUM(s.quantity * p.unit_price) as stock_value, SUM(s.quantity) as total_items FROM stock s JOIN product p ON s.product_id = p.product_id");
$stock_data = mysqli_fetch_assoc($stock_query);

// 3. Data for Charts (Last 30 Days)
$chart_labels = [];
$chart_sales = [];
$chart_purchases = [];

for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chart_labels[] = date('M d', strtotime($date));
    
    $s_q = mysqli_query($conn, "SELECT SUM(total_price) as daily_total FROM sales WHERE sales_date = '$date'");
    $s_r = mysqli_fetch_assoc($s_q);
    $chart_sales[] = (float)($s_r['daily_total'] ?? 0);

    $p_q = mysqli_query($conn, "SELECT SUM(total_price) as daily_total FROM purchase WHERE purchase_date = '$date'");
    $p_r = mysqli_fetch_assoc($p_q);
    $chart_purchases[] = (float)($p_r['daily_total'] ?? 0);
}

// 4. Low Stock Alert
$low_stock_query = mysqli_query($conn, "SELECT p.product_name, SUM(s.quantity) as total_qty FROM stock s JOIN product p ON s.product_id = p.product_id GROUP BY s.product_id HAVING total_qty < 10");
$low_stock_items = [];
while($row = mysqli_fetch_assoc($low_stock_query)) {
    $low_stock_items[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Intelligence | Inventory Manager</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Report specific overrides if needed */
        @media print {
            .sidebar, .quick-actions-panel, .add-btn { display: none !important; }
            .main-content { margin-left: 0 !important; width: 100% !important; }
            .dashboard-grid { display: block; }
            .chart-container { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

<div class="container">
    <?php include __DIR__ . '/components/sidebar.php'; ?>

    <main class="main-content">
        <!-- 1. Header -->
        <div class="welcome-section">
            <div class="welcome-text">
                <h1>Business Intelligence</h1>
                <p>Comprehensive overview of financial performance and inventory health.</p>
            </div>
        </div>

        <!-- 2. Key Metrics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div>
                    <div class="label">Total Sales</div>
                    <div class="value">रु <?= number_format($sales_data['total_sales'] ?? 0, 0) ?></div>
                </div>
                <div class="growth" style="color:var(--success); background:#dcfce7;">
                    ⚡ <?= $sales_data['sales_count'] ?> Trx
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="label">Total Purchases</div>
                    <div class="value">रु <?= number_format($purchase_data['total_purchases'] ?? 0, 0) ?></div>
                </div>
                <div class="growth" style="color:var(--danger); background:#fee2e2;">
                    🛒 <?= $purchase_data['purchase_count'] ?> Orders
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="label">Inventory Value</div>
                    <div class="value">रु <?= number_format($stock_data['stock_value'] ?? 0, 0) ?></div>
                </div>
                <div class="growth" style="color:var(--primary); background:#e0f2fe;">
                    📦 <?= $stock_data['total_items'] ?? 0 ?> Items
                </div>
            </div>
            
            <?php $profit = ($sales_data['total_sales'] ?? 0) - ($purchase_data['total_purchases'] ?? 0); ?>
            <div class="stat-card" style="border-left: 4px solid <?= $profit >= 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                <div>
                    <div class="label">Net Cashflow</div>
                    <div class="value" style="color:<?= $profit >= 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                         <?= $profit >= 0 ? '+' : '' ?> रु <?= number_format($profit, 0) ?>
                    </div>
                </div>
                <div style="font-size:0.8rem; Color:var(--text-sub);">Sales vs Procurement</div>
            </div>
        </div>

        <!-- 3. Report Grid -->
        <div class="dashboard-grid">
            <!-- Left Column: Detailed Data -->
            <div class="main-column">
                <!-- Financial Chart -->
                <div class="chart-panel">
                    <h3>📈 30-Day Performance Trend</h3>
                    <div class="chart-container">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>

                <!-- Detailed Audit Log -->
                <div class="chart-panel">
                    <h3>
                        <span>📋 Detailed Activity Log</span>
                        <a href="sales.php" style="font-size:0.85rem; font-weight:600; color:var(--primary); text-decoration:none;">View All</a>
                    </h3>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Customer/Source</th>
                                    <th>Item Details</th>
                                    <th>Type</th>
                                    <th style="text-align: right;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $activity = mysqli_query($conn, "
                                    (SELECT 
                                        'SALE' as type,
                                        s.sales_date as date,
                                        c.customer_name as name,
                                        p.product_name as item,
                                        cat.category_name as category,
                                        s.quantity,
                                        s.total_price as amount,
                                        u.username as staff_name
                                    FROM sales s 
                                    JOIN product p ON s.product_id = p.product_id 
                                    JOIN customer c ON s.customer_id = c.customer_id 
                                    JOIN category cat ON p.category_id = cat.category_id
                                    LEFT JOIN user u ON s.user_id = u.user_id)
                                    
                                    UNION ALL
                                    
                                    (SELECT 
                                        'PURCHASE' as type,
                                        pur.purchase_date as date,
                                        sup.supplier_name as name,
                                        p.product_name as item,
                                        cat.category_name as category,
                                        pur.quantity,
                                        pur.total_price as amount,
                                        u.username as staff_name
                                    FROM purchase pur
                                    JOIN product p ON pur.product_id = p.product_id 
                                    JOIN supplier sup ON pur.supplier_id = sup.supplier_id 
                                    JOIN category cat ON p.category_id = cat.category_id
                                    LEFT JOIN user u ON pur.user_id = u.user_id)
                                    
                                    ORDER BY date DESC LIMIT 20
                                ");
                                while($row = mysqli_fetch_assoc($activity)): 
                                    $is_sale = $row['type'] === 'SALE';
                                ?>
                                <tr>
                                    <td style="color: var(--secondary);"><?= date('M d', strtotime($row['date'])) ?></td>
                                    <td style="font-weight: 600;">
                                        <?= htmlspecialchars($row['name']) ?>
                                        <div style="font-size:0.75rem; color:var(--text-sub); font-weight:400; margin-top:2px;">
                                            <?= $is_sale ? 'Customer' : 'Supplier' ?>
                                        </div>
                                        <?php if (!empty($row['staff_name'])): ?>
                                        <div style="font-size:0.7rem; color:#6366f1; font-weight:500; margin-top:4px; display:inline-flex; align-items:center; gap:4px;">
                                            <i data-lucide="user" style="width:10px;"></i>
                                            <?= htmlspecialchars($row['staff_name']) ?>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($row['item']) ?> 
                                        <small style="color:var(--secondary);">(Qty: <?= $row['quantity'] ?>)</small>
                                        <br>
                                        <small style="background: #f1f5f9; color: #475569; padding: 2px 6px; border-radius: 3px; font-size: 0.7rem; font-weight: 600; display: inline-block; margin-top: 4px;">
                                            <?= htmlspecialchars($row['category']) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if($is_sale): ?>
                                            <span style="background:#ecfdf5; color:#059669; padding:0.25rem 0.5rem; border-radius:4px; font-size:0.7rem; font-weight:700;">SALE</span>
                                        <?php else: ?>
                                            <span style="background:#fff7ed; color:#c2410c; padding:0.25rem 0.5rem; border-radius:4px; font-size:0.7rem; font-weight:700;">PURCHASE</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right; font-weight: 700; color: <?= $is_sale ? 'var(--success)' : 'var(--danger)' ?>;">
                                        <?= $is_sale ? '+' : '-' ?> रु <?= number_format($row['amount'], 2) ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Alerts & Summary -->
            <div class="side-column">
                 <!-- Critical Stock Feed -->
                 <div class="chart-panel">
                    <h3>⚠️ Critical Stock Levels</h3>
                    <div class="alert-feed" style="max-height: 500px;">
                        <?php if(empty($low_stock_items)): ?>
                            <div style="text-align: center; padding: 3rem 1rem; color: var(--text-sub);">
                                <div style="font-size: 2rem; margin-bottom: 0.5rem;">✅</div>
                                Stock levels optimal
                            </div>
                        <?php else: ?>
                            <?php foreach($low_stock_items as $item): ?>
                                <div class="alert-item">
                                    <div>
                                        <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                        <span>Critical: <?= $item['total_qty'] ?> left</span>
                                    </div>
                                    <a href="purchases.php" class="btn-restock">Restock</a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    const ctx = document.getElementById('performanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_labels) ?>,
            datasets: [
                {
                    label: 'Sales Revenue',
                    data: <?= json_encode($chart_sales) ?>,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.05)',
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Purchase Expense',
                    data: <?= json_encode($chart_purchases) ?>,
                    borderColor: '#94a3b8',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: { 
                    position: 'top',
                    align: 'end',
                    labels: {usePointStyle: true, padding: 20, font: {family: "'Inter', sans-serif"}}
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    padding: 12,
                    cornerRadius: 8,
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: { font: { family: "'Inter', sans-serif", size: 11 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: "'Inter', sans-serif", size: 11 } }
                }
            }
        }
    });
</script>
</body>
</html>
