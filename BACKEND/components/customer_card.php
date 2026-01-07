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
    <button class="action-btn">View</button>
    <button class="action-btn">Edit</button>
    <button class="action-btn">Delete</button>
  </div>
</div>
