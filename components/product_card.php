<?php
// Product card component
?>
<link rel="stylesheet" href="css/product_card.css">


<div class="premium-card">
    <div class="card-actions">
        <button class="action-btn btn-edit" title="Edit Product" onclick="openEditModal(<?= $product['product_id'] ?>, '<?= addslashes($product['product_name']) ?>', '<?= $product['unit_price'] ?>', '<?= $product['category_id'] ?>', '<?= $product['supplier_id'] ?>')">✎</button>
        <button class="action-btn btn-delete" title="Delete Product" onclick="confirmDelete(<?= $product['product_id'] ?>)">🗑</button>
    </div>

    <div class="icon-box">📦</div>
    
    <div>
        <div class="card-title"><?= htmlspecialchars($product['product_name']) ?></div>
        <div class="card-subtitle">
            <span class="badge badge-success"><?= htmlspecialchars($product['category_name'] ?? 'General') ?></span>
            <span style="display:block; margin-top: 5px; font-size: 0.8rem;">Supplier: <?= htmlspecialchars($product['supplier_name'] ?? 'None') ?></span>
        </div>
    </div>

    <div class="card-stats">
        <div class="stat-item">
            <span class="stat-label">Price</span>
            <span class="stat-value" style="color: #059669;">रु <?= number_format($product['unit_price'], 2) ?></span>
        </div>
        <div class="stat-item" style="margin-left:auto; text-align:right;">
            <span class="stat-label">Stock</span>
            <?php 
            $stock = $product['total_stock'] ?? 0;
            $stockColor = $stock < 10 ? '#dc2626' : '#059669';
            $stockBg = $stock < 10 ? '#fee2e2' : '#dcfce7';
            ?>
            <span class="stat-value" style="color: <?= $stockColor ?>; background: <?= $stockBg ?>; padding: 4px 10px; border-radius: 6px; font-weight: 700;">
                <?= $stock ?> units
            </span>
        </div>
    </div>
</div>
