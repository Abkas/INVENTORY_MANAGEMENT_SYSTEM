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
$cus_result = mysqli_query(
    $conn, 
    "SELECT * FROM customer 
    ORDER BY customer_name ASC"
    );

if (!$cus_result) { die('Customer query error: ' . mysqli_error($conn)); }
$customers = [];
while ($row = mysqli_fetch_assoc($cus_result)) {
    $customers[] = $row;
}

// Fetch products
$prod_result = mysqli_query(
    $conn, 
    "SELECT * FROM product 
    ORDER BY product_name ASC"
    );

if (!$prod_result) { die('Product query error: ' . mysqli_error($conn)); }
$products = [];
while ($row = mysqli_fetch_assoc($prod_result)) {
    $products[] = $row;
}   

// Fetch sales
$sales_result = mysqli_query(
    $conn, 
    "SELECT s.*, c.customer_name, p.product_name 
    FROM sales s JOIN customer c ON s.customer_id = c.customer_id 
    JOIN product p ON s.product_id = p.product_id 
    ORDER BY s.sales_id DESC"
    );

if (!$sales_result) { die('Sales query error: ' . mysqli_error($conn)); }
$sales = [];
while ($row = mysqli_fetch_assoc($sales_result)) {
    $sales[] = $row;
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Sales</title>
    <style>
        body { background: #fff; color: #111; font-family: Arial, sans-serif; }
        .container { max-width: 900px; margin: 2.5rem auto; padding: 2rem; border: 1px solid #ddd; border-radius: 8px; }
        h2 { margin-top: 0; }
        form { margin-bottom: 2rem; display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr auto; gap: 1rem; }
        input[type="text"], input[type="number"], input[type="date"], select { padding: 0.5rem; border: 1px solid #bbb; border-radius: 4px; }
        button { padding: 0.5rem 1.2rem; border: none; background: #111; color: #fff; border-radius: 4px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.7rem; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f4f4f4; }
        .top-bar { display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem; }
    </style>
</head>
<body style="margin:0; padding:0;">
<?php include __DIR__ . '/comopnents/sidebar.php'; ?>
<div class="container" style="margin-left:220px;">
    <div class="top-bar">
        <h2>Sales</h2>
        <a href="/INVENTORY_SYSTEM/BACKEND/index.php"><button>Dashboard</button></a>
    </div>
    <?php if (!empty($error)): ?>
        <div style="color:red; margin-bottom:1rem; font-weight:bold;"> <?php echo $error; ?> </div>
    <?php endif; ?>
    <form method="POST" action="sales.php">
        <input type="date" name="sales_date" required>
        <input type="number" name="quantity" placeholder="Quantity" required>
        <input type="number" name="total_price" placeholder="Total Price" step="0.01" required>
        <select name="customer_id" required>
            <option value="">Customer</option>
            <?php foreach ($customers as $cus): ?>
                <option value="<?php echo $cus['customer_id']; ?>"><?php echo htmlspecialchars($cus['customer_name']); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="product_id" required>
            <option value="">Product</option>
            <?php foreach ($products as $prod): ?>
                <option value="<?php echo $prod['product_id']; ?>"><?php echo htmlspecialchars($prod['product_name']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Add</button>
    </form>
    <table>
        <tr><th>ID</th><th>Date</th><th>Quantity</th><th>Total Price</th><th>Customer</th><th>Product</th></tr>
        <?php foreach ($sales as $sale): ?>
            <tr>
                <td><?php echo $sale['sales_id']; ?></td>
                <td><?php echo $sale['sales_date']; ?></td>
                <td><?php echo $sale['quantity']; ?></td>
                <td><?php echo $sale['total_price']; ?></td>
                <td><?php echo htmlspecialchars($sale['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
