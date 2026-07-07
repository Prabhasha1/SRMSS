<?php
// Ensure a session is active before checking credentials
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check: If a user isn't logged in, kick them back to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRMSS - Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold text-primary" href="dashboard.php">
            <i class="bi bi-bus-front"></i> SRMSS
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50" href="routes.php">Routes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50" href="schedules.php">Schedules</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50" href="drivers.php">Drivers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50" href="vehicles.php">Vehicles</a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center">
                <span class="navbar-text text-white me-3 small">
                    <i class="bi bi-person-circle"></i> 
                    <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> 
                    (<?php echo htmlspecialchars($_SESSION['role']); ?>)
                </span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid px-4">