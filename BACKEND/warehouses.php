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
                <button class="add-btn" onclick="document.getElementById('addWarehouseModal').style.display='flex'">Add Warehouse</button>
            </div>
        </div>

        <!-- Feedback Messages -->
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="msg-success"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="msg-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <!-- Card View -->
        <div id="view-card" class="warehouse-card-grid responsive-grid">
            <?php foreach ($warehouses as $warehouse): ?>
                <?php include __DIR__ . '/components/warehouse_card.php'; ?>
            <?php endforeach; ?>
        </div>

        <!-- Table View -->
        <div id="view-table" class="table-container" style="display:none;">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Warehouse Name</th>
                        <th style="width: 30%;">Location</th>
                        <th style="text-align:right;">Total Stock</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($warehouses as $warehouse): ?>
                    <tr>
                        <td>
                            <div style="font-weight:600; color:var(--text-main);"><?= htmlspecialchars($warehouse['warehouse_name']) ?></div>
                        </td>
                        <td>
                            <div style="color:var(--text-sub); display:flex; align-items:center; gap:6px;">
                                <i data-lucide="map-pin" style="width:14px;"></i>
                                <?= htmlspecialchars($warehouse['location']) ?>
                            </div>
                        </td>
                        <td style="text-align:right;">
                            <span style="background:#f0f9ff; color:#0369a1; padding:4px 10px; border-radius:6px; font-weight:700; font-size:0.9rem;">
                                <?= number_format($warehouse['total_stock']) ?> units
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:inline-flex; gap:8px;">
                                <button class="action-btn" title="Edit" onclick="openEditModal(<?= $warehouse['warehouse_id'] ?>, '<?= addslashes($warehouse['warehouse_name']) ?>', '<?= addslashes($warehouse['location']) ?>')" style="background:#eff6ff; color:#2563eb; border:none; width:32px; height:32px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                                    <i data-lucide="edit-2" style="width:16px;"></i>
                                </button>
                                <button class="action-btn" title="Delete" onclick="confirmDelete(<?= $warehouse['warehouse_id'] ?>)" style="background:#fee2e2; color:#dc2626; border:none; width:32px; height:32px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                                    <i data-lucide="trash-2" style="width:16px;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
        </script>
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
        document.getElementById('editWarehouseModal').style.display = 'flex';
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
