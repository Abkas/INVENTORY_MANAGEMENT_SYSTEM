<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
    exit();
}
require_once __DIR__ . '/db/connect.php';

// Fetch warehouses with total stock counts
$query = "SELECT w.*, COALESCE(s.total_stock, 0) as total_stock 
          FROM warehouse w 
          LEFT JOIN (SELECT warehouse_id, SUM(quantity) as total_stock FROM stock GROUP BY warehouse_id) s 
          ON w.warehouse_id = s.warehouse_id 
          ORDER BY w.warehouse_id DESC";
$result = mysqli_query($conn, $query);
$warehouses = [];
while ($row = mysqli_fetch_assoc($result)) {
    $warehouses[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouses</title>
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
                <div class="header-title">Warehouses</div>
                <div class="header-sub">Manage your warehouses</div>
            </div>
            <button class="add-btn" onclick="document.getElementById('addWarehouseModal').style.display='flex'">Add Warehouse</button>
        </div>

        <!-- Feedback Messages -->
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="msg-success"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="msg-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <div class="warehouse-card-grid" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:25px;">
            <?php foreach ($warehouses as $warehouse): ?>
                <?php include __DIR__ . '/components/warehouse_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add Warehouse Modal -->
    <div id="addWarehouseModal" class="modal-bg" style="display:none;">
        <div class="modal-content modal-content">
            <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Add Warehouse</h2>
            <form method="POST" action="warehouse/add.php">
                <div class="modal-fields modal-fields">
                    <label class="modal-label">Warehouse Name
                        <input type="text" name="warehouse_name" placeholder="Warehouse name" required>
                    </label>
                    <label class="modal-label">Location
                        <input type="text" name="location" placeholder="Location">
                    </label>
                    <div class="modal-actions modal-actions">
                        <button type="button" class="modal-cancel modal-cancel" onclick="document.getElementById('addWarehouseModal').style.display='none'">Cancel</button>
                        <button type="submit" class="add-btn add-btn">Add</button>
                    </div>
                </div>
            </form>
            <button class="modal-close" onclick="document.getElementById('addWarehouseModal').style.display='none'">&times;</button>
        </div>
    </div>

    <!-- Edit Warehouse Modal -->
    <div id="editWarehouseModal" class="modal-bg" style="display:none;">
        <div class="modal-content modal-content">
            <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Edit Warehouse</h2>
            <form method="POST" action="warehouse/edit.php">
                <input type="hidden" name="warehouse_id" id="edit_warehouse_id">
                <div class="modal-fields modal-fields">
                    <label class="modal-label">Warehouse Name
                        <input type="text" name="warehouse_name" id="edit_warehouse_name" required>
                    </label>
                    <label class="modal-label">Location
                        <input type="text" name="location" id="edit_warehouse_location">
                    </label>
                    <div class="modal-actions modal-actions">
                        <button type="button" class="modal-cancel modal-cancel" onclick="document.getElementById('editWarehouseModal').style.display='none'">Cancel</button>
                        <button type="submit" class="add-btn add-btn">Update</button>
                    </div>
                </div>
            </form>
            <button class="modal-close" onclick="document.getElementById('editWarehouseModal').style.display='none'">&times;</button>
        </div>
    </div>

    <script>
    function openEditModal(id, name, location) {
        document.getElementById('edit_warehouse_id').value = id;
        document.getElementById('edit_warehouse_name').value = name;
        document.getElementById('edit_warehouse_location').value = location;
        document.getElementById('editWarehouseModal').style.display = 'block';
    }
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this warehouse? All stock associations must be empty.')) {
            window.location.href = 'warehouse/delete.php?id=' + id;
        }
    }
    </script>
</div>
</body>
</html>
