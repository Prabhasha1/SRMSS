<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRMSS - Account Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* Cinematic Fluid Light Gradient Animation Workspace Base */
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        body {
            background: linear-gradient(-45deg, #f0fdfa, #e0e7ff, #f5f3ff, #ecfeff, #fff1f2);
            background-size: 400% 400%;
            animation: gradientShift 12s ease infinite;
            color: #0f172a;
            min-height: 100vh;
            overflow: hidden;
            font-family: system-ui, -apple-system, sans-serif;
            position: relative;
        }

        /* Ambient Transit Depot SVG Network Map Overlays with Light Blend Filters */
        .depot-network-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
            opacity: 0.75;
            mix-blend-mode: multiply;
        }

        /* SVG Vector Glow Route Paths Dash Animations */
        .transit-path {
            stroke-dasharray: 30, 150;
            animation: routeFlow 9s linear infinite;
        }
        .transit-path-fast {
            stroke-dasharray: 50, 130;
            animation: routeFlow 6s linear infinite;
        }
        @keyframes routeFlow {
            from { stroke-dashoffset: 300; }
            to { stroke-dashoffset: 0; }
        }

        /* Pulsing Node Terminals Graphic Engine Hooks */
        .depot-node {
            animation: nodePulse 3s ease-in-out infinite alternate;
            transform-origin: center;
        }
        @keyframes nodePulse {
            0% { r: 4px; opacity: 0.5; filter: drop-shadow(0 0 2px rgba(16,185,129,0.3)); }
            100% { r: 8px; opacity: 1; filter: drop-shadow(0 0 10px rgba(16,185,129,0.7)); }
        }

        /* Main Workspace Separation Wrapper */
        .main-card-wrapper {
            position: relative;
            z-index: 5;
            animation: cardEntrance 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            width: 450px;
        }
        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(30px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Brand Profile Header Segment - Separated Element */
        .brand-profile-container {
            background: rgba(255, 255, 255, 0.4);
            border: 1px solid rgba(15, 23, 42, 0.04);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.02);
        }

        /* Frosted Glassmorphism Premium Form Container */
        .register-card {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(30px) saturate(190%);
            -webkit-backdrop-filter: blur(30px) saturate(190%);
            border: 1px solid rgba(255, 255, 255, 0.7) !important;
            border-radius: 24px;
            box-shadow: 0 30px 60px rgba(15, 23, 42, 0.08), inset 0 1px 2px rgba(255,255,255,0.9) !important;
        }

        /* High Visibility Interactive Input and Select Fields */
        .form-control, .form-select {
            background-color: #ffffff !important;
            border: 2px solid #cbd5e1 !important; /* Highly defined slate outline */
            color: #0f172a !important;
            padding: 12px 16px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .form-control:focus, .form-select:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15) !important;
            transform: scale(1.01);
        }
        .form-control::placeholder { color: #94a3b8; }
        
        .input-group-text { 
            background-color: #f8fafc !important; 
            border: 2px solid #cbd5e1 !important; 
            border-right: none !important;
            color: #475569; 
            font-size: 1.1rem;
        }
        
        .input-group .form-control, .input-group .form-select {
            border-left: none !important;
        }
        
        option { background-color: #ffffff; color: #0f172a; }

        /* Form Row Labels */
        .form-label-highlight {
            color: #1e293b !important;
            font-weight: 700 !important;
            letter-spacing: 0.3px;
        }

        /* Micro Interactive Action Triggers */
        .btn-submit {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(16, 185, 129, 0.3);
            filter: brightness(1.05);
        }
        
        .brand-icon-wrapper {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #10b981, #34d399);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            margin-right: 16px;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.2);
        }

        .login-link { 
            transition: all 0.2s ease; 
            color: #2563eb !important;
            background: rgba(37, 99, 235, 0.05);
            padding: 10px 16px;
            border-radius: 30px;
            display: inline-block;
        }
        .login-link:hover { 
            background: rgba(37, 99, 235, 0.1);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100 py-4">

<svg class="depot-network-canvas" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice">
    <defs>
        <pattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse">
            <path d="M 60 0 L 0 0 0 60" fill="none" stroke="rgba(15,23,42,0.015)" stroke-width="1"/>
        </pattern>
    </defs>
    <rect width="100%" height="100%" fill="url(#grid)" />

    <path d="M-100,200 L400,200 L600,450 L1600,450" fill="none" stroke="rgba(15,23,42,0.03)" stroke-width="3" />
    <path class="transit-path" d="M-100,200 L400,200 L600,450 L1600,450" fill="none" stroke="#10b981" stroke-width="3" opacity="0.6" filter="drop-shadow(0 0 4px rgba(16,185,129,0.3))"/>

    <path d="M200,1000 L500,600 L800,450 L900,-100" fill="none" stroke="rgba(15,23,42,0.03)" stroke-width="2" />
    <path class="transit-path-fast" d="M200,1000 L500,600 L800,450 L900,-100" fill="none" stroke="#2563eb" stroke-width="2" opacity="0.6" filter="drop-shadow(0 0 4px rgba(37,99,235,0.3))"/>
    
    <circle class="depot-node" cx="400" cy="200" r="5" fill="#10b981" />
    <circle class="depot-node" cx="600" cy="450" r="6" fill="#10b981" />
    <circle class="depot-node" cx="500" cy="600" r="5" fill="#2563eb" />
    <circle class="depot-node" cx="800" cy="450" r="5" fill="#2563eb" />
</svg>

<div class="main-card-wrapper">

    <div class="brand-profile-container d-flex align-items-center">
        <div class="brand-icon-wrapper">
            <i class="bi bi-person-plus-fill fs-4"></i>
        </div>
        <div>
            <h4 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">Create Account</h4>
            <p class="text-muted small fw-medium mb-0">Register new Depot Staff or System Administrators</p>
        </div>
    </div>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger p-3 bg-danger-subtle text-danger border-0 small rounded-3 mb-3 text-center shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>System Alert:</strong> <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success p-3 bg-success-subtle text-success border-0 small rounded-3 mb-3 text-center shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><strong>Success:</strong> <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>

    <div class="card register-card border-0">
        <div class="card-body p-4 p-md-4">
            <form action="register_process.php" method="POST">
                
                <div class="mb-3">
                    <label for="username" class="form-label small form-label-highlight mb-2">Username or ID String</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Input unique username..." required autocomplete="off">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="role" class="form-label small form-label-highlight mb-2">Assigned Depot System Role</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-briefcase-fill"></i></span>
                        <select name="role" id="role" class="form-select" required>
                            <option value="" disabled selected>Select assigned role...</option>
                            <option value="Administrator">Administrator</option>
                            <option value="Supervisor">Supervisor</option>
                            <option value="Depot Staff">Depot Staff</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label small form-label-highlight mb-2">Secure Gateway Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••••••" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="confirm_password" class="form-label small form-label-highlight mb-2">Confirm String Matching Verification</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="••••••••••••" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success btn-submit text-white w-100 mb-3 shadow-sm">
                    Register Account Profile <i class="bi bi-check2-circle ms-1"></i>
                </button>
                
                <div class="text-center pt-2">
                    <a href="login.php" class="text-decoration-none small fw-bold login-link">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Already have an account? Sign In
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>