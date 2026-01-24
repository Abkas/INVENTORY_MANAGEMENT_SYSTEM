<?php
require_once __DIR__ . '/connect.php';

echo "Starting staff tracking schema update...\n\n";

// 1. Add user_id to sales table
$sql1 = "ALTER TABLE sales ADD COLUMN user_id INT NULL";
if (mysqli_query($conn, $sql1)) {
    echo "✓ Added user_id column to sales table\n";
} else {
    if (mysqli_errno($conn) == 1060) {
        echo "- user_id column already exists in sales table\n";
    } else {
        echo "✗ Error: " . mysqli_error($conn) . "\n";
    }
}

// 2. Add user_id to purchase table
$sql2 = "ALTER TABLE purchase ADD COLUMN user_id INT NULL";
if (mysqli_query($conn, $sql2)) {
    echo "✓ Added user_id column to purchase table\n";
} else {
    if (mysqli_errno($conn) == 1060) {
        echo "- user_id column already exists in purchase table\n";
    } else {
        echo "✗ Error: " . mysqli_error($conn) . "\n";
    }
}

// 3. Set default user_id for existing records (use first admin)
$admin_query = mysqli_query($conn, "SELECT user_id FROM user WHERE role='admin' ORDER BY user_id ASC LIMIT 1");
$admin = mysqli_fetch_assoc($admin_query);

if ($admin) {
    $admin_id = $admin['user_id'];
    
    // Update sales
    $sql3 = "UPDATE sales SET user_id = $admin_id WHERE user_id IS NULL";
    if (mysqli_query($conn, $sql3)) {
        $affected = mysqli_affected_rows($conn);
        echo "✓ Updated $affected sales records with default user_id\n";
    }
    
    // Update purchases
    $sql4 = "UPDATE purchase SET user_id = $admin_id WHERE user_id IS NULL";
    if (mysqli_query($conn, $sql4)) {
        $affected = mysqli_affected_rows($conn);
        echo "✓ Updated $affected purchase records with default user_id\n";
    }
} else {
    echo "! No admin user found to set as default\n";
}

echo "\n✓ Schema update complete!\n";
?>
