<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /INVENTORY_SYSTEM/BACKEND/user/login.php");
    exit();
}
require_once __DIR__ . '/db/connect.php';

// Fetch categories with product count and product names
$result = mysqli_query($conn, "
    SELECT c.*, 
           COUNT(p.product_id) as product_count,
           GROUP_CONCAT(p.product_name SEPARATOR '|||') as product_names
    FROM category c 
    LEFT JOIN product p ON c.category_id = p.category_id 
    GROUP BY c.category_id 
    ORDER BY c.category_id DESC
");
$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Split product names into an array
    $row['products'] = !empty($row['product_names']) ? explode('|||', $row['product_names']) : [];
    $categories[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/shared_cards.css">
</head>
<body>
<div class="container">
    <?php include __DIR__ . '/components/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <div>
                <div class="header-title">Categories</div>
                <div class="header-sub">Manage your category list</div>
            </div>
            <button class="add-btn" onclick="document.getElementById('addCategoryModal').style.display='flex'">Add Category</button>
        </div>

        <!-- Feedback Messages -->
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="msg-success"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
        <?php endif; ?>
        <div class="category-card-grid" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:25px;">
            <?php foreach ($categories as $category): ?>
                <?php include __DIR__ . '/components/category_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="modal-bg" style="display:none;">
        <div class="modal-content modal-content">
            <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Add Category</h2>
            <form method="POST" action="category/add.php">
                <div class="modal-fields modal-fields">
                    <label class="modal-label">Category Name
                        <input type="text" name="category_name" placeholder="Enter category name" required>
                    </label>
                    <div class="modal-actions modal-actions">
                        <button type="button" class="modal-cancel modal-cancel" onclick="document.getElementById('addCategoryModal').style.display='none'">Cancel</button>
                        <button type="submit" class="add-btn add-btn">Add</button>
                    </div>
                </div>
            </form>
            <button class="modal-close" onclick="document.getElementById('addCategoryModal').style.display='none'">&times;</button>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="modal-bg" style="display:none;">
        <div class="modal-content modal-content">
            <h2 style="margin-top:0;font-size:1.6rem;font-weight:700;letter-spacing:-1px;color:#23272f;">Edit Category</h2>
            <form method="POST" action="category/edit.php">
                <input type="hidden" name="category_id" id="edit_category_id">
                <div class="modal-fields modal-fields">
                    <label class="modal-label">Category Name
                        <input type="text" name="category_name" id="edit_category_name" placeholder="Enter category name" required>
                    </label>
                    <div class="modal-actions modal-actions">
                        <button type="button" class="modal-cancel modal-cancel" onclick="document.getElementById('editCategoryModal').style.display='none'">Cancel</button>
                        <button type="submit" class="add-btn add-btn">Update</button>
                    </div>
                </div>
            </form>
            <button class="modal-close" onclick="document.getElementById('editCategoryModal').style.display='none'">&times;</button>
        </div>
    </div>

    <script>
    function openEditModal(id, name) {
        document.getElementById('edit_category_id').value = id;
        document.getElementById('edit_category_name').value = name;
        document.getElementById('editCategoryModal').style.display = 'block';
    }
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this category?')) {
            window.location.href = 'category/delete.php?id=' + id;
        }
    }
    </script>
</div>
</body>
</html>
