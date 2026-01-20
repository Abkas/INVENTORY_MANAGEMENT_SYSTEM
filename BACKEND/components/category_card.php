<?php
// Category card component
?>
<link rel="stylesheet" href="/INVENTORY_SYSTEM/BACKEND/css/category_card.css">
<div class="category-card">
  <div class="category-card-header">
    <span class="category-card-title"><?= htmlspecialchars($category['category_name']) ?></span>
  </div>
  <div class="category-card-actions">
    <button class="action-btn" onclick="openEditModal(<?= $category['category_id'] ?>, '<?= addslashes($category['category_name']) ?>')">Edit</button>
    <button class="action-btn delete-btn" style="background:#fee2e2;color:#991b1b;" onclick="confirmDelete(<?= $category['category_id'] ?>)">Delete</button>
  </div>
</div>
