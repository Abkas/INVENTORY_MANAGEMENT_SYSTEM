<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/FRONTEND/pages/login.html");
    exit();
}
require_once __DIR__ . '/db/connect.php';

// Handle add supplier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supplier_name'])) {
    $supplier_name = trim($_POST['supplier_name']);
    $supplier_email = trim($_POST['supplier_email']);
    $supplier_phone = trim($_POST['supplier_phone']);
    if ($supplier_name !== '') {
        mysqli_query($conn, "INSERT INTO supplier (supplier_name, supplier_email, supplier_phone) VALUES ('$supplier_name', '$supplier_email', '$supplier_phone')");
        header("Location: suppliers.php");
        exit();
    }
}
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
    <link rel="stylesheet" href="css/suppliers.css">
    <link rel="stylesheet" href="css/supplier_card.css">
</head>
<body>
<div class="container">
    <?php include __DIR__ . '/components/sidebar.php'; ?>
    <div class="header">
        <div>
            <div class="header-title">Suppliers</div>
            <div class="header-sub">Manage your supplier list</div>
        </div>
        <button class="add-btn" onclick="document.getElementById('addSupplierModal').style.display='block'">Add Supplier</button>
    </div>

    <!-- Main Content Wrapper -->
    <div class="main-content">
        <div class="supplier-card-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;">
            <?php foreach ($suppliers as $supplier): ?>
                <?php include __DIR__ . '/components/supplier_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add Supplier Modal -->
    <div id="addSupplierModal" class="modal-bg">
        <div class="modal-content modal-content-spacious">
            <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Add Supplier</h2>
            <form method="POST" action="suppliers.php">
                <div class="modal-fields modal-fields-spacious">
                    <label class="modal-label">Supplier Name
                        <input type="text" name="supplier_name" placeholder="Enter supplier name" required>
                    </label>
                    <label class="modal-label">Email
                        <input type="text" name="supplier_email" placeholder="Enter email">
                    </label>
                    <label class="modal-label">Phone
                        <input type="text" name="supplier_phone" placeholder="Enter phone">
                    </label>
                    <div class="modal-actions modal-actions-spacious">
                        <button type="button" class="modal-cancel modal-cancel-spacious" onclick="document.getElementById('addSupplierModal').style.display='none'">Cancel</button>
                        <button type="submit" class="add-btn add-btn-spacious">Add</button>
                    </div>
                </div>
            </form>
            <button class="modal-close" onclick="document.getElementById('addSupplierModal').style.display='none'">&times;</button>
        </div>
    </div>
</div>
</body>
</html>
