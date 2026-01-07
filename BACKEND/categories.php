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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
    <link rel="stylesheet" href="css/categories.css">
    <link rel="stylesheet" href="css/category_card.css">
</head>
<body>
<div class="container">
    <?php include __DIR__ . '/components/sidebar.php'; ?>
    
    <div class="header">
        <div>
            <div class="header-title">Categories</div>
            <div class="header-sub">Manage your category list</div>
        </div>
        <button class="add-btn" onclick="document.getElementById('addCategoryModal').style.display='block'">Add Category</button>
    </div>

    <!-- Main Content Wrapper -->
    <div class="main-content">
        <div class="category-card-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;">
            <?php foreach ($categories as $category): ?>
                <?php include __DIR__ . '/components/category_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="modal-bg">
        <div class="modal-content modal-content-spacious">
            <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Add Category</h2>
            <form method="POST" action="categories.php">
                <div class="modal-fields modal-fields-spacious">
                    <label class="modal-label">Category Name
                        <input type="text" name="category_name" placeholder="Enter category name" required>
                    </label>
                    <div class="modal-actions modal-actions-spacious">
                        <button type="button" class="modal-cancel modal-cancel-spacious" onclick="document.getElementById('addCategoryModal').style.display='none'">Cancel</button>
                        <button type="submit" class="add-btn add-btn-spacious">Add</button>
                    </div>
                </div>
            </form>
            <button class="modal-close" onclick="document.getElementById('addCategoryModal').style.display='none'">&times;</button>
        </div>
    </div>
</div>
</body>
</html>
