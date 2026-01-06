<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/FRONTEND/pages/login.html");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory Dashboard</title>
    <style>
        body {
            background: #fff;
            color: #111;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dashboard-box {
            width: 100%;
            max-width: 900px;
            padding: 2.5rem 2rem;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .dashboard-header h2 {
            margin: 0;
            font-size: 2rem;
            font-weight: 500;
        }
        .logout-btn {
            background: #111;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 0.5rem 1.2rem;
            font-size: 1rem;
            cursor: pointer;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }
        .dashboard-card {
            background: #fff;
            border: 1px solid #bbb;
            border-radius: 6px;
            padding: 1.5rem 1rem;
            text-align: left;
        }
        .dashboard-card h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.1rem;
            color: #111;
        }
        .dashboard-card p {
            margin: 0;
            color: #222;
            font-size: 1rem;
        }
        .dashboard-card a button {
            margin-top: 1rem;
            background: #111;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 0.5rem 1.2rem;
            font-size: 1rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="dashboard-box">
        <div class="dashboard-header">
            <h2>Inventory Dashboard</h2>
            <a href="/INVENTORY_SYSTEM/BACKEND/user/logout.php"><button class="logout-btn">Logout</button></a>
        </div>
        <div style="margin-bottom:2.5rem;">
            <strong>Welcome, <?php echo $_SESSION['user']; ?>!</strong>
            <div style="margin-top:0.7rem; color:#444;">Manage your inventory, sales, purchases, and more from this dashboard.</div>
        </div>
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Products</h3>
                <p>View, add, or edit products.</p>
                <a href="/INVENTORY_SYSTEM/BACKEND/products.php"><button>Products</button></a>
            </div>
            <div class="dashboard-card">
                <h3>Stock</h3>
                <p>Monitor and update stock levels.</p>
                <a href="/INVENTORY_SYSTEM/BACKEND/stock.php"><button>Stock</button></a>
            </div>
            <div class="dashboard-card">
                <h3>Sales</h3>
                <p>Record new sales and view history.</p>
                <a href="/INVENTORY_SYSTEM/BACKEND/sales.php"><button>Sales</button></a>
            </div>
            <div class="dashboard-card">
                <h3>Purchases</h3>
                <p>Track product purchases.</p>
                <a href="/INVENTORY_SYSTEM/BACKEND/purchases.php"><button>Purchases</button></a>
            </div>
            <div class="dashboard-card">
                <h3>Customers</h3>
                <p>Manage customer info.</p>
                <a href="/INVENTORY_SYSTEM/BACKEND/customers.php"><button>Customers</button></a>
            </div>
                        <div class="dashboard-card">
                            <h3>Warehouses</h3>
                            <p>View and manage warehouses.</p>
                            <a href="/INVENTORY_SYSTEM/BACKEND/warehouses.php"><button>Warehouses</button></a>
                        </div>
            <div class="dashboard-card">
                <h3>Suppliers</h3>
                <p>View and manage suppliers.</p>
                <a href="/INVENTORY_SYSTEM/BACKEND/suppliers.php"><button>Suppliers</button></a>
            </div>
            <div class="dashboard-card">
                <h3>Categories</h3>
                <p>Organize products by category.</p>
                <a href="/INVENTORY_SYSTEM/BACKEND/categories.php"><button>Categories</button></a>
            </div>
            <div class="dashboard-card">
                <h3>Reports</h3>
                <p>View sales and inventory reports.</p>
                <a href="#"><button>Reports</button></a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
