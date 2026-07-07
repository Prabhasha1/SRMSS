<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRMSS - Secure Login Desk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* Cinematic Fluid Gradient Animation Workspace Base */
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        body {
            background: linear-gradient(-45deg, #070a13, #0f172a, #1e3a8a, #064e3b, #111827);
            background-size: 400% 400%;
            animation: gradientShift 18s ease infinite;
            color: #f8fafc;
            min-height: 100vh;
            overflow: hidden;
            font-family: system-ui, -apple-system, sans-serif;
            position: relative;
        }

        /* Ambient Transit Depot SVG Network Map Overlays with Blend Filters */
        .depot-network-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
            opacity: 0.55;
            mix-blend-mode: screen;
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
            0% { r: 4px; opacity: 0.3; filter: drop-shadow(0 0 2px rgba(59,130,246,0.4)); }
            100% { r: 8px; opacity: 1; filter: drop-shadow(0 0 10px rgba(59,130,246,0.9)); }
        }

        /* Main Workspace Glass Front-Facing Container Panel */
        .main-card-wrapper {
            position: relative;
            z-index: 5;
            animation: cardEntrance 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(30px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .login-card {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(25px) saturate(200%);
            -webkit-backdrop-filter: blur(25px) saturate(200%);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 24px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5), inset 0 1px 2px rgba(255,255,255,0.15) !important;
            width: 420px;
        }

        /* Form Custom Elements Framework Integration Layers */
        .form-control {
            background-color: rgba(11, 15, 25, 0.7) !important;
            border: 2px solid rgba(255, 255, 255, 0.08);
            color: #ffffff !important;
            padding: 12px 16px;
            border-radius: 12px;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3);
            transform: scale(1.01);
        }
        .form-control::placeholder { color: #475569; }
        .input-group-text { 
            background-color: rgba(11, 15, 25, 0.8) !important; 
            border: 2px solid rgba(255, 255, 255, 0.08); 
            color: #64748b; 
        }

        /* Micro Interactive Elements Controls Hooks Buttons layout */
        .btn-submit {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(37, 99, 235, 0.45);
            filter: brightness(1.15);
        }
        
        .brand-icon-wrapper {
            width: 68px;
            height: 68px;
            background: rgba(37, 99, 235, 0.15);
            color: #3b82f6;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            margin: 0 auto 20px auto;
            border: 1px solid rgba(59, 130, 246, 0.25);
        }
        .signup-link { transition: all 0.2s ease; }
        .signup-link:hover { color: #10b981 !important; letter-spacing: 0.3px; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

<svg class="depot-network-canvas" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice">
    <defs>
        <pattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse">
            <path d="M 60 0 L 0 0 0 60" fill="none" stroke="rgba(255,255,255,0.015)" stroke-width="1"/>
        </pattern>
    </defs>
    <rect width="100%" height="100%" fill="url(#grid)" />

    <path d="M-100,200 L400,200 L600,450 L1600,450" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="3" />
    <path class="transit-path" d="M-100,200 L400,200 L600,450 L1600,450" fill="none" stroke="#3b82f6" stroke-width="3" filter="drop-shadow(0 0 5px #2563eb)"/>

    <path d="M200,1000 L500,600 L800,450 L900,-100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="2" />
    <path class="transit-path-fast" d="M200,1000 L500,600 L800,450 L900,-100" fill="none" stroke="#10b981" stroke-width="2" filter="drop-shadow(0 0 5px #059669)"/>
    
    <circle class="depot-node" cx="400" cy="200" r="5" fill="#3b82f6" />
    <circle class="depot-node" cx="600" cy="450" r="6" fill="#3b82f6" />
    <circle class="depot-node" cx="500" cy="600" r="5" fill="#10b981" />
    <circle class="depot-node" cx="800" cy="450" r="5" fill="#10b981" />
</svg>

<div class="main-card-wrapper">
    <div class="card login-card border-0">
        <div class="card-body p-4 p-md-5">
            
            <div class="text-center">
                <div class="brand-icon-wrapper shadow-sm">
                    <i class="bi bi-bus-front-fill fs-3"></i>
                </div>
                <h3 class="fw-bold text-white mb-1" style="letter-spacing: -0.5px;">SRMSS Gateway</h3>
                <p class="text-muted small fw-medium mb-4">Smart Route Management & Scheduling System</p>
            </div>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger p-3 bg-danger-subtle text-danger border-0 small rounded-3 mb-4 text-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form action="authenticate.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label small fw-bold text-secondary mb-2">Username or ID Key</label>
                    <div class="input-group">
                        <span class="input-group-text px-3"><i class="bi bi-person-fill"></i></span>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Input credential ID string..." required autocomplete="off">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label small fw-bold text-secondary mb-2">Security Account Password</label>
                    <div class="input-group">
                        <span class="input-group-text px-3"><i class="bi bi-shield-lock-fill"></i></span>
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••••••" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-submit text-white w-100 mb-4 shadow-sm">
                    Sign In Matrix Panel <i class="bi bi-box-arrow-in-right ms-1"></i>
                </button>
                
                <div class="text-center border-top border-secondary border-opacity-20 pt-3">
                    <a href="register.php" class="text-decoration-none small text-success fw-bold signup-link">
                        <i class="bi bi-person-plus-fill me-1"></i> Missing depot identity? Register Profile Account
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>