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
    <button class="action-btn" onclick="openEditModal(<?= $product['product_id'] ?>, '<?= addslashes($product['product_name']) ?>', '<?= $product['unit_price'] ?>', '<?= $product['category_id'] ?>', '<?= $product['supplier_id'] ?>')">Edit</button>
    <button class="action-btn delete-btn" style="background:#fee2e2;color:#991b1b;" onclick="confirmDelete(<?= $product['product_id'] ?>)">Delete</button>
  </div>
</div>
