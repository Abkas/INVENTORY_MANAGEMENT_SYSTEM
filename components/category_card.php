<?php
?>
<link rel="stylesheet" href="css/category_card.css">
<div class="premium-card">
    <div class="card-actions">
        <button class="action-btn btn-edit" title="Edit Category" onclick="openEditModal(<?= $category['category_id'] ?>, '<?= addslashes($category['category_name']) ?>')">✎</button>
        <button class="action-btn btn-delete" title="Delete Category" onclick="confirmDelete(<?= $category['category_id'] ?>)">🗑</button>
    </div>

    <div class="icon-box" style="background: #fff7ed; color: #ea580c;">📁</div>
    
    <div>
        <div class="card-title"><?= htmlspecialchars($category['category_name']) ?></div>
        <div class="card-subtitle" style="margin-bottom: 10px;">
            <span class="badge badge-primary"><?= $category['product_count'] ?> Product<?= $category['product_count'] != 1 ? 's' : '' ?></span>
        </div>
        
        <?php if (!empty($category['products']) && $category['product_count'] > 0): ?>
            <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #e2e8f0;">
                <div style="font-size: 0.75rem; font-weight: 600; color: #64748b; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Products</div>
                <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                    <?php 
                    $displayProducts = array_slice($category['products'], 0, 3);
                    foreach ($displayProducts as $pname): 
                    ?>
                        <span style="background: #f1f5f9; color: #334155; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 500;">
                            <?= htmlspecialchars($pname) ?>
                        </span>
                    <?php endforeach; ?>
                    <?php if ($category['product_count'] > 3): ?>
                        <span style="background: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                            +<?= $category['product_count'] - 3 ?> more
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div style="margin-top: 12px; padding: 8px; background: #fef3c7; border-radius: 6px; font-size: 0.75rem; color: #92400e; text-align: center;">
                No products yet
            </div>
        <?php endif; ?>
    </div>

    <div class="card-stats" style="margin-top: 12px;">
        <div class="stat-item">
            <span class="stat-label">Category ID</span>
            <span class="stat-value">#<?= $category['category_id'] ?></span>
        </div>
    </div>
</div>
