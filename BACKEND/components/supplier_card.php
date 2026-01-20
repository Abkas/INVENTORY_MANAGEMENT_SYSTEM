<?php
// Supplier card component
?>
<link rel="stylesheet" href="/INVENTORY_SYSTEM/BACKEND/css/supplier_card.css">
<div class="supplier-card">
  <div class="supplier-card-header">
    <span class="supplier-card-title"><?= htmlspecialchars($supplier['supplier_name']) ?></span>
  </div>
  <div class="supplier-card-body">
    <div class="supplier-card-info">
      <span class="supplier-card-label">Email:</span>
      <span><?= htmlspecialchars($supplier['supplier_email']) ?></span>
    </div>
    <div class="supplier-card-info">
      <span class="supplier-card-label">Phone:</span>
      <span><?= htmlspecialchars($supplier['supplier_phone']) ?></span>
    </div>
  </div>
  <div class="supplier-card-actions">
    <button class="action-btn" onclick="openEditModal(<?= $supplier['supplier_id'] ?>, '<?= addslashes($supplier['supplier_name']) ?>', '<?= addslashes($supplier['supplier_email']) ?>', '<?= addslashes($supplier['supplier_phone']) ?>')">Edit</button>
    <button class="action-btn delete-btn" style="background:#fee2e2;color:#991b1b;" onclick="confirmDelete(<?= $supplier['supplier_id'] ?>)">Delete</button>
  </div>
</div>
