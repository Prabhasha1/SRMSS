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
    <title>SRMSS - Panel Operations Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* Cinematic Fluid Gradient Base Workspace */
        @keyframes headerGradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        body {
            background: linear-gradient(-45deg, #060913, #0d1527, #13275c, #043629, #0f131f);
            background-size: 400% 400%;
            animation: headerGradientShift 22s ease infinite;
            color: #f8fafc;
            min-height: 100vh;
            font-family: system-ui, -apple-system, sans-serif;
            position: relative;
            background-attachment: fixed;
        }

        /* Vector Transit Route Underlays */
        .depot-header-network {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            opacity: 0.25;
            mix-blend-mode: screen;
        }

        .transit-line {
            stroke-dasharray: 40, 180;
            animation: globalRouteFlow 12s linear infinite;
        }
        @keyframes globalRouteFlow {
            from { stroke-dashoffset: 400; }
            to { stroke-dashoffset: 0; }
        }

        /* Glassmorphic Navbar Container */
        .premium-navbar {
            background: rgba(15, 23, 42, 0.55) !important;
            backdrop-filter: blur(20px) saturate(160%);
            -webkit-backdrop-filter: blur(20px) saturate(160%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            position: relative;
            z-index: 10;
        }

        .brand-accent {
            background: linear-gradient(135deg, #3b82f6, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        /* Modernized Interactive Nav Items */
        .premium-navbar .nav-link {
            color: #94a3b8 !important;
            font-weight: 500;
            padding: 8px 16px !important;
            border-radius: 8px;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            margin: 0 2px;
        }

        .premium-navbar .nav-link:hover {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.05);
        }

        /* Highlighting Active Operational Page Hooks */
        .premium-navbar .nav-link.active-hub {
            color: #ffffff !important;
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.25);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
        }

        /* User Profile Desk Badges */
        .user-identity-badge {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 6px 14px;
        }

        .role-pill {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.25);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
        }

        .btn-logout-premium {
            border: 1px solid rgba(239, 68, 68, 0.3);
            background: rgba(239, 68, 68, 0.05);
            color: #f87171;
            border-radius: 10px;
            transition: all 0.25s ease;
        }
        .btn-logout-premium:hover {
            background: #ef4444;
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
            transform: translateY(-1px);
        }

        /* Dashboard content layout container override */
        .workspace-anchor {
            position: relative;
            z-index: 5;
        }
    </style>
</head>
<body>

<svg class="depot-header-network" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice">
    <path d="M-50,150 L600,150 L800,350 L1600,350" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="2" />
    <path class="transit-line" d="M-50,150 L600,150 L800,350 L1600,350" fill="none" stroke="#3b82f6" stroke-width="2" filter="drop-shadow(0 0 3px #2563eb)"/>
    <path d="M100,900 L400,500 L900,500 L1100,-50" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="2" />
    <path class="transit-line" d="M100,900 L400,500 L900,500 L1100,-50" fill="none" stroke="#10b981" stroke-width="2" filter="drop-shadow(0 0 3px #059669)"/>
</svg>

<nav class="navbar navbar-expand-lg navbar-dark premium-navbar shadow-lg mb-4">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="dashboard.php">
            <i class="bi bi-bus-front-fill me-2 text-primary" style="filter: drop-shadow(0 0 8px #2563eb);"></i> 
            <span class="brand-accent">SRMSS</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <?php 
                $current_page = basename($_SERVER['PHP_SELF']); 
            ?>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 mt-2 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active-hub' : ''; ?>" href="dashboard.php">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'routes.php') ? 'active-hub' : ''; ?>" href="routes.php">
                        <i class="bi bi-map-fill me-1"></i> Routes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'schedules.php') ? 'active-hub' : ''; ?>" href="schedules.php">
                        <i class="bi bi-calendar3 me-1"></i> Schedules
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'drivers.php') ? 'active-hub' : ''; ?>" href="drivers.php">
                        <i class="bi bi-person-badge-fill me-1"></i> Drivers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'vehicles.php') ? 'active-hub' : ''; ?>" href="vehicles.php">
                        <i class="bi bi-truck-flatbed me-1"></i> Vehicles
                    </a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                <div class="user-identity-badge d-flex align-items-center shadow-sm">
                    <i class="bi bi-person-circle text-info me-2 fs-5"></i>
                    <span class="small me-2 text-white-50">
                        Logged as: <strong class="text-white"><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                    </span>
                    <span class="role-pill fw-bold text-uppercase"><?php echo htmlspecialchars($_SESSION['role']); ?></span>
                </div>
                
                <a href="logout.php" class="btn btn-logout-premium btn-sm px-3 py-2 fw-medium d-flex align-items-center gap-1">
                    <i class="bi bi-box-arrow-right"></i> Terminate Session
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 workspace-anchor">