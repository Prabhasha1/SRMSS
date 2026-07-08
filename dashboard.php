<?php 
// Include the shared navigation bar and layout container
include_once 'includes/header.php'; 
?>

<style>
    /* Smooth Light Animated Gradient Background */
    body {
        background: linear-gradient(-45deg, #e0f2fe, #f3e8ff, #fce7f3, #e0e7ff) !important;
        background-size: 400% 400% !important;
        animation: lightGradientBG 12s ease infinite !important;
        color: #1e293b;
    }

    @keyframes lightGradientBG {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Keyframe Animations for Fluid Entrance */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(25px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-header {
        animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* Staggered Card Entrance Timings */
    .animate-card-1 { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.15s forwards; opacity: 0; }
    .animate-card-2 { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.30s forwards; opacity: 0; }
    .animate-card-3 { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.45s forwards; opacity: 0; }

    /* Light Glassmorphism Cards */
    .light-glass-card {
        background: rgba(255, 255, 255, 0.65) !important;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 16px;
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    /* Cinematic Lift & Soft Ambient Glow on Hover */
    .light-glass-card:hover {
        transform: translateY(-8px) scale(1.015);
        background: rgba(255, 255, 255, 0.85) !important;
        box-shadow: 0 20px 35px rgba(99, 102, 241, 0.12), 0 0 20px rgba(255, 255, 255, 0.8);
    }

    /* Vibrant Cinematic Gradient Text Title */
    .dashboard-title-light {
        background: linear-gradient(90deg, #0284c7, #6366f1, #d946ef);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: textShimmer 6s linear infinite;
    }

    @keyframes textShimmer {
        to { background-position: 200% center; }
    }

    /* Custom Subtle Divider */
    .light-hr {
        border-top: 1px solid rgba(0, 0, 0, 0.08);
    }

    /* Interactive Soft Hover Effect for Action Buttons */
    .btn-action {
        transition: all 0.25s ease;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="row pt-2">
    <div class="col-md-12 animate-header mb-4">
        <h2 class="fw-bold mb-1 dashboard-title-light">
            <i class="bi bi-speedometer2 me-1"></i> Depot Management Modules
        </h2>
        <p class="text-secondary mb-3">Welcome to your operational dashboard control center.</p>
        <hr class="light-hr">
    </div>
    
    <div class="col-md-4 mb-4 animate-card-1">
        <div class="card h-100 light-glass-card shadow-sm border-0 border-start border-primary border-4">
            <div class="card-body p-4">
                <h5 class="card-title fw-bold text-primary mb-3">
                    <i class="bi bi-map me-2"></i> Route Management
                </h5>
                <p class="card-text text-secondary small mb-4">
                    Add, edit, view, and search public transport route networks with real-time updates.
                </p>
                <a href="routes.php" class="btn btn-primary btn-sm px-3 rounded-pill fw-semibold btn-action">
                    Open Module <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4 animate-card-2">
        <div class="card h-100 light-glass-card shadow-sm border-0 border-start border-success border-4">
            <div class="card-body p-4">
                <h5 class="card-title fw-bold text-success mb-3">
                    <i class="bi bi-calendar-event me-2"></i> Schedule Engine
                </h5>
                <p class="card-text text-secondary small mb-4">
                    Create operational shifts, map timelines, and run automated conflict detection algorithms.
                </p>
                <a href="schedules.php" class="btn btn-success btn-sm px-3 rounded-pill fw-semibold btn-action">
                    Open Module <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4 animate-card-3">
        <div class="card h-100 light-glass-card shadow-sm border-0 border-start border-warning border-4">
            <div class="card-body p-4">
                <h5 class="card-title fw-bold text-dark mb-3">
                    <i class="bi bi-file-earmark-pdf me-2 text-warning"></i> Reporting Hub
                </h5>
                <p class="card-text text-secondary small mb-4">
                    Monitor real-time logs and export administrative database records to valid compliance PDFs.
                </p>
                <a href="reports.php" class="btn btn-warning btn-sm px-3 rounded-pill fw-semibold btn-action text-dark">
                    Open Module <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>