<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/FRONTEND/pages/login.html");
    exit();
}
require_once __DIR__ . '/db/connect.php';
// Handle add customer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_name'])) {
    $customer_name = trim($_POST['customer_name']);
    $customer_email = trim($_POST['customer_email']);
    $customer_phone = trim($_POST['customer_phone']);
    if ($customer_name !== '') {
        mysqli_query($conn, "INSERT INTO customer (customer_name, customer_email, customer_phone) VALUES ('$customer_name', '$customer_email', '$customer_phone')");
        header("Location: customers.php");
        exit();
    }
}
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
    <link rel="stylesheet" href="css/customers.css">
    <link rel="stylesheet" href="css/customer_card.css">
</head>
<body>
<div class="container">
    <?php include __DIR__ . '/components/sidebar.php'; ?>
    <div class="header">
        <div>
            <div class="header-title">Customers</div>
            <div class="header-sub">Manage your customer list</div>
        </div>
        <button class="add-btn" onclick="document.getElementById('addCustomerModal').style.display='block'">Add Customer</button>
    </div>

    <!-- Main Content Wrapper -->
    <div class="main-content">
        <div class="customer-card-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;">
            <?php foreach ($customers as $customer): ?>
                <?php include __DIR__ . '/components/customer_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div id="addCustomerModal" class="modal-bg">
        <div class="modal-content modal-content-spacious">
            <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Add Customer</h2>
            <form method="POST" action="customers.php">
                <div class="modal-fields modal-fields-spacious">
                    <label class="modal-label">Customer Name
                        <input type="text" name="customer_name" placeholder="Enter customer name" required>
                    </label>
                    <label class="modal-label">Email
                        <input type="text" name="customer_email" placeholder="Enter email">
                    </label>
                    <label class="modal-label">Phone
                        <input type="text" name="customer_phone" placeholder="Enter phone">
                    </label>
                    <div class="modal-actions modal-actions-spacious">
                        <button type="button" class="modal-cancel modal-cancel-spacious" onclick="document.getElementById('addCustomerModal').style.display='none'">Cancel</button>
                        <button type="submit" class="add-btn add-btn-spacious">Add</button>
                    </div>
                </div>
            </form>
            <button class="modal-close" onclick="document.getElementById('addCustomerModal').style.display='none'">&times;</button>
        </div>
    </div>
</div>
</body>
</html>
