<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_date'], $_POST['quantity'], $_POST['total_price'])) {
    
    $purchase_date = mysqli_real_escape_string($conn, $_POST['purchase_date']);
    $quantity = intval($_POST['quantity']);
    $total_price = mysqli_real_escape_string($conn, $_POST['total_price']);
    $unit_price = isset($_POST['unit_price']) ? mysqli_real_escape_string($conn, $_POST['unit_price']) : 0;
    $user_id = $_SESSION['user_id'] ?? 1;

    $is_new = isset($_POST['is_new_product']);

    mysqli_begin_transaction($conn);

    try {
        $product_id = 0;

        if ($is_new) {
            // Create New Product
            $name = mysqli_real_escape_string($conn, $_POST['new_product_name']);
            $cat_id = intval($_POST['category_id']);
            $sup_id = intval($_POST['supplier_id']);

            if (empty($name) || $cat_id <= 0 || $sup_id <= 0) {
                throw new Exception("New product details (Name, Category, Supplier) are required.");
            }

            $prod_query = "INSERT INTO product (product_name, unit_price, category_id, supplier_id) 
                           VALUES ('$name', '$unit_price', '$cat_id', '$sup_id')";
            if (!mysqli_query($conn, $prod_query)) {
                throw new Exception("Could not create new product: " . mysqli_error($conn));
            }
            $product_id = mysqli_insert_id($conn);
        } else {
            // Use Existing Product
            $product_id = intval($_POST['product_id']);
            if ($product_id <= 0) {
                throw new Exception("Please select a valid product.");
            }
            
            // Optionally update the product's unit price to the latest purchase price
            mysqli_query($conn, "UPDATE product SET unit_price = '$unit_price' WHERE product_id = $product_id");
        }


        // 1. Get supplier_id from the product
        $supplier_query = mysqli_query($conn, "SELECT supplier_id FROM product WHERE product_id = $product_id");
        $supplier_row = mysqli_fetch_assoc($supplier_query);
        $supplier_id = $supplier_row ? $supplier_row['supplier_id'] : null;

        // 2. Insert into purchase table
        $query = "INSERT INTO purchase (purchase_date, quantity, total_price, product_id, supplier_id, user_id) 
                  VALUES ('$purchase_date', '$quantity', '$total_price', '$product_id', '$supplier_id', '$user_id')";
        if (!mysqli_query($conn, $query)) {
            throw new Exception("Could not record purchase: " . mysqli_error($conn));
        }

        // 2. Update/Insert stock
        $warehouse_result = mysqli_query($conn, "SELECT warehouse_id FROM warehouse ORDER BY warehouse_id ASC LIMIT 1");
        $warehouse_row = mysqli_fetch_assoc($warehouse_result);
        
        if (!$warehouse_row) {
            throw new Exception("No warehouse found. Please create a warehouse first.");
        }
        
        $warehouse_id = $warehouse_row['warehouse_id'];
        $stock_check = mysqli_query($conn, "SELECT stock_id, quantity FROM stock WHERE product_id = $product_id AND warehouse_id = $warehouse_id");
        
        if ($row = mysqli_fetch_assoc($stock_check)) {
            $new_qty = $row['quantity'] + $quantity;
            mysqli_query($conn, "UPDATE stock SET quantity = $new_qty WHERE stock_id = " . $row['stock_id']);
        } else {
            mysqli_query($conn, "INSERT INTO stock (product_id, warehouse_id, quantity, user_id) 
                                 VALUES ($product_id, $warehouse_id, $quantity, $user_id)");
        }

        mysqli_commit($conn);
        $_SESSION['msg'] = $is_new ? "New product created and purchase recorded!" : "Purchase recorded and stock increased!";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}
header("Location: ../purchases.php");
exit();
?>
