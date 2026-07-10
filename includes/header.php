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

// Include database connection to fetch real-time session information if needed
require_once 'config/db.php';

// Fetch the most updated profile details from the database for the header display
$session_user_id = $_SESSION['user_id'];
$header_user_name = $_SESSION['username'] ?? 'User';
$header_user_role = $_SESSION['role'] ?? 'Staff';

// FIX: Rewritten to use PDO syntax instead of MySQLi
$stmt = $conn->prepare("SELECT username, role FROM users WHERE id = ?");
if ($stmt) {
    $stmt->execute([$session_user_id]);
    if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $header_user_name = $user['username'];
        $header_user_role = $user['role'];
        // Sync session data
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
    }
}

// Get current page script name for active link highlighting
$current_page = basename($_SERVER['PHP_SELF']);

// Automatically load footer.php when the page finishes executing
register_shutdown_function(function() {
    require_once __DIR__ . '/footer.php';
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRMSS - Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Light Cinematic Dynamic Background Layer Integration */
        body {
            background: linear-gradient(-45deg, #f0fdfa, #e0e7ff, #f5f3ff, #ecfeff, #fff1f2) !important;
            background-size: 400% 400% !important;
            animation: lightCinematicBG 15s ease-in-out infinite !important;
            color: #0f172a !important; 
            min-height: 100vh;
            position: relative;
            transition: background 0.4s ease, color 0.4s ease;
        }

        @keyframes lightCinematicBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% center; }
        }

        /* PREMIUM DARK MODE STYLES OVERRIDES */
        body.dark-mode {
            background: linear-gradient(-45deg, #0f172a, #1e1b4b, #1e1b4b, #0f172a) !important;
            background-size: 400% 400% !important;
            animation: lightCinematicBG 15s ease-in-out infinite !important;
            color: #f8fafc !important;
        }

        /* Frosted Glassmorphism Premium Navbar Styling */
        .premium-light-nav {
            background: rgba(255, 255, 255, 0.75) !important;
            backdrop-filter: blur(20px) saturate(160%);
            -webkit-backdrop-filter: blur(20px) saturate(160%);
            border-bottom: 1px solid rgba(15, 23, 42, 0.06) !important;
            box-shadow: 0 4px 30px rgba(15, 23, 42, 0.02) !important;
            padding-top: 12px;
            padding-bottom: 12px;
            transition: all 0.4s ease;
        }

        body.dark-mode .premium-light-nav {
            background: rgba(15, 23, 42, 0.75) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3) !important;
        }

        /* Cinematic Logo Shimmer Text styling */
        .brand-cinematic {
            background: linear-gradient(90deg, #6366f1, #38bdf8, #6366f1);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: brandShimmer 4s linear infinite;
            letter-spacing: -0.5px;
        }

        @keyframes brandShimmer {
            to { background-position: 200% center; }
        }

        /* Premium Light Theme Nav Items */
        .premium-light-nav .nav-link {
            color: #475569 !important; /* Deep neutral slate */
            font-weight: 500;
            padding: 8px 16px !important;
            border-radius: 50px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        body.dark-mode .premium-light-nav .nav-link {
            color: #94a3b8 !important;
        }

        .premium-light-nav .nav-link:hover {
            color: #4f46e5 !important;
            background: rgba(99, 102, 241, 0.06);
            transform: translateY(-1px);
        }

        body.dark-mode .premium-light-nav .nav-link:hover {
            color: #38bdf8 !important;
            background: rgba(56, 189, 248, 0.1);
        }

        /* Active Cinematic Link State overrides */
        .premium-light-nav .nav-link.active-cinematic {
            color: #ffffff !important;
            background: linear-gradient(135deg, #4f46e5, #6366f1) !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }

        body.dark-mode .premium-light-nav .nav-link.active-cinematic {
            background: linear-gradient(135deg, #38bdf8, #4f46e5) !important;
            box-shadow: 0 4px 12px rgba(56, 189, 248, 0.2);
        }

        /* User badge identity details segment */
        .user-text-badge {
            color: #334155 !important;
            font-weight: 500;
            background: rgba(15, 23, 42, 0.04);
            padding: 8px 16px;
            border-radius: 50px;
            border: 1px solid rgba(15, 23, 42, 0.03);
            transition: all 0.3s ease;
        }

        body.dark-mode .user-text-badge {
            color: #cbd5e1 !important;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        body.dark-mode .text-muted {
            color: #94a3b8 !important;
        }

        /* Theme Toggle Button */
        .btn-theme-toggle {
            border-radius: 50px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border: 1px solid rgba(15, 23, 42, 0.08);
            background: rgba(15, 23, 42, 0.04);
            color: #475569;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        body.dark-mode .btn-theme-toggle {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fbbf24;
        }

        .btn-theme-toggle:hover {
            transform: scale(1.05);
            background: rgba(99, 102, 241, 0.1);
            color: #4f46e5;
        }

        body.dark-mode .btn-theme-toggle:hover {
            background: rgba(251, 191, 36, 0.15);
            color: #f59e0b;
        }

        /* Clean action logout trigger */
        .btn-logout-premium {
            border-radius: 50px;
            padding: 8px 18px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .btn-logout-premium:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.15);
        }
    </style>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark-theme-init');
                document.write('<style>body { background: #0f172a !important; color: #f8fafc !important; }</style>');
            }
        })();
    </script>
</head>
<body class="d-flex flex-column min-vh-100">
<script>
    // Apply dark mode class immediately to body upon execution
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
    }
</script>

<nav class="navbar navbar-expand-lg premium-light-nav mb-4">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-extrabold brand-cinematic fs-4 d-flex align-items-center" href="dashboard.php">
            <i class="bi bi-bus-front me-2"></i>SRMSS
        </a>
        <button class="navbar-toggler border-0 shadow-none text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto g-2">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active-cinematic' : ''; ?>" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'routes.php') ? 'active-cinematic' : ''; ?>" href="routes.php">Routes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'schedules.php') ? 'active-cinematic' : ''; ?>" href="schedules.php">Schedules</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'drivers.php') ? 'active-cinematic' : ''; ?>" href="drivers.php">Drivers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'vehicles.php') ? 'active-cinematic' : ''; ?>" href="vehicles.php">Vehicles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'profile.php') ? 'active-cinematic' : ''; ?>" href="profile.php">My Profile</a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center flex-wrap gap-2 mt-2 mt-lg-0">
                <button id="themeToggleBtn" class="btn btn-theme-toggle me-1" title="Toggle Dark/Light Mode" type="button">
                    <i id="themeToggleIcon" class="bi bi-moon-stars-fill"></i>
                </button>

                <a href="profile.php" class="text-decoration-none nav-link p-0 me-lg-2">
                    <span class="navbar-text user-text-badge small d-flex align-items-center m-0">
                        <i class="bi bi-person-circle me-2 text-primary fs-5"></i> 
                        <span>
                            <strong><?php echo htmlspecialchars($header_user_name); ?></strong> 
                            <span class="text-muted small">(<?php echo htmlspecialchars($header_user_role); ?>)</span>
                        </span>
                    </span>
                </a>
                <a href="logout.php" class="btn btn-outline-danger btn-sm btn-logout-premium">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const themeToggleIcon = document.getElementById('themeToggleIcon');

    // Sync button UI icon on initial load
    if (document.body.classList.contains('dark-mode')) {
        themeToggleIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
    } else {
        themeToggleIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
    }

    // Toggle click event
    themeToggleBtn.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        
        if (document.body.classList.contains('dark-mode')) {
            localStorage.setItem('theme', 'dark');
            themeToggleIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
        } else {
            localStorage.setItem('theme', 'light');
            themeToggleIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
        }
    });
</script>

<div class="container-fluid px-4 flex-grow-1">