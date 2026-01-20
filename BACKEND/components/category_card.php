<?php
// Category card component
?>
<link rel="stylesheet" href="/INVENTORY_SYSTEM/BACKEND/css/category_card.css">
<div class="premium-card">
    <div class="card-actions">
        <button class="action-btn btn-edit" title="Edit Category" onclick="openEditModal(<?= $category['category_id'] ?>, '<?= addslashes($category['category_name']) ?>')">✎</button>
        <button class="action-btn btn-delete" title="Delete Category" onclick="confirmDelete(<?= $category['category_id'] ?>)">🗑</button>
    </div>

    <div class="icon-box" style="background: #fff7ed; color: #ea580c;">📁</div>
    
    <div>
        <div class="card-title"><?= htmlspecialchars($category['category_name']) ?></div>
        <div class="card-subtitle">Organize and group products</div>
    </div>

    <div class="card-stats">
        <div class="stat-item">
            <span class="stat-label">Category ID</span>
            <span class="stat-value">#<?= $category['category_id'] ?></span>
        </div>
    </div>
</div>
