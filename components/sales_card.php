<?php
// Sales card component
?>
<link rel="stylesheet" href="css/sales_card.css">
<div class="premium-card">
    <div class="icon-box" style="background: #f0fdf4; color: #166534;">💰</div>
    
    <div>
        <div class="card-title"><?= htmlspecialchars($sale['product_name'] ?? 'Product #'.$sale['product_id']) ?></div>
        <div class="card-subtitle">
            <span class="badge badge-primary" style="display: inline-block; margin-bottom: 6px;">
                📁 <?= htmlspecialchars($sale['category_name'] ?? 'Uncategorized') ?>
            </span>
            <div style="font-weight: 600; color: #1e293b;">👤 <?= htmlspecialchars($sale['customer_name'] ?? 'Customer #'.$sale['customer_id']) ?></div>
            <div style="font-size: 0.8rem; margin-top: 4px;">📅 <?= date('M d, Y', strtotime($sale['sales_date'])) ?></div>
        </div>
    </div>

    <div class="card-stats">
        <div class="stat-item">
            <span class="stat-label">Qty</span>
            <span class="stat-value"><?= $sale['quantity'] ?></span>
        </div>
        <div class="stat-item" style="margin-left:auto; text-align:right;">
            <span class="stat-label">Total Revenue</span>
            <span class="stat-value" style="color: #059669; font-size: 1.1rem;">रु <?= number_format($sale['total_price'], 2) ?></span>
        </div>
    </div>
</div>
