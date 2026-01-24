<?php
// Stock card component
?>
<link rel="stylesheet" href="css/stock_card.css">
<div class="premium-card">
    <div class="icon-box" style="background: #fdf2f8; color: #db2777;">📦</div>
    
    <div>
        <div class="card-title"><?= htmlspecialchars($stock['product_name']) ?></div>
        <div class="card-subtitle">
            <div style="font-weight: 600; color: #475569;">📍 <?= htmlspecialchars($stock['warehouse_name']) ?></div>
            <div style="font-size: 0.75rem; margin-top: 4px; color: #94a3b8;">Record ID: #<?= $stock['stock_id'] ?></div>
        </div>
    </div>

    <div class="card-stats">
        <div class="stat-item">
            <span class="stat-label">Available Stock</span>
            <span class="stat-value" style="font-size: 1.25rem;"><?= $stock['quantity'] ?> Units</span>
        </div>
    </div>
</div>
