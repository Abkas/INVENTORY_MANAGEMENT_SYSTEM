<?php
// Generate password hashes for SQL file
echo "-- Password hashes for Initial_sql.sql\n\n";

$admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
$staff_hash = password_hash('staff123', PASSWORD_DEFAULT);

echo "Admin password: 'admin123'\n";
echo "Admin hash: $admin_hash\n\n";

echo "Staff password: 'staff123'\n";
echo "Staff hash: $staff_hash\n\n";

echo "-- SQL INSERT statement:\n";
echo "INSERT INTO user (username, password, role) VALUES\n";
echo "('admin', '$admin_hash', 'admin'),\n";
echo "('staff', '$staff_hash', 'staff');\n";
?>
