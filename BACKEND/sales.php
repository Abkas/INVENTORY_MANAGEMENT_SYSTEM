<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/FRONTEND/pages/login.html");
    exit();
}
require_once __DIR__ . '/db/connect.php';
// Handle add sales
// Handle add sales with stock check
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sales_date'], $_POST['quantity'], $_POST['total_price'], $_POST['customer_id'], $_POST['product_id'])) {
    
    $sales_date = trim($_POST['sales_date']);
    $quantity = intval($_POST['quantity']);
    $total_price = trim($_POST['total_price']);
    $customer_id = intval($_POST['customer_id']);
    $product_id = intval($_POST['product_id']);
    $user_id = $_SESSION['user_id'];

    if ($sales_date !== '' && $quantity > 0 && $total_price !== '' && $customer_id > 0 && $product_id > 0) {
        // Check stock for this product (sum across all warehouses)
        
        $stock_result = mysqli_query(
        $conn,
        "SELECT SUM(quantity) as total_stock 
        FROM stock 
        WHERE product_id = $product_id"
        );

        $stock_row = mysqli_fetch_assoc($stock_result);

        //change stock row in integer if no stock row then change it to 0
        $total_stock = $stock_row ? intval($stock_row['total_stock']) : 0;

        if ($total_stock < $quantity) {
            $error = 'Not enough stock available!';
        } else {

            // Insert sale
            mysqli_query(
                $conn, 
                "INSERT INTO sales (sales_date, quantity, total_price, customer_id, product_id, user_id) 
                VALUES ('$sales_date', '$quantity', '$total_price', '$customer_id', '$product_id', '$user_id')"
                );


            // Reduce stock (from any warehouse, prioritizing those with most stock)
            $qty_to_reduce = $quantity;

            $stock_q = mysqli_query(
                $conn,
                "SELECT * FROM stock 
                WHERE product_id = $product_id AND quantity > 0 
                ORDER BY quantity DESC" //order by quantity on descending order
                );

            while ($qty_to_reduce > 0 && ($row = mysqli_fetch_assoc($stock_q))) {
                $reduce = min($row['quantity'], $qty_to_reduce); //chooses the smaller value between (row['quantity], qty_to_reduce)
                $new_qty = $row['quantity'] - $reduce;

            mysqli_query(
                $conn,
                "UPDATE stock SET quantity = $new_qty 
                WHERE stock_id = {$row['stock_id']}"
            );

                $qty_to_reduce -= $reduce;
            }
            header("Location: sales.php");
            exit();
        }
    }
}
// Fetch customers
// Fetch customers
$cus_result = mysqli_query(
    $conn, 
    "SELECT * FROM customer ORDER BY customer_name ASC"
);
if (!$cus_result) { die('Customer query error: ' . mysqli_error($conn)); }
$customers = [];
while ($row = mysqli_fetch_assoc($cus_result)) {
    $customers[] = $row;
}
// Fetch products
$prod_result = mysqli_query(
    $conn, 
    "SELECT * FROM product ORDER BY product_name ASC"
);
$products = [];
while ($row = mysqli_fetch_assoc($prod_result)) {
    $products[] = $row;
}
// Fetch sales (with customer and product name)
$sales_result = mysqli_query(
    $conn, 
    "SELECT s.*, c.customer_name, p.product_name FROM sales s JOIN customer c ON s.customer_id = c.customer_id JOIN product p ON s.product_id = p.product_id ORDER BY s.sales_id DESC"
);
$sales = [];
while ($row = mysqli_fetch_assoc($sales_result)) {
    $sales[] = $row;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales</title>
    <link rel="stylesheet" href="css/sales.css">
    <link rel="stylesheet" href="css/sales_card.css">
</head>
<body>
<div class="container">
    <?php include __DIR__ . '/components/sidebar.php'; ?>
    <div class="header">
        <div>
            <div class="header-title">Sales</div>
            <div class="header-sub">Manage your sales records</div>
        </div
            <button class="add-btn" onclick="document.getElementById('addSalesModal').style.display='block'">Add Sale</button>
        </div>

        <!-- Main Content Wrapper -->
        <div class="main-content">
            <div class="sales-card-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;">
                <?php foreach ($sales as $sale): ?>
                    <?php include __DIR__ . '/components/sales_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Add Sales Modal -->
        <div id="addSalesModal" class="modal-bg">
            <div class="modal-content modal-content-spacious">
                <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Add Sale</h2>
                <form method="POST" action="sales.php">
                    <div class="modal-fields modal-fields-spacious">
                        <label class="modal-label">Date
                            <input type="text" name="sales_date" placeholder="Date" required>
                        </label>
                        <label class="modal-label">Quantity
                            <input type="number" name="quantity" placeholder="Quantity" required>
                        </label>
                        <label class="modal-label">Total Price
                            <input type="text" name="total_price" placeholder="Total Price" required>
                        </label>
                        <label class="modal-label">Customer
                            <select name="customer_id" required>
                                <option value="">Customer</option>
                                <?php foreach ($customers as $cus): ?>
                                    <option value="<?= $cus['customer_id'] ?>"><?= htmlspecialchars($cus['customer_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="modal-label">Product
                            <select name="product_id" required>
                                <option value="">Product</option>
                                <?php foreach ($products as $prod): ?>
                                    <option value="<?= $prod['product_id'] ?>"><?= htmlspecialchars($prod['product_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <div class="modal-actions modal-actions-spacious">
                            <button type="button" class="modal-cancel modal-cancel-spacious" onclick="document.getElementById('addSalesModal').style.display='none'">Cancel</button>
                            <button type="submit" class="add-btn add-btn-spacious">Add</button>
                        </div>
                    </div>
                </form>
                <button class="modal-close" onclick="document.getElementById('addSalesModal').style.display='none'">&times;</button>
            </div>
        </div>
    </div>

