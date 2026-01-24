<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../user/login.php");
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php?error=Access Denied");
    exit();
}

require_once __DIR__ . '/../db/connect.php';

if (!isset($_GET['id'])) {
    header("Location: ../staff.php");
    exit();
}

$user_id = intval($_GET['id']);

$user_query = "SELECT * FROM user WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

if (!$user) {
    die("User not found.");
}

$activity_query = "
    (SELECT 
        'SALE' as type,
        s.sales_date as date,
        c.customer_name as related_to,
        p.product_name as item,
        s.quantity,
        s.total_price as amount
    FROM sales s 
    JOIN product p ON s.product_id = p.product_id 
    JOIN customer c ON s.customer_id = c.customer_id 
    WHERE s.user_id = $user_id)
    
    UNION ALL
    
    (SELECT 
        'PURCHASE' as type,
        pur.purchase_date as date,
        sup.supplier_name as related_to,
        p.product_name as item,
        pur.quantity,
        pur.total_price as amount
    FROM purchase pur
    JOIN product p ON pur.product_id = p.product_id 
    JOIN supplier sup ON pur.supplier_id = sup.supplier_id 
    WHERE pur.user_id = $user_id)
    
    ORDER BY date DESC
";
$activity_result = mysqli_query($conn, $activity_query);
$activities = [];
$total_sales = 0;
$total_purchases = 0;
$sales_count = 0;
$purchase_count = 0;

while ($row = mysqli_fetch_assoc($activity_result)) {
    $activities[] = $row;
    if ($row['type'] === 'SALE') {
        $total_sales += $row['amount'];
        $sales_count++;
    } else {
        $total_purchases += $row['amount'];
        $purchase_count++;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Profile</title>
    <link rel="stylesheet" href="../css/global.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../css/shared_cards.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
<div class="container">
    <?php $path_prefix = '../'; include __DIR__ . '/../components/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <div>
                <a href="../staff.php" style="text-decoration:none; color:var(--text-sub); display:inline-flex; align-items:center; gap:4px; font-size:0.9rem; margin-bottom:4px;">
                    <i data-lucide="arrow-left" style="width:14px;"></i> Back to Staff
                </a>
                <div class="header-title"><?= htmlspecialchars($user['username']) ?></div>
                <div class="header-sub">
                    <?= $user['role'] === 'admin' ? '👑 Administrator' : '👤 Staff Member' ?>
                </div>
            </div>
        </div>

        <div class="responsive-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); margin-bottom: 2rem;">
            <div class="premium-card" style="min-height: auto;">
                <div style="display:flex; gap:16px; align-items:center;">
                    <div class="icon-box" style="margin-bottom:0; background:#dcfce7; color:#166534;">
                        <i data-lucide="trending-up" style="width:24px;"></i>
                    </div>
                    <div>
                        <div style="font-size:0.85rem; color:var(--text-sub);">Total Sales</div>
                        <div style="font-weight:700; font-size:1.5rem; color:var(--text-main); margin-top:2px;">
                            रु <?= number_format($total_sales, 2) ?>
                        </div>
                        <div style="font-size:0.85rem; color:var(--success);">
                            <?= $sales_count ?> transactions
                        </div>
                    </div>
                </div>
            </div>

            <div class="premium-card" style="min-height: auto;">
                <div style="display:flex; gap:16px; align-items:center;">
                    <div class="icon-box" style="margin-bottom:0; background:#fff7ed; color:#c2410c;">
                        <i data-lucide="shopping-cart" style="width:24px;"></i>
                    </div>
                    <div>
                        <div style="font-size:0.85rem; color:var(--text-sub);">Total Purchases</div>
                        <div style="font-weight:700; font-size:1.5rem; color:var(--text-main); margin-top:2px;">
                            रु <?= number_format($total_purchases, 2) ?>
                        </div>
                        <div style="font-size:0.85rem; color:var(--danger);">
                            <?= $purchase_count ?> orders
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h3 style="margin-bottom:1.5rem; color:var(--text-main);">Activity Log</h3>

        <div class="table-container">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Date</th>
                        <th style="width: 25%;">Customer/Supplier</th>
                        <th style="width: 30%;">Item</th>
                        <th>Type</th>
                        <th style="text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activities as $activity): 
                        $is_sale = $activity['type'] === 'SALE';
                    ?>
                    <tr>
                        <td style="color:var(--text-sub); font-weight:500;">
                            <?= date('M d, Y', strtotime($activity['date'])) ?>
                        </td>
                        <td>
                            <div style="font-weight:600; color:var(--text-main);"><?= htmlspecialchars($activity['related_to']) ?></div>
                            <div style="font-size:0.75rem; color:var(--text-sub);">
                                <?= $is_sale ? 'Customer' : 'Supplier' ?>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight:500; color:var(--text-main);"><?= htmlspecialchars($activity['item']) ?></div>
                            <small style="color:var(--text-sub);">Qty: <?= $activity['quantity'] ?></small>
                        </td>
                        <td>
                            <?php if($is_sale): ?>
                                <span style="background:#ecfdf5; color:#059669; padding:0.25rem 0.5rem; border-radius:4px; font-size:0.7rem; font-weight:700;">SALE</span>
                            <?php else: ?>
                                <span style="background:#fff7ed; color:#c2410c; padding:0.25rem 0.5rem; border-radius:4px; font-size:0.7rem; font-weight:700;">PURCHASE</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:right;">
                            <span style="background:<?= $is_sale ? '#ecfdf5' : '#fff7ed' ?>; color:<?= $is_sale ? '#059669' : '#c2410c' ?>; padding:4px 8px; border-radius:6px; font-weight:700; font-size:0.9rem;">
                                <?= $is_sale ? '+' : '-' ?> रु <?= number_format($activity['amount'], 2) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($activities)): ?>
                    <tr><td colspan="5" style="padding:3rem; text-align:center; color:var(--text-sub);">No activity yet.</td></tr>
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
