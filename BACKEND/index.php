<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
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
    <title>Dynamic Dashboard | Inventory Manager</title>
    <link rel="stylesheet" href="css/categories.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #2563eb;
            --success: #059669;
            --warning: #d97706;
            --danger: #dc2626;
            --secondary: #64748b;
        }

        .main-content {
            padding: 2.5rem;
            background: #f8fafc;
            min-height: 100vh;
        }

        .welcome-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .welcome-text h1 {
            font-size: 1.875rem;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
            letter-spacing: -0.025em;
        }

        .welcome-text p {
            color: #64748b;
            margin-top: 0.25rem;
            font-size: 1.1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: white;
            padding: 1.75rem;
            border-radius: 1.25rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
        }

        .stat-card .label {
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-card .value {
            font-size: 2.25rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0.75rem 0;
            letter-spacing: -0.04em;
        }

        .stat-card .growth {
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.25rem 0.6rem;
            border-radius: 2rem;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .growth.up { background: #dcfce7; color: #15803d; }
        .growth.down { background: #fee2e2; color: #b91c1c; }

        /* Chart Layout */
        .analytics-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2.5rem;
        }

        .chart-panel {
            background: white;
            border-radius: 1.25rem;
            padding: 1.5rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
        }

        .chart-container {
            position: relative;
            width: 100%;
            height: 300px; /* Fixed height to prevent resizing loop */
        }

        .chart-panel h3 {
            margin: 0 0 1.5rem 0;
            font-size: 1.125rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Quick Actions Container */
        .quick-actions {
            background: #1e293b;
            padding: 1.5rem;
            border-radius: 1.25rem;
            margin-bottom: 2.5rem;
            color: white;
        }

        .action-btns {
            display: flex;
            gap: 1.25rem;
            margin-top: 1rem;
        }

        .action-btn {
            background: rgba(255,255,255,0.1);
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .action-btn:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }

        .dashboard-row {
            display: grid;
            grid-template-columns: 2fr 1.2fr;
            gap: 2rem;
        }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 1rem; color: #64748b; font-size: 0.85rem; border-bottom: 1px solid #f1f5f9; }
        td { padding: 1.25rem 1rem; font-size: 0.95rem; border-bottom: 1px solid #f8fafc; }

        .alert-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem;
            background: #fef2f2;
            border-radius: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #fee2e2;
        }

        @media (max-width: 1024px) {
            .analytics-row, .dashboard-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body style="margin:0; padding:0;">

<?php include __DIR__ . '/components/sidebar.php'; ?>

<main class="main-content" style="margin-left:220px;">
    <!-- Welcome -->
    <div class="welcome-section">
        <div class="welcome-text">
            <h1><?= $greeting ?>, <?= htmlspecialchars($_SESSION['user']) ?>!</h1>
            <p>Your business pulse at a glance.</p>
        </div>
        <div style="display:flex; gap:10px;">
            <div style="text-align:right;">
                <div style="font-weight:700; color:#1e293b;"><?= date('l, M d') ?></div>
                <div style="font-size:0.85rem; color:#64748b;">System status: <span style="color:var(--success); font-weight:700;">● Live</span></div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">Today's Revenue</div>
            <div class="value">रु <?= number_format($today_val, 2) ?></div>
            <div class="growth <?= ($diff >= 0) ? 'up' : 'down' ?>">
                <?= ($diff >= 0) ? '▲' : '▼' ?> <?= number_format(abs($perc), 1) ?>%
                <span style="opacity:0.7; font-weight:400; font-size:0.75rem;">vs yesterday</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="label">Total Orders</div>
            <div class="value"><?= $sales_stats['total_count'] ?? 0 ?></div>
            <div class="label" style="font-size:0.7rem;">Cumulative record</div>
        </div>
        <div class="stat-card">
            <div class="label">Inventory Items</div>
            <div class="value"><?= $product_count['total'] ?? 0 ?></div>
            <div class="label" style="font-size:0.7rem;">Active SKU count</div>
        </div>
        <div class="stat-card" style="border-top: 5px solid var(--danger);">
            <div class="label" style="color:var(--danger);">Urgent Alerts</div>
            <div class="value" style="color:var(--danger);"><?= $low_stock_count['total'] ?? 0 ?></div>
            <div class="label" style="font-size:0.7rem;">Low stock items</div>
        </div>
    </div>

    <!-- Analytics Charts -->
    <div class="analytics-row">
        <div class="chart-panel">
            <h3>📈 Sales Performance (Last 7 Days)</h3>
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        <div class="chart-panel">
            <h3>📊 Category Distribution</h3>
            <div class="chart-container">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <div style="display:flex; justify-content:space-between; align-items:center;">
             <h3 style="margin:0; font-weight:700;">Shortcuts</h3>
             <span style="font-size:0.8rem; opacity:0.6;">Quick management tools</span>
        </div>
        <div class="action-btns">
            <a href="sales.php" class="action-btn">
                <span>⚡</span> Recorded Sale
            </a>
            <a href="purchases.php" class="action-btn">
                <span>📦</span> Restock Items
            </a>
            <a href="reports.php" class="action-btn">
                <span>📊</span> Full Analytics
            </a>
        </div>
    </div>

    <div class="dashboard-row">
        <!-- Activity Log -->
        <div class="chart-panel">
            <h3>🕒 Recent Transactions <a href="sales.php" style="font-size:0.8rem; font-weight:400; color:var(--primary); text-decoration:none;">View All</a></h3>
            <table>
                <thead>
                    <tr>
                        <th>Transaction Date</th>
                        <th>Entity</th>
                        <th>Product</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($sale = mysqli_fetch_assoc($recent_sales)): ?>
                    <tr>
                        <td style="color:#64748b;"><?= date('M d, H:i', strtotime($sale['sales_date'])) ?></td>
                        <td><strong><?= htmlspecialchars($sale['customer_name']) ?></strong></td>
                        <td><?= htmlspecialchars($sale['product_name']) ?></td>
                        <td style="color:var(--success); font-weight:700;">रु <?= number_format($sale['total_price'], 2) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Stock Warnings -->
        <div class="chart-panel">
            <h3>⚠️ Inventory Warnings</h3>
            <?php while($item = mysqli_fetch_assoc($low_stock_products)): ?>
            <div class="alert-item">
                <div>
                    <strong style="display:block; color:#991b1b;"><?= htmlspecialchars($item['product_name']) ?></strong>
                    <span style="font-size:0.85rem; color:#b91c1c;">Stock: <?= $item['total_qty'] ?> left</span>
                </div>
                <a href="purchases.php" style="color:white; background:var(--danger); padding:0.5rem 0.75rem; border-radius:0.5rem; text-decoration:none; font-size:0.75rem; font-weight:700;">RESTOCK</a>
            </div>
            <?php endwhile; ?>
            <?php if(mysqli_num_rows($low_stock_products) == 0): ?>
            <div style="text-align:center; padding: 3rem 1rem;">
                <span style="font-size:3rem; display:block; margin-bottom:1rem;">✅</span>
                <p style="color:#64748b; font-weight:600;">Inventory level is optimal.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

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
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#2563eb'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
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
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
            },
            cutout: '70%'
        }
    });

    // Sidebar Responsiveness
    window.addEventListener('resize', () => {
        const main = document.querySelector('.main-content');
        if (window.innerWidth <= 900) { main.style.marginLeft = '0'; } 
        else { main.style.marginLeft = '220px'; }
    });
</script>

</body>
</html>
