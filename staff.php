<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: user/login.php");
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=Access Denied");
    exit();
}

require_once __DIR__ . '/db/connect.php';

$query = "
    SELECT 
        u.*,
        COALESCE(s.sales_count, 0) as sales_count,
        COALESCE(s.sales_total, 0) as sales_total,
        COALESCE(p.purchase_count, 0) as purchase_count,
        COALESCE(p.purchase_total, 0) as purchase_total
    FROM user u
    LEFT JOIN (
        SELECT user_id, COUNT(*) as sales_count, SUM(total_price) as sales_total 
        FROM sales 
        GROUP BY user_id
    ) s ON u.user_id = s.user_id
    LEFT JOIN (
        SELECT user_id, COUNT(*) as purchase_count, SUM(total_price) as purchase_total 
        FROM purchase 
        GROUP BY user_id
    ) p ON u.user_id = p.user_id
    ORDER BY u.user_id ASC
";
$result = mysqli_query($conn, $query);
$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff</title>
    <link rel="stylesheet" href="css/global.css?v=<?= time() ?>">
    <link rel="stylesheet" href="css/shared_cards.css">
    <link rel="stylesheet" href="css/confirm_modal.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="js/confirm_modal.js"></script>
</head>
<body>
<div class="container">
    <?php include __DIR__ . '/components/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <div>
                <div class="header-title">Manage Staff</div>
                <div class="header-sub">View all users and their activity</div>
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
            </div>
        </div>

        <?php if (isset($_SESSION['msg'])): ?>
            <div class="msg-success"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="msg-error" style="background:#fee2e2; color:#991b1b; padding:1rem; border-radius:8px; margin-bottom:1.5rem;">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div id="view-card" class="responsive-grid">
            <?php foreach ($users as $user): ?>
                <?php include __DIR__ . '/components/staff_card.php'; ?>
            <?php endforeach; ?>
        </div>

        <div id="view-table" class="table-container" style="display:none;">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Username</th>
                        <th>Role</th>
                        <th style="text-align:right;">Sales</th>
                        <th style="text-align:right;">Purchases</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <div style="font-weight:600; color:var(--text-main);"><?= htmlspecialchars($user['username']) ?></div>
                        </td>
                        <td>
                            <?php if ($user['role'] === 'admin'): ?>
                                <span style="background:#fef3c7; color:#92400e; padding:4px 10px; border-radius:12px; font-weight:600; font-size:0.85rem;">
                                    Admin
                                </span>
                            <?php else: ?>
                                <span style="background:#e0f2fe; color:#0369a1; padding:4px 10px; border-radius:12px; font-weight:600; font-size:0.85rem;">
                                    Staff
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:right;">
                            <div style="font-weight:600; color:var(--text-main);"><?= $user['sales_count'] ?> sales</div>
                            <div style="font-size:0.85rem; color:var(--text-sub);">रु <?= number_format($user['sales_total'], 0) ?></div>
                        </td>
                        <td style="text-align:right;">
                            <div style="font-weight:600; color:var(--text-main);"><?= $user['purchase_count'] ?> purchases</div>
                            <div style="font-size:0.85rem; color:var(--text-sub);">रु <?= number_format($user['purchase_total'], 0) ?></div>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:inline-flex; gap:8px;">
                                <a href="staff/profile.php?id=<?= $user['user_id'] ?>" class="action-btn" title="View Profile" style="background:#eff6ff; color:#2563eb; text-decoration:none;">
                                    <i data-lucide="eye" style="width:16px;"></i>
                                </a>
                                <?php if ($user['user_id'] !== $_SESSION['user_id']): ?>
                                <button class="action-btn" title="Delete Staff" onclick="confirmDelete(<?= $user['user_id'] ?>, '<?= addslashes($user['username']) ?>')" style="background:#fee2e2; color:#dc2626; border:none; width:32px; height:32px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                                    <i data-lucide="trash-2" style="width:16px;"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
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

function confirmDelete(id, username) {
    showConfirmModal({
        title: 'Delete Staff Member?',
        message: `Are you sure you want to delete <strong>${username}</strong>? All their activity records will remain but they won't be able to log in anymore.`,
        icon: '🗑️',
        iconType: 'danger',
        confirmText: 'Yes, Delete',
        confirmClass: 'confirm',
        onConfirm: () => {
            window.location.href = 'staff/delete.php?id=' + id;
        }
    });
}

if(window.lucide) lucide.createIcons();
</script>
</body>
</html>
