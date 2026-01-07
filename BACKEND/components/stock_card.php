<?php
// Stock card component
?>
<link rel="stylesheet" href="/INVENTORY_SYSTEM/BACKEND/css/stock_card.css">
<div class="stock-card">
  <div class="stock-card-header">
    <span class="stock-card-title">Stock #<?= htmlspecialchars($stock['stock_id']) ?></span>
  </div>
  <div class="stock-card-body">
    <div class="stock-card-info"><span class="stock-card-label">Product:</span> <span><?= htmlspecialchars($stock['product_name']) ?></span></div>
    <div class="stock-card-info"><span class="stock-card-label">Warehouse:</span> <span><?= htmlspecialchars($stock['warehouse_name']) ?></span></div>
    <div class="stock-card-info"><span class="stock-card-label">Quantity:</span> <span><?= htmlspecialchars($stock['quantity']) ?></span></div>
  </div>
  <div class="stock-card-actions">
    <button class="action-btn">View</button>
    <button class="action-btn">Edit</button>
    <button class="action-btn">Delete</button>
  </div>
</div>
