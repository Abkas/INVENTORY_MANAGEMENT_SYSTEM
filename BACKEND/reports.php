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
    <title>Analytics | Inventory Management</title>
    <link rel="stylesheet" href="css/global.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .container { min-height: 100vh; width: 100%; }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: white;
            padding: 1.75rem;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stat-card h3 {
            margin: 0;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-card .value {
            font-size: 1.75rem;
            font-weight: 800;
            margin: 0.75rem 0;
            color: var(--text-main);
            letter-spacing: -0.025em;
        }

        .stat-card .trend {
            font-size: 0.875rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        /* Charts Row */
        .chart-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .panel {
            background: white;
            padding: 1.75rem;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }

        .panel h2 {
            margin: 0 0 1.5rem 0;
            font-size: 1.125rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-main);
        }

        /* Responsive adjustments */
        @media (max-width: 1100px) {
            .chart-row { grid-template-columns: 1fr; }
        }

        @media (max-width: 640px) {
            .stats-grid { grid-template-columns: 1fr; }
            .stat-card .value { font-size: 1.5rem; }
        }

        /* Table Aesthetics */
        .table-wrap {
            width: 100%;
            overflow-x: auto;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
        }

        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th { 
            text-align: left; 
            padding: 1rem 1.25rem; 
            color: var(--secondary); 
            font-size: 0.7rem; 
            text-transform: uppercase; 
            font-weight: 700;
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
        }
        td { 
            padding: 1.125rem 1.25rem; 
            border-bottom: 1px solid var(--border); 
            font-size: 0.875rem;
            color: var(--text-main);
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fcfcfd; }

        .alert-item {
            padding: 1rem;
            background: #fff5f5;
            border-left: 4px solid var(--danger);
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .badge-sale {
            background: #ecfdf5; 
            color: #059669; 
            padding: 0.35rem 0.65rem; 
            border-radius: 0.5rem; 
            font-size: 0.65rem; 
            font-weight: 800;
            letter-spacing: 0.025em;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include __DIR__ . '/components/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="header">
                <div>
                    <h1 class="header-title">Business Intelligence</h1>
                    <p class="header-sub">Comprehensive overview of financial performance and inventory health</p>
                </div>
                <div class="header-actions">
                    <button class="add-btn" onclick="window.print()">🖨️ Export PDF</button>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Revenue (Sales)</h3>
                    <div class="value">रु <?= number_format($sales_data['total_sales'] ?? 0, 0) ?></div>
                    <div class="trend" style="color: var(--success);">⚡ <?= $sales_data['sales_count'] ?> Transactions</div>
                </div>
                <div class="stat-card">
                    <h3>Expense (Purchases)</h3>
                    <div class="value">रु <?= number_format($purchase_data['total_purchases'] ?? 0, 0) ?></div>
                    <div class="trend" style="color: var(--danger);">🛒 <?= $purchase_data['purchase_count'] ?> Restocks</div>
                </div>
                <div class="stat-card">
                    <h3>Inventory Value</h3>
                    <div class="value">रु <?= number_format($stock_data['stock_value'] ?? 0, 0) ?></div>
                    <div class="trend" style="color: var(--primary);">📦 <?= $stock_data['total_items'] ?? 0 ?> Items in Stock</div>
                </div>
                <div class="stat-card">
                    <h3>Net Cashflow</h3>
                    <?php $profit = ($sales_data['total_sales'] ?? 0) - ($purchase_data['total_purchases'] ?? 0); ?>
                    <div class="value" style="color: <?= $profit >= 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                        रु <?= number_format($profit, 0) ?>
                    </div>
                    <div class="trend">Sales vs Procurement</div>
                </div>
            </div>

            <div class="chart-row">
                <div class="panel">
                    <h2>📈 Financial Trend (30 Days)</h2>
                    <div style="height: 350px;">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
                <div class="panel">
                    <h2>⚠️ Critical Stock Alerts</h2>
                    <div style="max-height: 350px; overflow-y: auto;">
                        <?php if(empty($low_stock_items)): ?>
                            <div style="text-align: center; padding: 2rem; color: var(--secondary);">
                                <div style="font-size: 2rem; margin-bottom: 0.5rem;">✅</div>
                                All inventory levels are optimal.
                            </div>
                        <?php else: ?>
                            <?php foreach($low_stock_items as $item): ?>
                                <div class="alert-item">
                                    <div>
                                        <div style="font-weight: 600;"><?= htmlspecialchars($item['product_name']) ?></div>
                                        <div style="font-size: 0.75rem; color: var(--secondary);">Needs immediate restock</div>
                                    </div>
                                    <div style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.75rem; font-weight: 700;">
                                        <?= $item['total_qty'] ?> Left
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="panel">
                <h2>🕒 Recent Activity Log</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Transaction Date</th>
                                <th>Customer / Source</th>
                                <th>Item Details</th>
                                <th>Action</th>
                                <th style="text-align: right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $activity = mysqli_query($conn, "SELECT s.*, p.product_name, c.customer_name FROM sales s JOIN product p ON s.product_id = p.product_id JOIN customer c ON s.customer_id = c.customer_id ORDER BY s.sales_id DESC LIMIT 10");
                            while($row = mysqli_fetch_assoc($activity)): ?>
                            <tr>
                                <td style="color: var(--secondary);"><?= date('M d, H:i', strtotime($row['sales_date'])) ?></td>
                                <td style="font-weight: 600;"><?= htmlspecialchars($row['customer_name']) ?></td>
                                <td><?= htmlspecialchars($row['product_name']) ?> <small style="color:var(--secondary);">(x<?= $row['quantity'] ?>)</small></td>
                                <td><span class="badge-sale">SALE</span></td>
                                <td style="text-align: right; font-weight: 700; color: var(--success);">रु <?= number_format($row['total_price'], 2) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 1.5rem; text-align: center;">
                    <button class="add-btn" style="background: transparent; border: 1px solid var(--border); color: var(--text-main);" onclick="location.href='sales.php'">View Full Audit Log</button>
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
                        backgroundColor: 'rgba(37, 99, 235, 0.08)',
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#2563eb',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Purchase Expense',
                        data: <?= json_encode($chart_purchases) ?>,
                        borderColor: '#94a3b8',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [6, 4],
                        pointRadius: 0,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: "'Inter', sans-serif", size: 12, weight: '500' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        titleFont: { size: 13, weight: '700' },
                        bodyFont: { size: 12 },
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: { 
                            font: { family: "'Inter', sans-serif", size: 11 },
                            callback: function(value) { return 'रु ' + value.toLocaleString(); }
                        }
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
