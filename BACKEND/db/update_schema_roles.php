<?php
require_once __DIR__ . '/connect.php';

// Add role column if it doesn't exist
$sql = "ALTER TABLE user ADD COLUMN role ENUM('admin', 'staff') DEFAULT 'staff'";
try {
    if (mysqli_query($conn, $sql)) {
        echo "Successfully added 'role' column to 'user' table.\n";
    } else {
        // Ignore duplicate column error
        if (mysqli_errno($conn) == 1060) {
             echo "Column 'role' already exists.\n";
        } else {
             echo "Error adding column: " . mysqli_error($conn) . "\n";
        }
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

// Update the first user to be admin (assuming the user currently logged in or the first one created is the owner)
$sql_update = "UPDATE user SET role='admin' ORDER BY user_id ASC LIMIT 1";
if (mysqli_query($conn, $sql_update)) {
    echo "Successfully promoted the first user to Admin.\n";
} else {
    echo "Error updating admin user: " . mysqli_error($conn) . "\n";
}
?>
