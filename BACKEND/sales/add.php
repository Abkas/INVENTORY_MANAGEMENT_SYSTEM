<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
    exit();
}
require_once __DIR__ . '/../db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sales_date'], $_POST['quantity'], $_POST['total_price'], $_POST['product_id'])) {
    
    $sales_date = mysqli_real_escape_string($conn, $_POST['sales_date']);
    $quantity = intval($_POST['quantity']);
    $total_price = mysqli_real_escape_string($conn, $_POST['total_price']);
    $product_id = intval($_POST['product_id']);
    $user_id = $_SESSION['user_id'] ?? 1;

    $is_new_customer = isset($_POST['is_new_customer']);

    // 1. Check total stock before doing anything else
    $stock_result = mysqli_query($conn, "SELECT SUM(quantity) as total_stock FROM stock WHERE product_id = $product_id");
    $stock_row = mysqli_fetch_assoc($stock_result);
    $total_stock = $stock_row ? intval($stock_row['total_stock']) : 0;

    if ($total_stock < $quantity) {
        $_SESSION['error'] = 'Not enough stock available!';
        header("Location: ../sales.php");
        exit();
    }

    // Start Transaction
    mysqli_begin_transaction($conn);

    try {
        $customer_id = 0;

        if ($is_new_customer) {
            // Create New Customer
            $name = mysqli_real_escape_string($conn, $_POST['new_customer_name']);
            $email = mysqli_real_escape_string($conn, $_POST['customer_email']);
            $phone = mysqli_real_escape_string($conn, $_POST['customer_phone']);

            if (empty($name)) {
                throw new Exception("Customer name is required.");
            }

            $cus_query = "INSERT INTO customer (customer_name, customer_email, customer_phone) 
                           VALUES ('$name', '$email', '$phone')";
            if (!mysqli_query($conn, $cus_query)) {
                throw new Exception("Could not create new customer: " . mysqli_error($conn));
            }
            $customer_id = mysqli_insert_id($conn);
        } else {
            // Use Existing Customer
            $customer_id = intval($_POST['customer_id']);
            if ($customer_id <= 0) {
                throw new Exception("Please select a valid customer.");
            }
        }

        // 2. Insert into sales table
        $sale_query = "INSERT INTO sales (sales_date, quantity, total_price, customer_id, product_id, user_id) 
                       VALUES ('$sales_date', '$quantity', '$total_price', '$customer_id', '$product_id', '$user_id')";
        if (!mysqli_query($conn, $sale_query)) {
            throw new Exception("Could not record sale: " . mysqli_error($conn));
        }

        // 3. Reduce stock (FIFO style from warehouses)
        $qty_to_reduce = $quantity;
        $stock_q = mysqli_query($conn, "SELECT * FROM stock WHERE product_id = $product_id AND quantity > 0 ORDER BY stock_id ASC");

        while ($qty_to_reduce > 0 && ($row = mysqli_fetch_assoc($stock_q))) {
            $reduce = min($row['quantity'], $qty_to_reduce);
            $new_qty = $row['quantity'] - $reduce;
            $stock_id = $row['stock_id'];
            
            mysqli_query($conn, "UPDATE stock SET quantity = $new_qty WHERE stock_id = $stock_id");
            $qty_to_reduce -= $reduce;
        }

        mysqli_commit($conn);
        $_SESSION['msg'] = $is_new_customer ? "New customer created and sale recorded!" : "Sale recorded and stock updated!";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}
header("Location: ../sales.php");
exit();
?>
