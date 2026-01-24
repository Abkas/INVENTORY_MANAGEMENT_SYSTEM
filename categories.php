<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: user/login.php");
    exit();
}
require_once __DIR__ . '/db/connect.php';

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
    <link rel="stylesheet" href="css/global.css?v=<?= time() ?>">
    <link rel="stylesheet" href="css/shared_cards.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
<div class="container">
    <?php include __DIR__ . '/components/sidebar.php'; ?>
    <?php include __DIR__ . '/components/toast_notifications.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <div>
                <div class="header-title">Categories</div>
                <div class="header-sub">Manage your category list</div>
            </div>
            <div style="display:flex; gap:16px; align-items: center;">
                <div class="segment-group">
                    <button class="segment-btn active" onclick="toggleView('card')" id="btn-card" title="Grid View">
                        <i data-lucide="layout-grid" style="width:18px;"></i>
                    </button>
                    <div style="width:1px; background:#e2e8f0; margin:4px 0;"></div>
                    <button class="segment-btn" onclick="toggleView('table')" id="btn-table" title="Table View">
                        <i data-lucide="table" style="width:18px;"></i>
                    </button>
                </div>
                <button class="add-btn" onclick="document.getElementById('addCategoryModal').style.display='flex'">Add Category</button>
            </div>
        </div>

        <div id="view-card" class="category-card-grid responsive-grid">
            <?php foreach ($categories as $category): ?>
                <?php include __DIR__ . '/components/category_card.php'; ?>
        </div>
        <div id="view-table" class="table-container" style="display:none;">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Category Name</th>
                        <th style="width: 30%;">Product Count</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                    <tr>
                        <td>
                            <div style="font-weight:600; color:var(--text-main);"><?= htmlspecialchars($category['category_name']) ?></div>
                        </td>
                        <td>
                            <span style="background:#f1f5f9; color:#475569; padding:4px 10px; border-radius:12px; font-weight:600; font-size:0.85rem;">
                                <?= $category['product_count'] ?> products
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:inline-flex; gap:8px;">
                                <button class="action-btn" title="Edit" onclick="openEditModal(<?= $category['category_id'] ?>, '<?= addslashes($category['category_name']) ?>')" style="background:#eff6ff; color:#2563eb; border:none; width:32px; height:32px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                                    <i data-lucide="edit-2" style="width:16px;"></i>
                                </button>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <button class="action-btn" title="Delete" onclick="confirmDelete(<?= $category['category_id'] ?>)" style="background:#fee2e2; color:#dc2626; border:none; width:32px; height:32px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                                    <i data-lucide="trash-2" style="width:16px;"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

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
    function toggleView(view) {
        const cardView = document.getElementById('view-card');
        const tableView = document.getElementById('view-table');
        const btnCard = document.getElementById('btn-card');
        const btnTable = document.getElementById('btn-table');

        if (view === 'card') {
            cardView.style.display = 'grid';
            tableView.style.display = 'none';
            btnCard.classList.add('active');
            btnTable.classList.remove('active');
        } else {
            cardView.style.display = 'none';
            tableView.style.display = 'block';
            btnTable.classList.add('active');
            btnCard.classList.remove('active');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if(window.lucide) lucide.createIcons();
    });

    function openEditModal(id, name) {
        document.getElementById('edit_category_id').value = id;
        document.getElementById('edit_category_name').value = name;
        document.getElementById('editCategoryModal').style.display = 'flex';
    }
    function confirmDelete(id) {
        showConfirmModal({
            title: 'Delete Category?',
            message: 'Are you sure you want to delete this category? Products in this category will be affected.',
            icon: '🗑️',
            iconType: 'danger',
            confirmText: 'Yes, Delete',
            confirmClass: 'confirm',
            onConfirm: () => {
                window.location.href = 'category/delete.php?id=' + id;
            }
        });
    }
    </script>
</div>
</body>
</html>
