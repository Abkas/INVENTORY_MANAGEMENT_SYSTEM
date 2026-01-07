<?php
// Sales card component
?>
<link rel="stylesheet" href="/INVENTORY_SYSTEM/BACKEND/css/sales_card.css">
<div class="sales-card">
  <div class="sales-card-header">
    <span class="sales-card-title">Sale #<?= htmlspecialchars($sale['sales_id']) ?></span>
    <span class="sales-card-date"><?= htmlspecialchars($sale['sales_date']) ?></span>
  </div>
  <div class="sales-card-body">
    <div class="sales-card-info"><span class="sales-card-label">Product:</span> <span><?= htmlspecialchars($sale['product_id']) ?></span></div>
    <div class="sales-card-info"><span class="sales-card-label">Customer:</span> <span><?= htmlspecialchars($sale['customer_id']) ?></span></div>
    <div class="sales-card-info"><span class="sales-card-label">Quantity:</span> <span><?= htmlspecialchars($sale['quantity']) ?></span></div>
    <div class="sales-card-info"><span class="sales-card-label">Total Price:</span> <span><?= htmlspecialchars($sale['total_price']) ?></span></div>
  </div>
  <div class="sales-card-actions">
    <button class="action-btn">View</button>
    <button class="action-btn">Edit</button>
    <button class="action-btn">Delete</button>
  </div>
</div>
