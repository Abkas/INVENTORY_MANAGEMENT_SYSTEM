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
    <link rel="stylesheet" href="css/suppliers.css">
    <link rel="stylesheet" href="css/supplier_card.css">
    <link rel="stylesheet" href="css/categories.css"> <!-- Reusing modal styles -->
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

    <?php if (isset($_SESSION['msg'])): ?>
        <div style="background:#dcfce7;color:#166534;padding:12px;border-radius:8px;margin-bottom:20px;"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

    <!-- Main Content Wrapper -->
    <div class="main-content">
        <div class="supplier-card-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;">
            <?php foreach ($suppliers as $supplier): ?>
                <?php include __DIR__ . '/components/supplier_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add Supplier Modal -->
    <div id="addSupplierModal" class="modal-bg" style="display:none;">
        <div class="modal-content modal-content-spacious">
            <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Add Supplier</h2>
            <form method="POST" action="supplier/add.php">
                <div class="modal-fields modal-fields-spacious">
                    <label class="modal-label">Supplier Name
                        <input type="text" name="supplier_name" placeholder="Enter supplier name" required>
                    </label>
                    <label class="modal-label">Email
                        <input type="email" name="supplier_email" placeholder="Enter email">
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

    <!-- Edit Supplier Modal -->
    <div id="editSupplierModal" class="modal-bg" style="display:none;">
        <div class="modal-content modal-content-spacious">
            <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Edit Supplier</h2>
            <form method="POST" action="supplier/edit.php">
                <input type="hidden" name="supplier_id" id="edit_supplier_id">
                <div class="modal-fields modal-fields-spacious">
                    <label class="modal-label">Supplier Name
                        <input type="text" name="supplier_name" id="edit_supplier_name" required>
                    </label>
                    <label class="modal-label">Email
                        <input type="email" name="supplier_email" id="edit_supplier_email">
                    </label>
                    <label class="modal-label">Phone
                        <input type="text" name="supplier_phone" id="edit_supplier_phone">
                    </label>
                    <div class="modal-actions modal-actions-spacious">
                        <button type="button" class="modal-cancel modal-cancel-spacious" onclick="document.getElementById('editSupplierModal').style.display='none'">Cancel</button>
                        <button type="submit" class="add-btn add-btn-spacious">Update</button>
                    </div>
                </div>
            </form>
            <button class="modal-close" onclick="document.getElementById('editSupplierModal').style.display='none'">&times;</button>
        </div>
    </div>

    <script>
    function openEditModal(id, name, email, phone) {
        document.getElementById('edit_supplier_id').value = id;
        document.getElementById('edit_supplier_name').value = name;
        document.getElementById('edit_supplier_email').value = email;
        document.getElementById('edit_supplier_phone').value = phone;
        document.getElementById('editSupplierModal').style.display = 'block';
    }
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this supplier?')) {
            window.location.href = 'supplier/delete.php?id=' + id;
        }
    }
    </script>
</div>
</body>
</html>
