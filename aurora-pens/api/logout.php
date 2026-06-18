<?php
// api/logout.php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect back to the main catalog page
header("Location: ../index.php");
exit;
?>