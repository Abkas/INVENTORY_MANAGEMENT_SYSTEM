<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
    exit();
}
require_once __DIR__ . '/db/connect.php';

// Fetch suppliers
$result = mysqli_query($conn, "SELECT * FROM supplier ORDER BY supplier_id DESC");
$suppliers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $suppliers[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppliers</title>
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
                <div class="header-title">Suppliers</div>
                <div class="header-sub">Manage your supplier list</div>
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
                <button class="add-btn" onclick="document.getElementById('addSupplierModal').style.display='flex'">Add Supplier</button>
            </div>
        </div>

        <?php if (isset($_SESSION['msg'])): ?>
            <div class="msg-success"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
        <?php endif; ?>
        
        <!-- Card View -->
        <div id="view-card" class="supplier-card-grid responsive-grid">
            <?php foreach ($suppliers as $supplier): ?>
                <?php include __DIR__ . '/components/supplier_card.php'; ?>
            <?php endforeach; ?>
        </div>

        <!-- Table View -->
        <div id="view-table" class="table-container" style="display:none;">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Supplier Name</th>
                        <th style="width: 30%;">Email</th>
                        <th style="width: 30%;">Phone</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suppliers as $supplier): ?>
                    <tr>
                        <td>
                            <div style="font-weight:600; color:var(--text-main);"><?= htmlspecialchars($supplier['supplier_name']) ?></div>
                        </td>
                        <td>
                            <div style="color:var(--text-sub); display:flex; align-items:center; gap:6px;">
                                <i data-lucide="mail" style="width:14px;"></i>
                                <?= htmlspecialchars($supplier['supplier_email']) ?>
                            </div>
                        </td>
                        <td>
                            <div style="color:var(--text-sub); display:flex; align-items:center; gap:6px;">
                                <i data-lucide="phone" style="width:14px;"></i>
                                <?= htmlspecialchars($supplier['supplier_phone']) ?>
                            </div>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:inline-flex; gap:8px;">
                                <button class="action-btn" title="Edit" onclick="openEditModal(<?= $supplier['supplier_id'] ?>, '<?= addslashes($supplier['supplier_name']) ?>', '<?= addslashes($supplier['supplier_email']) ?>', '<?= addslashes($supplier['supplier_phone']) ?>')" style="background:#eff6ff; color:#2563eb; border:none; width:32px; height:32px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                                    <i data-lucide="edit-2" style="width:16px;"></i>
                                </button>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <button class="action-btn" title="Delete" onclick="confirmDelete(<?= $supplier['supplier_id'] ?>)" style="background:#fee2e2; color:#dc2626; border:none; width:32px; height:32px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
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

    <!-- Add Supplier Modal -->
    <div id="addSupplierModal" class="modal-bg" style="display:none;">
        <div class="modal-content modal-content">
            <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Add Supplier</h2>
            <form method="POST" action="supplier/add.php">
                <div class="modal-fields modal-fields">
                    <label class="modal-label">Supplier Name
                        <input type="text" name="supplier_name" placeholder="Enter supplier name" required>
                    </label>
                    <label class="modal-label">Email
                        <input type="email" name="supplier_email" placeholder="Enter email">
                    </label>
                    <label class="modal-label">Phone
                        <input type="text" name="supplier_phone" placeholder="Enter phone">
                    </label>
                    <div class="modal-actions modal-actions">
                        <button type="button" class="modal-cancel modal-cancel" onclick="document.getElementById('addSupplierModal').style.display='none'">Cancel</button>
                        <button type="submit" class="add-btn add-btn">Add</button>
                    </div>
                </div>
            </form>
            <button class="modal-close" onclick="document.getElementById('addSupplierModal').style.display='none'">&times;</button>
        </div>
    </div>

    <!-- Edit Supplier Modal -->
    <div id="editSupplierModal" class="modal-bg" style="display:none;">
        <div class="modal-content modal-content">
            <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Edit Supplier</h2>
            <form method="POST" action="supplier/edit.php">
                <input type="hidden" name="supplier_id" id="edit_supplier_id">
                <div class="modal-fields modal-fields">
                    <label class="modal-label">Supplier Name
                        <input type="text" name="supplier_name" id="edit_supplier_name" required>
                    </label>
                    <label class="modal-label">Email
                        <input type="email" name="supplier_email" id="edit_supplier_email">
                    </label>
                    <label class="modal-label">Phone
                        <input type="text" name="supplier_phone" id="edit_supplier_phone">
                    </label>
                    <div class="modal-actions modal-actions">
                        <button type="button" class="modal-cancel modal-cancel" onclick="document.getElementById('editSupplierModal').style.display='none'">Cancel</button>
                        <button type="submit" class="add-btn add-btn">Update</button>
                    </div>
                </div>
            </form>
            <button class="modal-close" onclick="document.getElementById('editSupplierModal').style.display='none'">&times;</button>
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

    // Initialize icons
    document.addEventListener('DOMContentLoaded', () => {
        if(window.lucide) lucide.createIcons();
    });

    function openEditModal(id, name, email, phone) {
        document.getElementById('edit_supplier_id').value = id;
        document.getElementById('edit_supplier_name').value = name;
        document.getElementById('edit_supplier_email').value = email;
        document.getElementById('edit_supplier_phone').value = phone;
        document.getElementById('editSupplierModal').style.display = 'flex';
    }
    function confirmDelete(id) {
        showConfirmModal({
            title: 'Delete Supplier?',
            message: 'Are you sure you want to delete this supplier? Products from this supplier will be affected.',
            icon: '🗑️',
            iconType: 'danger',
            confirmText: 'Yes, Delete',
            confirmClass: 'confirm',
            onConfirm: () => {
                window.location.href = 'supplier/delete.php?id=' + id;
            }
        });
    }
    </script>
</div>
</body>
</html>
