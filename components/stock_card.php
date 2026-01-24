<?php
?>
<link rel="stylesheet" href="css/stock_card.css">
<div class="premium-card">
    <div class="icon-box" style="background: #fdf2f8; color: #db2777;">📦</div>
    <div style="position: absolute; top: 1rem; right: 1rem;">
        <a href="product/view.php?id=<?= $stock['product_id'] ?>" style="background: #eff6ff; color: #2563eb; width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; text-decoration: none;" title="View Product Details">
            <i data-lucide="eye" style="width: 16px;"></i>
        </a>
    </div>
    
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
