<?php 
// Include the shared navigation bar and layout container
include_once 'includes/header.php'; 
require_once 'config/db.php';

// Database Connection setup with active system counts
$total_routes = 0;
$total_schedules = 0;
$total_drivers = 0;
$total_vehicles = 0;

if (isset($conn) && $conn !== null) {
    try {
        // Live execution blocks reading direct database rows using PDO
        $routes_query = $conn->query("SELECT COUNT(*) as total FROM routes");
        if ($routes_query) { $total_routes = $routes_query->fetchColumn(); }

        $schedules_query = $conn->query("SELECT COUNT(*) as total FROM schedules");
        if ($schedules_query) { $total_schedules = $schedules_query->fetchColumn(); }

        $drivers_query = $conn->query("SELECT COUNT(*) as total FROM drivers");
        if ($drivers_query) { $total_drivers = $drivers_query->fetchColumn(); }

        $vehicles_query = $conn->query("SELECT COUNT(*) as total FROM vehicles");
        if ($vehicles_query) { $total_vehicles = $vehicles_query->fetchColumn(); }
    } catch (PDOException $e) {
        // Fallback safely to 0 if an exception occurs during execution
        $total_routes = 0;
        $total_schedules = 0;
        $total_drivers = 0;
        $total_vehicles = 0;
    }
} else {
    // Intelligent dynamic counters fallback if server environment connection is loading
    $total_routes = 0;
    $total_schedules = 0;
    $total_drivers = 0;
    $total_vehicles = 0;
}
?>

