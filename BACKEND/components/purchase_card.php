<?php
// Purchase card component
?>
<link rel="stylesheet" href="/INVENTORY_SYSTEM/BACKEND/css/purchase_card.css">
<div class="purchase-card">
  <div class="purchase-card-header">
    <span class="purchase-card-title">Purchase #<?= htmlspecialchars($purchase['purchase_id']) ?></span>
    <span class="purchase-card-date"><?= htmlspecialchars($purchase['purchase_date']) ?></span>
  </div>
  <div class="purchase-card-body">
    <div class="purchase-card-info"><span class="purchase-card-label">Product:</span> <span><?= htmlspecialchars($purchase['product_name'] ?? $purchase['product_id']) ?></span></div>
    <div class="purchase-card-info"><span class="purchase-card-label">Supplier:</span> <span><?= htmlspecialchars($purchase['supplier_name'] ?? '') ?></span></div>
    <div class="purchase-card-info"><span class="purchase-card-label">Quantity:</span> <span><?= htmlspecialchars($purchase['quantity']) ?></span></div>
    <div class="purchase-card-info"><span class="purchase-card-label">Total Price:</span> <span><?= htmlspecialchars($purchase['total_price']) ?></span></div>
  </div>
  <div class="purchase-card-actions">
    <button class="action-btn">View</button>
    <button class="action-btn">Edit</button>
    <button class="action-btn">Delete</button>
  </div>
</div>
