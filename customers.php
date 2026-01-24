<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: user/login.php");
    exit();
}
require_once __DIR__ . '/db/connect.php';

$result = mysqli_query($conn, "SELECT * FROM customer ORDER BY customer_id DESC");
$customers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $customers[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers</title>
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
                <div class="header-title">Customers</div>
                <div class="header-sub">Manage your customer list</div>
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
                <button class="add-btn" onclick="document.getElementById('addCustomerModal').style.display='flex'">Add Customer</button>
            </div>
        </div>

        <?php if (isset($_SESSION['msg'])): ?>
            <div class="msg-success"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
        <?php endif; ?>
        
        <div id="view-card" class="customer-card-grid responsive-grid">
            <?php foreach ($customers as $customer): ?>
                <?php include __DIR__ . '/components/customer_card.php'; ?>
            <?php endforeach; ?>
        </div>

        <div id="view-table" class="table-container" style="display:none;">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Customer Name</th>
                        <th style="width: 30%;">Email</th>
                        <th style="width: 30%;">Phone</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td>
                            <div style="font-weight:600; color:var(--text-main);"><?= htmlspecialchars($customer['customer_name']) ?></div>
                        </td>
                        <td>
                            <div style="color:var(--text-sub); display:flex; align-items:center; gap:6px;">
                                <i data-lucide="mail" style="width:14px;"></i>
                                <?= htmlspecialchars($customer['customer_email']) ?>
                            </div>
                        </td>
                        <td>
                            <div style="color:var(--text-sub); display:flex; align-items:center; gap:6px;">
                                <i data-lucide="phone" style="width:14px;"></i>
                                <?= htmlspecialchars($customer['customer_phone']) ?>
                            </div>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:inline-flex; gap:8px;">
                                <a href="customer/profile.php?id=<?= $customer['customer_id'] ?>" class="action-btn" title="View Profile" style="background:#f0fdf4; color:#166534; text-decoration:none;">
                                    <i data-lucide="eye" style="width:16px;"></i>
                                </a>
                                <button class="action-btn" title="Edit" onclick="openEditModal(<?= $customer['customer_id'] ?>, '<?= addslashes($customer['customer_name']) ?>', '<?= addslashes($customer['customer_email']) ?>', '<?= addslashes($customer['customer_phone']) ?>')" style="background:#eff6ff; color:#2563eb; border:none; width:32px; height:32px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                                    <i data-lucide="edit-2" style="width:16px;"></i>
                                </button>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <button class="action-btn" title="Delete" onclick="confirmDelete(<?= $customer['customer_id'] ?>)" style="background:#fee2e2; color:#dc2626; border:none; width:32px; height:32px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
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

    <div id="addCustomerModal" class="modal-bg" style="display:none;">
        <div class="modal-content modal-content">
            <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Add Customer</h2>
            <form method="POST" action="customer/add.php">
                <div class="modal-fields modal-fields">
                    <label class="modal-label">Customer Name
                        <input type="text" name="customer_name" placeholder="Enter customer name" required>
                    </label>
                    <label class="modal-label">Email
                        <input type="email" name="customer_email" placeholder="Enter email">
                    </label>
                    <label class="modal-label">Phone
                        <input type="text" name="customer_phone" placeholder="Enter phone">
                    </label>
                    <div class="modal-actions modal-actions">
                        <button type="button" class="modal-cancel modal-cancel" onclick="document.getElementById('addCustomerModal').style.display='none'">Cancel</button>
                        <button type="submit" class="add-btn add-btn">Add</button>
                    </div>
                </div>
            </form>
            <button class="modal-close" onclick="document.getElementById('addCustomerModal').style.display='none'">&times;</button>
        </div>
    </div>

    <div id="editCustomerModal" class="modal-bg" style="display:none;">
        <div class="modal-content modal-content">
            <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Edit Customer</h2>
            <form method="POST" action="customer/edit.php">
                <input type="hidden" name="customer_id" id="edit_customer_id">
                <div class="modal-fields modal-fields">
                    <label class="modal-label">Customer Name
                        <input type="text" name="customer_name" id="edit_customer_name" required>
                    </label>
                    <label class="modal-label">Email
                        <input type="email" name="customer_email" id="edit_customer_email">
                    </label>
                    <label class="modal-label">Phone
                        <input type="text" name="customer_phone" id="edit_customer_phone">
                    </label>
                    <div class="modal-actions modal-actions">
                        <button type="button" class="modal-cancel modal-cancel" onclick="document.getElementById('editCustomerModal').style.display='none'">Cancel</button>
                        <button type="submit" class="add-btn add-btn">Update</button>
                    </div>
                </div>
            </form>
            <button class="modal-close" onclick="document.getElementById('editCustomerModal').style.display='none'">&times;</button>
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

    document.addEventListener('DOMContentLoaded', () => {
        if(window.lucide) lucide.createIcons();
    });

    function openEditModal(id, name, email, phone) {
        document.getElementById('edit_customer_id').value = id;
        document.getElementById('edit_customer_name').value = name;
        document.getElementById('edit_customer_email').value = email;
        document.getElementById('edit_customer_phone').value = phone;
        document.getElementById('editCustomerModal').style.display = 'flex';
    }
    function confirmDelete(id) {
        showConfirmModal({
            title: 'Delete Customer?',
            message: 'Are you sure you want to delete this customer? Their purchase history will be affected.',
            icon: '🗑️',
            iconType: 'danger',
            confirmText: 'Yes, Delete',
            confirmClass: 'confirm',
            onConfirm: () => {
                window.location.href = 'customer/delete.php?id=' + id;
            }
        });
    }
    </script>
</div>
</body>
</html>