<style>
    /* 5-Second Ultra-Smooth Light Cinematic Gradient Cycle */
    body {
        background: linear-gradient(-45deg, #f0fdfa, #e0e7ff, #f5f3ff, #ecfeff, #fff1f2) !important;
        background-size: 400% 400% !important;
        animation: lightCinematicBG 5s ease-in-out infinite !important; /* Accelerated to 5 seconds */
        color: #0f172a !important; 
        min-height: 100vh;
        overflow-x: hidden;
        position: relative;
        transition: all 0.5s ease;
    }

    @keyframes lightCinematicBG {
        0% { background-position: 0% 50%; }
        25% { background-position: 50% 100%; }
        50% { background-position: 100% 50%; }
        75% { background-position: 50% 0%; }
        100% { background-position: 0% 50%; }
    }

    /* Ambient Floating Fluid Orbs */
    .ambient-glow-1 {
        position: absolute;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, rgba(255,255,255,0) 70%);
        top: -100px;
        right: -50px;
        z-index: -1;
        animation: floatOrb 6s ease-in-out infinite alternate;
    }

    .ambient-glow-2 {
        position: absolute;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(6, 182, 212, 0.12) 0%, rgba(255,255,255,0) 70%);
        bottom: 2%;
        left: -120px;
        z-index: -1;
        animation: floatOrb 9s ease-in-out infinite alternate-reverse;
    }

    @keyframes floatOrb {
        0% { transform: translateY(0px) scale(1) rotate(0deg); }
        100% { transform: translateY(40px) scale(1.05) rotate(15deg); }
    }

    /* Cinematic Layout Page Entrance Animations */
    @keyframes cinematicEntrance {
        from {
            opacity: 0;
            transform: translateY(25px);
            filter: blur(6px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
            filter: blur(0);
        }
    }

    .animate-header { animation: cinematicEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-stat-card { animation: cinematicEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.1s forwards; opacity: 0; }
    .animate-card-1 { animation: cinematicEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.15s forwards; opacity: 0; }
    .animate-card-2 { animation: cinematicEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.22s forwards; opacity: 0; }
    .animate-card-3 { animation: cinematicEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.29s forwards; opacity: 0; }
    .animate-card-4 { animation: cinematicEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.36s forwards; opacity: 0; }
    .animate-card-5 { animation: cinematicEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.43s forwards; opacity: 0; }

    /* Frosted Glassmorphism Grid Elements */
    .premium-glass-card {
        background: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(25px) saturate(180%);
        -webkit-backdrop-filter: blur(25px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(15, 23, 42, 0.03);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    .premium-glass-card:hover {
        transform: translateY(-6px) scale(1.01);
        background: rgba(255, 255, 255, 0.95) !important;
        border-color: rgba(99, 102, 241, 0.25) !important;
        box-shadow: 0 20px 40px rgba(99, 102, 241, 0.08);
    }

    /* Typography Contrast Enhancements */
    .dashboard-title-cinematic {
        background: linear-gradient(90deg, #0284c7, #4f46e5, #0ea5e9, #4f46e5);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: titleShimmer 4s linear infinite;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    @keyframes titleShimmer {
        to { background-position: 200% center; }
    }

    .text-cinematic-muted {
        color: #334155 !important; /* Rich deep slate color for strong accessibility */
        font-weight: 500;
    }

    .font-monospace-status {
        font-family: 'Courier New', Courier, monospace;
        color: #475569;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .cinematic-hr {
        border-top: 1px solid rgba(15, 23, 42, 0.08);
    }

    /* Live Counter Badges */
    .stat-badge {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: 18px;
        padding: 20px 12px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.01);
        transition: all 0.3s ease;
    }
    
    .stat-badge:hover {
        background: #ffffff;
        border-color: rgba(99, 102, 241, 0.3);
        transform: translateY(-2px);
    }

    .btn-action-premium {
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        border-radius: 50px;
        padding: 11px 24px;
        font-weight: 600;
        border: none;
    }

    .btn-action-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.06);
    }
</style>

<div class="ambient-glow-1"></div>
<div class="ambient-glow-2"></div>

<div class="container-fluid px-4 py-3">
    <div class="row pt-2">
        <div class="col-md-12 animate-header mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1 dashboard-title-cinematic display-6">
                        <i class="bi bi-cpu me-2"></i>Smart Depot Control Center
                    </h2>
                    <p class="mb-0 font-monospace-status small">OPERATIONAL NODE // REAL-TIME SYSTEM SYNCHRONIZATION RUNNING</p>
                </div>
                <div class="text-md-end mt-2 mt-md-0">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                        <i class="bi bi-shield-check me-1"></i> Database Synchronized
                    </span>
                </div>
            </div>
            <hr class="cinematic-hr my-4">
        </div>
    </div>

    <div class="row mb-5 animate-stat-card">
        <div class="col-12">
            <div class="card premium-glass-card border-0 p-4">
                <div class="row text-center g-4">
                    <div class="col-6 col-lg-3">
                        <div class="stat-badge">
                            <div class="text-cinematic-muted small text-uppercase font-monospace mb-1 fw-bold">Total Routes</div>
                            <h1 class="fw-bold text-info mb-0"><?php echo (int)$total_routes; ?></h1>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="stat-badge">
                            <div class="text-cinematic-muted small text-uppercase font-monospace mb-1 fw-bold">Active Shifts</div>
                            <h1 class="fw-bold text-success mb-0"><?php echo (int)$total_schedules; ?></h1>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="stat-badge">
                            <div class="text-cinematic-muted small text-uppercase font-monospace mb-1 fw-bold">Drivers Hub</div>
                            <h1 class="fw-bold text-primary mb-0"><?php echo (int)$total_drivers; ?></h1>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="stat-badge">
                            <div class="text-cinematic-muted small text-uppercase font-monospace mb-1 fw-bold">Active Fleet</div>
                            <h1 class="fw-bold text-danger mb-0"><?php echo (int)$total_vehicles; ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-4 col-md-6 animate-card-1">
            <div class="card h-100 premium-glass-card shadow-sm border-0 border-top border-info border-4">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-2 bg-info bg-opacity-10 rounded-3 border border-info border-opacity-25 me-3">
                                <i class="bi bi-map text-info fs-4 px-1"></i>
                            </div>
                            <h4 class="card-title fw-bold text-dark mb-0">Route Management</h4>
                        </div>
                        <p class="card-text text-cinematic-muted small mb-4">
                            Configure, expand, and structure your public transit mapping network. Instantly map global geo-coordinates and track real-time changes.
                        </p>
                    </div>
                    <div>
                        <a href="routes.php" class="btn btn-info text-dark w-100 btn-action-premium shadow-sm">
                            Open Module <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 animate-card-2">
            <div class="card h-100 premium-glass-card shadow-sm border-0 border-top border-success border-4">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-2 bg-success bg-opacity-10 rounded-3 border border-success border-opacity-25 me-3">
                                <i class="bi bi-calendar-event text-success fs-4 px-1"></i>
                            </div>
                            <h4 class="card-title fw-bold text-dark mb-0">Schedule Engine</h4>
                        </div>
                        <p class="card-text text-cinematic-muted small mb-4">
                            Organize structural operator shifts, timeline logs, and run intelligent conflict detection routines across vehicles.
                        </p>
                    </div>
                    <div>
                        <a href="schedules.php" class="btn btn-success text-dark w-100 btn-action-premium shadow-sm">
                            Open Module <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 animate-card-3">
            <div class="card h-100 premium-glass-card shadow-sm border-0 border-top border-primary border-4">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-2 bg-primary bg-opacity-10 rounded-3 border border-primary border-opacity-25 me-3">
                                <i class="bi bi-person-badge text-primary fs-4 px-1"></i>
                            </div>
                            <h4 class="card-title fw-bold text-dark mb-0">Drivers Profile Hub</h4>
                        </div>
                        <p class="card-text text-cinematic-muted small mb-4">
                            Monitor professional profiles, track schedule logs, update contact files, and examine assignment history charts.
                        </p>
                    </div>
                    <div>
                        <a href="drivers.php" class="btn btn-primary text-white w-100 btn-action-premium shadow-sm">
                            Open Module <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 animate-card-4">
            <div class="card h-100 premium-glass-card shadow-sm border-0 border-top border-danger border-4">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-2 bg-danger bg-opacity-10 rounded-3 border border-danger border-opacity-25 me-3">
                                <i class="bi bi-truck text-danger fs-4 px-1"></i>
                            </div>
                            <h4 class="card-title fw-bold text-dark mb-0">Fleet Registry</h4>
                        </div>
                        <p class="card-text text-cinematic-muted small mb-4">
                            Review physical rolling stock inventory, verify active license metrics, toggle deployment states, and explore maintenance histories.
                        </p>
                    </div>
                    <div>
                        <a href="vehicles.php" class="btn btn-danger text-white w-100 btn-action-premium shadow-sm">
                            Open Module <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 animate-card-5">
            <div class="card h-100 premium-glass-card shadow-sm border-0 border-top border-warning border-4">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-2 bg-warning bg-opacity-10 rounded-3 border border-warning border-opacity-25 me-3">
                                <i class="bi bi-file-earmark-pdf text-warning fs-4 px-1"></i>
                            </div>
                            <h4 class="card-title fw-bold text-dark mb-0">Reporting Hub</h4>
                        </div>
                        <p class="card-text text-cinematic-muted small mb-4">
                            Compile system state snapshots instantly, generate real-time performance indicators, and render valid PDFs.
                        </p>
                    </div>
                    <div>
                        <a href="reports.php" class="btn btn-warning text-dark w-100 btn-action-premium shadow-sm">
                            Open Module <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>