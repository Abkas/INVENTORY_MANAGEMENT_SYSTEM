<?php
// Product card component
?>
<link rel="stylesheet" href="/INVENTORY_SYSTEM/BACKEND/css/product_card.css">


<div class="product-card">
  <div class="product-card-header">
    <span class="product-card-title"><?= htmlspecialchars($product['product_name']) ?></span>
  </div>
  <div class="product-card-body">
    <div class="product-card-info">
      <span class="product-card-label">Category:</span>
      <span><?= htmlspecialchars($product['category_name'] ?? '') ?></span>
    </div>
    <div class="product-card-info">
      <span class="product-card-label">Supplier:</span>
      <span><?= htmlspecialchars($product['supplier_name'] ?? '') ?></span>
    </div>
    <div class="product-card-info">
      <span class="product-card-label">Unit Price:</span>
      <span>रु <?= number_format($product['unit_price'], 2) ?></span>
    </div>
  </div>
  <div class="product-card-actions">
    <button class="action-btn">View</button>
    <button class="action-btn">Edit</button>
    <button class="action-btn">Delete</button>
  </div>
</div>
