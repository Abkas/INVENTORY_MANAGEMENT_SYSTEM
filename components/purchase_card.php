<?php
// Purchase card component
?>
<link rel="stylesheet" href="css/purchase_card.css">
<div class="premium-card">
    <div class="icon-box" style="background: #eef2ff; color: #4338ca;">📦</div>
    
    <div>
        <div class="card-title"><?= htmlspecialchars($purchase['product_name'] ?? 'Product #'.$purchase['product_id']) ?></div>
        <div class="card-subtitle">
            <div style="font-weight: 600; color: #1e293b;">🏭 <?= htmlspecialchars($purchase['supplier_name'] ?? 'Direct Source') ?></div>
            <div style="font-size: 0.8rem; margin-top: 4px;">📅 <?= date('M d, Y', strtotime($purchase['purchase_date'])) ?></div>
        </div>
    </div>

    <div class="card-stats">
        <div class="stat-item">
            <span class="stat-label">Qty</span>
            <span class="stat-value">+<?= $purchase['quantity'] ?></span>
        </div>
        <div class="stat-item" style="margin-left:auto; text-align:right;">
            <span class="stat-label">Cost</span>
            <span class="stat-value" style="color: #dc2626; font-size: 1.1rem;">रु <?= number_format($purchase['total_price'], 2) ?></span>
        </div>
    </div>
</div>
