<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/FRONTEND/pages/login.html");
    exit();
}
require_once __DIR__ . '/db/connect.php';
// Handle add warehouse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['warehouse_name'], $_POST['location'])) {
    $warehouse_name = trim($_POST['warehouse_name']);
    $location = trim($_POST['location']);
    if ($warehouse_name !== '') {
        mysqli_query($conn, "INSERT INTO warehouse (warehouse_name, location) VALUES ('$warehouse_name', '$location')");
        header("Location: warehouses.php");
        exit();
    }
}
$result = mysqli_query($conn, "SELECT * FROM warehouse ORDER BY warehouse_id DESC");
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
    <link rel="stylesheet" href="css/warehouse.css">
    <link rel="stylesheet" href="css/warehouse_card.css">
</head>
<body>
<div class="container">
    <?php include __DIR__ . '/components/sidebar.php'; ?>
    <div class="header">
        <div>
            <div class="header-title">Warehouses</div>
            <div class="header-sub">Manage your warehouses</div>
        </div>
        <button class="add-btn" onclick="document.getElementById('addWarehouseModal').style.display='block'">Add Warehouse</button>
    </div>

    <!-- Main Content Wrapper -->
    <div class="main-content">
        <div class="warehouse-card-grid">
            <?php foreach ($warehouses as $warehouse): ?>
                <?php include __DIR__ . '/components/warehouse_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add Warehouse Modal -->
    <div id="addWarehouseModal" class="modal-bg">
        <div class="modal-content">
            <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Add Warehouse</h2>
            <form method="POST" action="warehouses.php">
                <div class="modal-fields">
                    <label class="modal-label">Warehouse Name
                        <input type="text" name="warehouse_name" placeholder="Warehouse name" required>
                    </label>
                    <label class="modal-label">Location
                        <input type="text" name="location" placeholder="Location">
                    </label>
                    <div class="modal-actions">
                        <button type="button" class="modal-cancel" onclick="document.getElementById('addWarehouseModal').style.display='none'">Cancel</button>
                        <button type="submit" class="add-btn">Add</button>
                    </div>
                </div>
            </form>
            <button class="modal-close" onclick="document.getElementById('addWarehouseModal').style.display='none'">&times;</button>
        </div>
    </div>
</div>
</body>
</html>
