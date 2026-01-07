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
    <div class="warehouse-card-footer">
        <span class="warehouse-card-id">ID: <?php echo $warehouse['warehouse_id']; ?></span>
    </div>
</div>
