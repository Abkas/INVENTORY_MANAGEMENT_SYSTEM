<?php
// Customer card component
?>
<link rel="stylesheet" href="/INVENTORY_SYSTEM/BACKEND/css/customer_card.css">
<div class="premium-card">
    <div class="card-actions">
        <button class="action-btn btn-edit" title="Edit Customer" onclick="openEditModal(<?= $customer['customer_id'] ?>, '<?= addslashes($customer['customer_name']) ?>', '<?= addslashes($customer['customer_email']) ?>', '<?= addslashes($customer['customer_phone']) ?>')">✎</button>
        <button class="action-btn btn-delete" title="Delete Customer" onclick="confirmDelete(<?= $customer['customer_id'] ?>)">🗑</button>
    </div>

    <div class="icon-box" style="background: #f5f3ff; color: #7c3aed;">👤</div>
    
    <div>
        <div class="card-title"><?= htmlspecialchars($customer['customer_name']) ?></div>
        <div class="card-subtitle">
            <div style="margin-bottom: 2px;">📧 <?= htmlspecialchars($customer['customer_email'] ?: 'No Email') ?></div>
            <div>📞 <?= htmlspecialchars($customer['customer_phone'] ?: 'No Phone') ?></div>
        </div>
    </div>

    <div class="card-stats">
        <div class="stat-item">
            <span class="stat-label">Customer ID</span>
            <span class="stat-value">#<?= $customer['customer_id'] ?></span>
        </div>
    </div>
</div>
