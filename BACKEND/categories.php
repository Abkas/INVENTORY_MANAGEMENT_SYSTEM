<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/FRONTEND/pages/login.html");
    exit();
}
require_once __DIR__ . '/db/connect.php';

// Handle add category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {
    $category_name = trim($_POST['category_name']);
    if ($category_name !== '') {
        mysqli_query($conn, "INSERT INTO category (category_name) VALUES ('$category_name')");
        header("Location: categories.php");
        exit();
    }
}
// Fetch categories
$result = mysqli_query($conn, "SELECT * FROM category ORDER BY category_id DESC");
$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories</title>
    <style>
        body { background: #fff; color: #111; font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 2.5rem auto; padding: 2rem; border: 1px solid #ddd; border-radius: 8px; }
        h2 { margin-top: 0; }
        form { margin-bottom: 2rem; display: flex; gap: 1rem; }
        input[type="text"] { flex: 1; padding: 0.5rem; border: 1px solid #bbb; border-radius: 4px; }
        button { padding: 0.5rem 1.2rem; border: none; background: #111; color: #fff; border-radius: 4px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.7rem; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f4f4f4; }
    </style>
</head>
<body style="margin:0; padding:0;">
<?php include __DIR__ . '/comopnents/sidebar.php'; ?>
<div class="container" style="margin-left:220px;">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h2>Categories</h2>
        <a href="/INVENTORY_SYSTEM/BACKEND/index.php"><button style="padding:0.5rem 1.2rem;">Dashboard</button></a>
    </div>
    <form method="POST" action="categories.php">
        <input type="text" name="category_name" placeholder="Add new category" required>
        <button type="submit">Add</button>
    </form>
    <table>
        <tr><th>ID</th><th>Name</th></tr>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?php echo $cat['category_id']; ?></td>
                <td><?php echo htmlspecialchars($cat['category_name']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
