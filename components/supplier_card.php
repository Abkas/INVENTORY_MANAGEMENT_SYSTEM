<?php
// Supplier card component
?>
<link rel="stylesheet" href="css/supplier_card.css">
<div class="premium-card">
    <div class="card-actions">
        <button class="action-btn btn-edit" title="Edit Supplier" onclick="openEditModal(<?= $supplier['supplier_id'] ?>, '<?= addslashes($supplier['supplier_name']) ?>', '<?= addslashes($supplier['supplier_email']) ?>', '<?= addslashes($supplier['supplier_phone']) ?>')">✎</button>
        <button class="action-btn btn-delete" title="Delete Supplier" onclick="confirmDelete(<?= $supplier['supplier_id'] ?>)">🗑</button>
    </div>

    <div class="icon-box" style="background: #e0f2fe; color: #0284c7;">🚚</div>
    
    <div>
        <div class="card-title"><?= htmlspecialchars($supplier['supplier_name']) ?></div>
        <div class="card-subtitle">
            <div style="margin-bottom: 2px;">📧 <?= htmlspecialchars($supplier['supplier_email'] ?: 'No Email') ?></div>
            <div>📞 <?= htmlspecialchars($supplier['supplier_phone'] ?: 'No Phone') ?></div>
        </div>
    </div>

    <div class="card-stats">
        <div class="stat-item">
            <span class="stat-label">Supplier ID</span>
            <span class="stat-value">#<?= $supplier['supplier_id'] ?></span>
        </div>
    </div>
</div>
