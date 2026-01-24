<?php
?>
<div class="premium-card">
    <div class="card-actions">
        <a href="staff/profile.php?id=<?= $user['user_id'] ?>" class="action-btn" title="View Profile" style="background:#eff6ff; color:#2563eb; text-decoration:none;">
            <i data-lucide="eye" style="width:16px;"></i>
        </a>
        <?php if ($user['user_id'] !== $_SESSION['user_id']): ?>
        <button class="action-btn" title="Delete Staff" onclick="confirmDelete(<?= $user['user_id'] ?>, '<?= addslashes($user['username']) ?>')" style="background:#fee2e2; color:#dc2626; border:none; width:32px; height:32px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
            <i data-lucide="trash-2" style="width:16px;"></i>
        </button>
        <?php endif; ?>
    </div>

    <div class="icon-box" style="background: <?= $user['role'] === 'admin' ? '#fef3c7' : '#e0f2fe' ?>; color: <?= $user['role'] === 'admin' ? '#92400e' : '#0369a1' ?>;">
        <i data-lucide="user" style="width:24px;"></i>
    </div>
    
    <div>
        <div class="card-title"><?= htmlspecialchars($user['username']) ?></div>
        <div class="card-subtitle">
            <?php if ($user['role'] === 'admin'): ?>
                <span style="background:#fef3c7; color:#92400e; padding:4px 8px; border-radius:8px; font-weight:600; font-size:0.75rem;">
                    👑 Admin
                </span>
            <?php else: ?>
                <span style="background:#e0f2fe; color:#0369a1; padding:4px 8px; border-radius:8px; font-weight:600; font-size:0.75rem;">
                    👤 Staff
                </span>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-stats">
        <div class="stat-item">
            <span class="stat-label">Sales</span>
            <span class="stat-value"><?= $user['sales_count'] ?></span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Purchases</span>
            <span class="stat-value"><?= $user['purchase_count'] ?></span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Total Activity</span>
            <span class="stat-value">रु <?= number_format($user['sales_total'] + $user['purchase_total'], 0) ?></span>
        </div>
    </div>
</div>
