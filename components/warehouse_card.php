<?php
?>
<div class="premium-card">
    <div class="card-actions">
        <button class="action-btn btn-edit" title="Edit Warehouse" onclick="openEditModal(<?= $warehouse['warehouse_id'] ?>, '<?= addslashes($warehouse['warehouse_name']) ?>', '<?= addslashes($warehouse['location'] ?? '') ?>')">✎</button>
        <button class="action-btn btn-delete" title="Delete Warehouse" onclick="confirmDelete(<?= $warehouse['warehouse_id'] ?>)">🗑</button>
    </div>

    <div class="icon-box" style="background: #ecfdf5; color: #059669;">🏢</div>
    
    <div>
        <div class="card-title"><?= htmlspecialchars($warehouse['warehouse_name']) ?></div>
        <div class="card-subtitle">
            <div>📍 <?= htmlspecialchars(($warehouse['location'] ?? '') ?: 'No Location') ?></div>
        </div>
    </div>

    <div class="card-stats">
        <div class="stat-item">
            <span class="stat-label">Total Stock</span>
            <span class="stat-value"><?= number_format($warehouse['total_stock'] ?? 0) ?> Units</span>
        </div>
        <div class="stat-item" style="margin-left:auto; text-align:right;">
            <span class="stat-label">Warehouse ID</span>
            <span class="stat-value">#<?= $warehouse['warehouse_id'] ?></span>
        </div>
    </div>
</div>
