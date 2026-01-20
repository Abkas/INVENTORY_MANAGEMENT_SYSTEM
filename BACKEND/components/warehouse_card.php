<?php
// Warehouse Card Component
// Usage: expects $warehouse array with keys: warehouse_id, warehouse_name, location, capacity
?>
<div class="warehouse-card">
    <div class="warehouse-card-header">
        <span class="warehouse-card-title"><?php echo htmlspecialchars($warehouse['warehouse_name']); ?></span>
    </div>
    <div class="warehouse-card-body">
        <div><strong>Location:</strong> <?php echo htmlspecialchars($warehouse['location'] ?? 'N/A'); ?></div>
        <div><strong>Capacity:</strong> <?php echo htmlspecialchars($warehouse['capacity'] ?? 'N/A'); ?></div>
    </div>
    <div class="warehouse-card-footer" style="display:flex; justify-content: space-between; align-items: center;">
        <span class="warehouse-card-id">ID: <?php echo $warehouse['warehouse_id']; ?></span>
        <div class="warehouse-card-actions">
            <button class="action-btn" onclick="openEditModal(<?= $warehouse['warehouse_id'] ?>, '<?= addslashes($warehouse['warehouse_name']) ?>', '<?= addslashes($warehouse['location'] ?? '') ?>')">Edit</button>
            <button class="action-btn delete-btn" style="background:#fee2e2;color:#991b1b;border:none;padding:4px 8px;border-radius:4px;cursor:pointer;" onclick="confirmDelete(<?= $warehouse['warehouse_id'] ?>)">Delete</button>
        </div>
    </div>
</div>
