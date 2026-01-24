<?php
// Customer card component
?>
<link rel="stylesheet" href="css/customer_card.css">
<div class="premium-card">
    <div class="card-actions">
        <!-- View Profile -->
        <a href="customer/profile.php?id=<?= $customer['customer_id'] ?>" class="action-btn" title="View Profile" style="background:#f0fdf4; color:#166534; text-decoration:none;">
            <i data-lucide="eye" style="width:16px;"></i>
        </a>
        <button class="action-btn btn-edit" title="Edit Customer" onclick="openEditModal(<?= $customer['customer_id'] ?>, '<?= addslashes($customer['customer_name']) ?>', '<?= addslashes($customer['customer_email']) ?>', '<?= addslashes($customer['customer_phone']) ?>')">
            <i data-lucide="edit-2" style="width:16px;"></i>
        </button>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <button class="action-btn btn-delete" title="Delete Customer" onclick="confirmDelete(<?= $customer['customer_id'] ?>)">
            <i data-lucide="trash-2" style="width:16px;"></i>
        </button>
        <?php endif; ?>
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
