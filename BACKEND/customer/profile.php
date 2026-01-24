<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if (!isset($_GET['id'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/customers.php");
    exit();
}

$customer_id = intval($_GET['id']);

// Fetch customer details
$cust_query = "SELECT * FROM customer WHERE customer_id = $customer_id";
$cust_result = mysqli_query($conn, $cust_query);
$customer = mysqli_fetch_assoc($cust_result);

if (!$customer) {
    die("Customer not found.");
}

// Fetch sales history
$sales_query = "
    SELECT s.*, p.product_name 
    FROM sales s 
    JOIN product p ON s.product_id = p.product_id 
    WHERE s.customer_id = $customer_id 
    ORDER BY s.sales_date DESC
";
$sales_result = mysqli_query($conn, $sales_query);
$sales = [];
$total_spent = 0;
while ($row = mysqli_fetch_assoc($sales_result)) {
    $sales[] = $row;
    $total_spent += $row['total_price'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profile</title>
    <link rel="stylesheet" href="../css/global.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../css/shared_cards.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
<div class="container">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <div>
                <a href="/INVENTORY_SYSTEM/BACKEND/customers.php" style="text-decoration:none; color:var(--text-sub); display:inline-flex; align-items:center; gap:4px; font-size:0.9rem; margin-bottom:4px;">
                    <i data-lucide="arrow-left" style="width:14px;"></i> Back to Customers
                </a>
                <div class="header-title"><?= htmlspecialchars($customer['customer_name']) ?></div>
                <div class="header-sub">Customer Profile & History</div>
            </div>
            <!-- Future: Add Edit Button Here -->
        </div>

        <div class="responsive-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); margin-bottom: 2rem;">
            <!-- Info Card -->
            <div class="premium-card" style="min-height: auto;">
                <div style="display:flex; gap:16px; align-items:center;">
                    <div class="icon-box" style="margin-bottom:0; background:#eff6ff; color:#2563eb;">
                        <i data-lucide="user" style="width:24px;"></i>
                    </div>
                    <div>
                        <div style="font-size:0.85rem; color:var(--text-sub);">Contact Info</div>
                        <div style="font-weight:600; font-size:1.1rem; color:var(--text-main); margin-top:2px;">
                            <?= htmlspecialchars($customer['customer_email'] ?: 'No Email') ?>
                        </div>
                        <div style="font-size:0.9rem; color:var(--text-sub);">
                            <?= htmlspecialchars($customer['customer_phone'] ?: 'No Phone') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="premium-card" style="min-height: auto;">
                <div style="display:flex; gap:16px; align-items:center;">
                    <div class="icon-box" style="margin-bottom:0; background:#dcfce7; color:#166534;">
                        <i data-lucide="wallet" style="width:24px;"></i>
                    </div>
                    <div>
                        <div style="font-size:0.85rem; color:var(--text-sub);">Total Spent</div>
                        <div style="font-weight:700; font-size:1.5rem; color:var(--text-main); margin-top:2px;">
                            रु <?= number_format($total_spent, 2) ?>
                        </div>
                        <div style="font-size:0.85rem; color:var(--success);">
                            <?= count($sales) ?> transactions
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h3 style="margin-bottom:1.5rem; color:var(--text-main);">Purchase History</h3>

        <div class="table-container">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">Date</th>
                        <th style="width: 40%;">Product</th>
                        <th style="text-align:right;">Qty</th>
                        <th style="text-align:right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td style="color:var(--text-sub); font-weight:500;">
                            <?= date('M d, Y', strtotime($sale['sales_date'])) ?>
                        </td>
                        <td>
                            <div style="font-weight:600; color:var(--text-main);"><?= htmlspecialchars($sale['product_name']) ?></div>
                        </td>
                        <td style="text-align:right; font-weight:600; color:var(--text-main);">
                            <?= $sale['quantity'] ?>
                        </td>
                        <td style="text-align:right;">
                            <span style="background:#ecfdf5; color:#059669; padding:4px 8px; border-radius:6px; font-weight:700; font-size:0.9rem;">
                                रु <?= number_format($sale['total_price'], 2) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($sales)): ?>
                    <tr><td colspan="4" style="padding:3rem; text-align:center; color:var(--text-sub);">No purchases yet.</td></tr>
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
