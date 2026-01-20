<?php
session_start();
session_unset();
session_destroy();
echo '<script>alert("You have been logged out."); window.location.href = "/INVENTORY_SYSTEM/BACKEND/user/login.php";</script>';
exit();
?>
