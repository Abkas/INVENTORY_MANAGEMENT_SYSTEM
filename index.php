<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: user/login.php");
    exit();
}

require_once __DIR__ . '/db/connect.php';

// --- 1. Basic Stats ---
$sales_stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) as total_val, COUNT(*) as total_count FROM sales"));
$product_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM product"));
$low_stock_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM (SELECT product_id FROM stock GROUP BY product_id HAVING SUM(quantity) < 10) as low_stock"));

// --- 2. Today vs Yesterday (True Dynamic Stats) ---
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime("-1 days"));

$today_sales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) as val FROM sales WHERE sales_date = '$today'"));
$yesterday_sales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) as val FROM sales WHERE sales_date = '$yesterday'"));

$today_val = $today_sales['val'] ?? 0;
$yesterday_val = $yesterday_sales['val'] ?? 0;

$diff = $today_val - $yesterday_val;
$perc = ($yesterday_val > 0) ? ($diff / $yesterday_val) * 100 : 0;

// --- 3. Chart Data: Sales for Last 7 Days ---
$chart_labels = [];
$chart_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chart_labels[] = date('D', strtotime($date));
    $s_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) as val FROM sales WHERE sales_date = '$date'"));
    $chart_data[] = $s_res['val'] ?? 0;
}

// --- 4. Chart Data: Stock Distribution (Top 5 Categories) ---
$cat_dist = mysqli_query($conn, "SELECT c.category_name, SUM(s.quantity) as qty FROM stock s JOIN product p ON s.product_id = p.product_id JOIN category c ON p.category_id = c.category_id GROUP BY c.category_id LIMIT 5");
$cat_labels = [];
$cat_values = [];
while($row = mysqli_fetch_assoc($cat_dist)) {
    $cat_labels[] = $row['category_name'];
    $cat_values[] = $row['qty'];
}

// --- 5. Recent Activity ---
$recent_sales = mysqli_query($conn, "SELECT s.*, p.product_name, c.customer_name FROM sales s JOIN product p ON s.product_id = p.product_id JOIN customer c ON s.customer_id = c.customer_id ORDER BY s.sales_id DESC LIMIT 5");
$low_stock_products = mysqli_query($conn, "SELECT p.product_name, SUM(s.quantity) as total_qty FROM stock s JOIN product p ON s.product_id = p.product_id GROUP BY s.product_id HAVING total_qty < 10 LIMIT 5");

$hour = date('H');
$greeting = ($hour < 12) ? "Good Morning" : (($hour < 18) ? "Good Afternoon" : "Good Evening");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Dashboard | Inventory Manager</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="container">
    <?php include __DIR__ . '/components/sidebar.php'; ?>

    <main class="main-content">
        <!-- 1. Hero / Welcome -->
        <div class="welcome-section">
            <div class="welcome-text">
                <h1><?= $greeting ?>, <?= htmlspecialchars($_SESSION['user']) ?></h1>
                <p>Overview of your business performance.</p>
            </div>
            <div style="text-align:right;">
                <div style="font-weight:700; color:var(--text-main); font-size: 1rem;"><?= date('l, F j') ?></div>
                <div style="font-size:0.8rem; color:var(--text-sub);">System Status: <span style="color:var(--success); font-weight:700;">● Online</span></div>
            </div>
        </div>

        <!-- 2. High-Level Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div>
                    <div class="label">Daily Revenue</div>
                    <div class="value">रु <?= number_format($today_val, 2) ?></div>
                </div>
                <div class="growth <?= ($diff >= 0) ? 'up' : 'down' ?>">
                    <?= ($diff >= 0) ? '▲' : '▼' ?> <?= number_format(abs($perc), 1) ?>%
                </div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="label">Total Orders</div>
                    <div class="value"><?= $sales_stats['total_count'] ?? 0 ?></div>
                </div>
                <div style="font-size:0.8rem; color:var(--text-sub);">Lifetime Volume</div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="label">Active Inventory</div>
                    <div class="value"><?= $product_count['total'] ?? 0 ?></div>
                </div>
                <div style="font-size:0.8rem; color:var(--text-sub);">SKUs in Stock</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid var(--danger);">
                <div>
                    <div class="label" style="color:var(--danger);">Critical Alerts</div>
                    <div class="value" style="color:var(--danger);"><?= $low_stock_count['total'] ?? 0 ?></div>
                </div>
                <div style="font-size:0.8rem; color:var(--text-sub);">Low Stock Items</div>
            </div>
        </div>

        <!-- 3. Executive Grid (2 Columns) -->
        <div class="dashboard-grid">
            <!-- Left Column: Data & Charts -->
            <div class="main-column">
                <!-- Quick Actions Panel (Moved Here) -->
                <div class="quick-actions-panel">
                    <h3 style="margin:0; font-size:1.1rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:0.75rem;">⚡ Quick Actions</h3>
                    <div class="qa-grid"> <!-- Can reuse qa-grid or make a new wide one -->
                        <a href="sales.php" class="qa-btn">
                            <span>💸</span> New Sale
                        </a>
                        <a href="purchases.php" class="qa-btn">
                            <span>📦</span> Restock
                        </a>
                        <a href="products.php" class="qa-btn">
                            <span>🏷️</span> Product
                        </a>
                        <a href="reports.php" class="qa-btn">
                            <span>📊</span> Reports
                        </a>
                    </div>
                </div>

                <!-- Sales Chart -->
                <div class="chart-panel">
                    <h3>📈 Revenue Trend (7 Days)</h3>
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Right Column: Actions & Alerts -->
            <div class="side-column">
                <!-- Category Distribution Mini (Moved to Top of Side) -->
                <div class="chart-panel">
                    <h3>📊 Distribution</h3>
                    <div style="height: 200px; position: relative;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>

                <!-- Stock Warnings Feed (Below Pie Chart) -->
                <div class="chart-panel">
                    <h3>⚠️ Low Stock Feed</h3>
                    <div class="alert-feed">
                        <?php if ($low_stock_products && mysqli_num_rows($low_stock_products) > 0): ?>
                            <?php 
                            // Reset pointer just in case
                            mysqli_data_seek($low_stock_products, 0);
                            while($item = mysqli_fetch_assoc($low_stock_products)): 
                            ?>
                            <div class="alert-item">
                                <div>
                                    <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                    <span><?= $item['total_qty'] ?> Units Remaining</span>
                                </div>
                                <a href="purchases.php" class="btn-restock">Add</a>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div style="text-align:center; padding: 2rem; color:var(--text-sub);">
                                <div style="font-size:2rem; margin-bottom:0.5rem; opacity:0.5;">✅</div>
                                Stock levels optimal
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    // 1. Sales Trend Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_labels) ?>,
            datasets: [{
                label: 'Sales (रु)',
                data: <?= json_encode($chart_data) ?>,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.05)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointRadius: 3,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#2563eb',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
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

    // 2. Category Pie Chart
    const catCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(catCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($cat_labels) ?>,
            datasets: [{
                data: <?= json_encode($cat_values) ?>,
                backgroundColor: ['#2563eb', '#059669', '#d97706', '#dc2626', '#64748b'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    display: false // Hide legend to save space in sidebar
                }
            },
            cutout: '70%'
        }
    });
</script>

</body>
</html>
