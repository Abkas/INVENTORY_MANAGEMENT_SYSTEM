<?php
session_start();
session_unset();
session_destroy();
echo '<script>alert("You have been logged out."); window.location.href = "/INVENTORY_SYSTEM/FRONTEND/pages/login.html";</script>';
exit();
?>