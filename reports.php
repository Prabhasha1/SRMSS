<?php 
require_once 'config/db.php';
require_once 'config/auth.php'; // Forces authentication
restrictToRoles(['Admin', 'Manager', 'Administrator']); // Only allows specific roles

include_once 'includes/header.php'; 

// Fetch filter metrics dropdown options
$allRoutes = $conn->query("SELECT id, route_number, start_location, end_location FROM routes ORDER BY route_number ASC")->fetchAll(PDO::FETCH_ASSOC);
$allDrivers = $conn->query("SELECT id, first_name, last_name FROM drivers ORDER BY first_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$allVehicles = $conn->query("SELECT id, plate_number, model FROM vehicles ORDER BY plate_number ASC")->fetchAll(PDO::FETCH_ASSOC);

// Base summary operational metrics counts
$totalRoutes = $conn->query("SELECT COUNT(*) FROM routes")->fetchColumn();
$totalDrivers = $conn->query("SELECT COUNT(*) FROM drivers")->fetchColumn();
$totalVehicles = $conn->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
$totalSchedules = $conn->query("SELECT COUNT(*) FROM schedules")->fetchColumn();

// --- PREMIUM FEATURE A: PREDICTIVE FLEET UTILIZATION LOGIC ---
try {
    // Count active vehicles scheduled in overlapping/incomplete upcoming frames
    $activeBookings = $conn->query("SELECT COUNT(DISTINCT vehicle_id) FROM schedules WHERE arrival_time > NOW()")->fetchColumn();
    $utilizationRate = ($totalVehicles > 0) ? round(($activeBookings / $totalVehicles) * 100) : 0;
} catch (Exception $e) {
    $utilizationRate = 0; // Fail-safe fallback
}
// --- END PREDICTIVE LOGIC ---

// Interactive Business Logic: Handle filter configurations parameters
$whereClauses = [];
$bindings = [];

if (!empty($_GET['route_id'])) {
    $whereClauses[] = "s.route_id = :route_id";
    $bindings['route_id'] = $_GET['route_id'];
}
if (!empty($_GET['driver_id'])) {
    $whereClauses[] = "s.driver_id = :driver_id";
    $bindings['driver_id'] = $_GET['driver_id'];
}
if (!empty($_GET['vehicle_id'])) {
    $whereClauses[] = "s.vehicle_id = :vehicle_id";
    $bindings['vehicle_id'] = $_GET['vehicle_id'];
}
if (!empty($_GET['start_date'])) {
    $whereClauses[] = "s.departure_time >= :start_date";
    $bindings['start_date'] = $_GET['start_date'] . " 00:00:00";
}
if (!empty($_GET['end_date'])) {
    $whereClauses[] = "s.departure_time <= :end_date";
    $bindings['end_date'] = $_GET['end_date'] . " 23:59:59";
}

// Build query dynamically based on parameters
$queryStr = "SELECT s.id, r.route_number, r.start_location, r.end_location, 
                    d.first_name, d.last_name, v.plate_number, v.model, s.departure_time, s.arrival_time 
             FROM schedules s
             JOIN routes r ON s.route_id = r.id
             JOIN drivers d ON s.driver_id = d.id
             JOIN vehicles v ON s.vehicle_id = v.id";

if (!empty($whereClauses)) {
    $queryStr .= " WHERE " . implode(" AND ", $whereClauses);
}
$queryStr .= " ORDER BY s.departure_time DESC";

$stmt = $conn->prepare($queryStr);
$stmt->execute($bindings);
$reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
/* Dashboard Layout Transitions */
.animate-fade { animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
.hover-card { transition: transform 0.3s ease, box-shadow 0.3s ease; border-radius: 12px !important; }
.hover-card:hover { transform: translateY(-3px); box-shadow: 0 12px 20px rgba(0,0,0,0.05) !important; }

/* Pulse animation rule for En Route badge status indicator */
@keyframes pulseGlow {
    0% { transform: scale(0.90); opacity: 0.6; }
    50% { transform: scale(1); opacity: 1; }
    100% { transform: scale(0.90); opacity: 0.6; }
}
.animated-pulse-dot {
    animation: pulseGlow 1.6s infinite ease-in-out;
}

/* Native Print Styles for Clean Layout Parsing */
@media print {
    body { background-color: #fff !important; color: #000 !important; font-size: 13px; }
    .navbar, .btn, hr, .no-print, form, .card-header .btn-group, .alert { display: none !important; }
    .container-fluid { padding: 0 !important; }
    .card { border: none !important; box-shadow: none !important; background: transparent !important; }
    .card-header { background-color: #f8f9fa !important; color: #000 !important; border-bottom: 2px solid #000 !important; }
    .print-header { display: block !important; text-align: center; margin-bottom: 25px; border-bottom: 3px double #2d3748; padding-bottom: 15px; }
    .badge { border: 1px solid #000 !important; background: transparent !important; color: #000 !important; font-weight: bold; }
    table { width: 100% !important; border-collapse: collapse !important; }
    th, td { border: 1px solid #dee2e6 !important; padding: 8px !important; }
}
.print-header { display: none; }
</style>

<div class="container-fluid py-4 animate-fade" style="background-color: #f7fafc; min-height: 90vh;">
    
    <div class="row print-header">
        <div class="col-md-12 text-center">
            <h2 class="fw-bold text-dark mb-1">Smart Route Management & Scheduling System (SRMSS)</h2>
            <h4 class="text-secondary fw-semibold">Official Operational Logistics Compliance Report</h4>
            <p class="text-muted small mb-0">
                Generated: <?php echo date('Y-m-d H:i:s'); ?> | Authority Profile: Central Management Depot Admin
            </p>
        </div>
    </div>

    <div class="row mb-3 d-flex justify-content-between align-items-center no-print">
        <div class="col-md-8">
            <h2 class="fw-bold" style="color: #2d3748;"><i class="bi bi-file-earmark-bar-graph text-primary me-2"></i>Reporting & Logistics Analytics Hub</h2>
            <p class="text-muted mb-0">Configure advanced filter parameters, view data tables, and print certified audit records.</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="text-end text-muted small fw-bold mb-2">
                <i class="bi bi-clock-history text-primary me-1"></i>System Live Time: <span id="liveClock" class="font-monospace">--:--:--</span>
            </div>
            <button onclick="window.print();" class="btn text-white fw-bold px-4 py-2 shadow-sm rounded-3" style="background-color: #2b6cb0;">
                <i class="bi bi-printer-fill me-1"></i> Export PDF Report
            </button>
        </div>
    </div>
    
    <?php if ($utilizationRate >= 85): ?>
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm rounded-3 p-3 mb-3 no-print" role="alert">
            <div class="d-flex align-items-center">
                <span class="fs-4 me-3">⚠️</span>
                <div>
                    <strong class="text-dark">High System Utilization Alert:</strong> Depot capacity is operating at <span class="badge bg-danger text-white"><?php echo $utilizationRate; ?>%</span>. Consider opening backup vehicle standby assets.
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="no-print"><hr style="border-color: #e2e8f0;"></div>

    <div class="card shadow-sm border-0 bg-white p-4 mb-4 no-print rounded-3">
        <form method="GET" action="reports.php">
            <h6 class="fw-bold mb-3 text-secondary text-uppercase small"><i class="bi bi-funnel-fill me-1"></i> Live Parameters Analytics Filter</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Filter by Transit Route</label>
                    <select name="route_id" class="form-select border-0 bg-light py-2">
                        <option value="">-- All Active Routes --</option>
                        <?php foreach($allRoutes as $route): ?>
                            <option value="<?php echo $route['id']; ?>" <?php if(isset($_GET['route_id']) && $_GET['route_id'] == $route['id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($route['route_number'] . " (" . $route['start_location'] . " - " . $route['end_location'] . ")"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Assigned Operator</label>
                    <select name="driver_id" class="form-select border-0 bg-light py-2">
                        <option value="">-- All Drivers --</option>
                        <?php foreach($allDrivers as $driver): ?>
                            <option value="<?php echo $driver['id']; ?>" <?php if(isset($_GET['driver_id']) && $_GET['driver_id'] == $driver['id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($driver['first_name'] . ' ' . $driver['last_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Fleet Unit No.</label>
                    <select name="vehicle_id" class="form-select border-0 bg-light py-2">
                        <option value="">-- All Vehicles --</option>
                        <?php foreach($allVehicles as $vehicle): ?>
                            <option value="<?php echo $vehicle['id']; ?>" <?php if(isset($_GET['vehicle_id']) && $_GET['vehicle_id'] == $vehicle['id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($vehicle['plate_number'] . " - " . $vehicle['model']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Start Date Bound</label>
                    <input type="date" name="start_date" class="form-control border-0 bg-light py-2" value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">End Date Bound</label>
                    <input type="date" name="end_date" class="form-control border-0 bg-light py-2" value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-dark w-100 py-2 fw-bold"><i class="bi bi-search"></i></button>
                </div>
            </div>
            <?php if(!empty($_GET)): ?>
                <div class="text-start mt-2">
                    <a href="reports.php" class="text-decoration-none small fw-bold text-danger"><i class="bi bi-x-circle-fill"></i> Clear Analytics Filters</a>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <div class="row mb-2">
        <div class="col-md-3 col-6 mb-4">
            <div class="card shadow-sm border-0 text-white p-3 hover-card" style="background-color: #3182ce;">
                <h6 class="text-uppercase small mb-1 fw-semibold text-white-50">Total Routes Tracked</h6>
                <h2 class="fw-bold mb-0"><?php echo $totalRoutes; ?></h2>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card shadow-sm border-0 text-white p-3 hover-card" style="background-color: #38a169;">
                <h6 class="text-uppercase small mb-1 fw-semibold text-white-50">Registered Operators</h6>
                <h2 class="fw-bold mb-0"><?php echo $totalDrivers; ?></h2>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card shadow-sm border-0 text-white p-3 hover-card" style="background-color: #e53e3e;">
                <h6 class="text-uppercase small mb-1 fw-semibold text-white-50">Active Fleet Assets</h6>
                <h2 class="fw-bold mb-0"><?php echo $totalVehicles; ?></h2>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-4">
            <div class="card shadow-sm border-0 text-white p-3 hover-card" style="background-color: #2d3748;">
                <h6 class="text-uppercase small mb-1 fw-semibold text-white-50">Dispatched Shifts</h6>
                <h2 class="fw-bold mb-0"><?php echo $totalSchedules; ?></h2>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 bg-white hover-card">
                <div class="card-header bg-dark text-white fw-bold py-3 d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-calendar4-week me-2"></i>Comprehensive Depot Dispatch Log Matrix</span>
                    <span class="badge bg-light text-dark fw-bold px-2 py-1"><?php echo count($reportData); ?> Result Record Logs Found</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="color: #2d3748;">
                            <thead class="table-light text-secondary small text-uppercase fw-semibold" style="border-bottom: 2px solid #e2e8f0;">
                                <tr>
                                    <th class="ps-4 py-3">Route Code</th>
                                    <th>Transit Leg Journey</th>
                                    <th>Assigned Driver</th>
                                    <th>Vehicle Fleet No.</th>
                                    <th>Departure Time</th>
                                    <th>Expected Arrival</th>
                                    <th>Shift Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($reportData)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="bi bi-folder-x display-6 d-block mb-2 text-secondary"></i>
                                            No master dispatch records match the selected database system constraints.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reportData as $row): ?>
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <td class="ps-4">
                                                <strong class="text-primary" style="letter-spacing: 0.3px;"><?php echo htmlspecialchars($row['route_number']); ?></strong>
                                            </td>
                                            <td class="fw-semibold"><?php echo htmlspecialchars($row['start_location'] . " ➔ " . $row['end_location']); ?></td>
                                            <td><i class="bi bi-person text-secondary me-1"></i><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                            <td>
                                                <span class="badge bg-dark text-white px-2.5 py-1.5 font-monospace" style="font-size: 11px;">
                                                    <?php echo htmlspecialchars($row['plate_number']); ?>
                                                </span>
                                                <small class="text-muted d-block font-sans" style="font-size: 11px;"><?php echo htmlspecialchars($row['model']); ?></small>
                                            </td>
                                            <td><span class="text-secondary small fw-semibold"><?php echo date('M d, Y H:i', strtotime($row['departure_time'])); ?></span></td>
                                            <td><span class="text-secondary small fw-semibold"><?php echo date('M d, Y H:i', strtotime($row['arrival_time'])); ?></span></td>
                                            <td>
                                                <?php 
                                                $currentTime = time();
                                                $departureTime = strtotime($row['departure_time']);
                                                $arrivalTime = strtotime($row['arrival_time']);

                                                if ($currentTime < $departureTime) {
                                                    echo '<span class="badge bg-primary px-2.5 py-1.5 rounded-pill"><i class="bi bi-hourglass-split me-1"></i>Upcoming</span>';
                                                } elseif ($currentTime >= $departureTime && $currentTime <= $arrivalTime) {
                                                    echo '<span class="badge bg-success px-2.5 py-1.5 rounded-pill animated-pulse-dot"><span class="spinner-grow spinner-grow-sm me-1" style="width: 7px; height: 7px;" role="status"></span>En Route</span>';
                                                } else {
                                                    echo '<span class="badge bg-secondary px-2.5 py-1.5 rounded-pill opacity-75"><i class="bi bi-check-circle-fill me-1"></i>Completed</span>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function updateClock() {
    const now = new Date();
    document.getElementById('liveClock').innerText = now.toLocaleTimeString();
}
setInterval(updateClock, 1000);
updateClock(); // Run immediately on view construction parsing
</script>

</body>
</html>