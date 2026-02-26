<?php
session_start();
// Remove customer view mode
unset($_SESSION['view_mode']);

// Redirect back to admin dashboard
header("Location: admin_dashboard.php");
exit();
?>