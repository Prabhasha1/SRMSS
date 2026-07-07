<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. User Authentication Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Role-Based Access Control (RBAC) Function
function restrictToRoles(array $allowedRoles) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
        // Forbidden or redirect to unauthorized error page
        header("HTTP/1.1 403 Forbidden");
        echo "<div style='text-align:center; margin-top:50px;'><h2>403 Forbidden: Access Denied.</h2></div>";
        exit();
    }
}
?>