<?php
// Customer card component
?>
<link rel="stylesheet" href="/INVENTORY_SYSTEM/BACKEND/css/customer_card.css">
<div class="customer-card">
  <div class="customer-card-header">
    <span class="customer-card-title"><?= htmlspecialchars($customer['customer_name']) ?></span>
  </div>
  <div class="customer-card-body">
    <div class="customer-card-info">
      <span class="customer-card-label">Email:</span>
      <span><?= htmlspecialchars($customer['customer_email']) ?></span>
    </div>
    <div class="customer-card-info">
      <span class="customer-card-label">Phone:</span>
      <span><?= htmlspecialchars($customer['customer_phone']) ?></span>
    </div>
  </div>
  <div class="customer-card-actions">
    <button class="action-btn" onclick="openEditModal(<?= $customer['customer_id'] ?>, '<?= addslashes($customer['customer_name']) ?>', '<?= addslashes($customer['customer_email']) ?>', '<?= addslashes($customer['customer_phone']) ?>')">Edit</button>
    <button class="action-btn delete-btn" style="background:#fee2e2;color:#991b1b;" onclick="confirmDelete(<?= $customer['customer_id'] ?>)">Delete</button>
  </div>
</div>
