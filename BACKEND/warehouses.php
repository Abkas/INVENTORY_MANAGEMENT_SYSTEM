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
    <title>Manage Warehouses</title>
    <style>
        body { background: #fff; color: #111; font-family: Arial, sans-serif; }
        .container { max-width: 700px; margin: 2.5rem auto; padding: 2rem; border: 1px solid #ddd; border-radius: 8px; }
        h2 { margin-top: 0; }
        form { margin-bottom: 2rem; display: grid; grid-template-columns: 2fr 2fr auto; gap: 1rem; }
        input[type="text"] { padding: 0.5rem; border: 1px solid #bbb; border-radius: 4px; }
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
        <h2>Warehouses</h2>
        <a href="/INVENTORY_SYSTEM/BACKEND/index.php"><button>Dashboard</button></a>
    </div>
    <form method="POST" action="warehouses.php">
        <input type="text" name="warehouse_name" placeholder="Warehouse name" required>
        <input type="text" name="location" placeholder="Location">
        <button type="submit">Add</button>
    </form>
    <table>
        <tr><th>ID</th><th>Name</th><th>Location</th></tr>
        <?php foreach ($warehouses as $wh): ?>
            <tr>
                <td><?php echo $wh['warehouse_id']; ?></td>
                <td><?php echo htmlspecialchars($wh['warehouse_name']); ?></td>
                <td><?php echo htmlspecialchars($wh['location']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
