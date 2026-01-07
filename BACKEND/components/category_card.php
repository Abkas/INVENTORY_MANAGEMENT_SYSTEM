<?php
// Category card component
?>
<link rel="stylesheet" href="/INVENTORY_SYSTEM/BACKEND/css/category_card.css">
<div class="category-card">
  <div class="category-card-header">
    <span class="category-card-title"><?= htmlspecialchars($category['category_name']) ?></span>
  </div>
  <div class="category-card-actions">
    <button class="action-btn">View</button>
    <button class="action-btn">Edit</button>
    <button class="action-btn">Delete</button>
  </div>
</div>
