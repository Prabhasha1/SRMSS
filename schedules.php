<?php 
require_once 'config/db.php';
include_once 'includes/header.php'; 

$message = '';

// 1. Business Logic: Handle Action Deletions/Cancellations
if (isset($_GET['cancel_id'])) {
    $cancel_id = $_GET['cancel_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM schedules WHERE id = :id");
        $stmt->execute(['id' => $cancel_id]);
        $message = '<div class="alert alert-success border-0 shadow-sm animate-fade py-3" style="background-color: #e6fffa; color: #319795; border-left: 4px solid #319795;"><i class="bi bi-check-circle-fill me-2"></i>Trip run sequence deleted from dispatch schedules matrices safely.</div>';
    } catch (PDOException $e) {
        $message = '<div class="alert alert-danger border-0 shadow-sm animate-fade py-3" style="background-color: #fff5f5; color: #e53e3e; border-left: 4px solid #e53e3e;"><i class="bi bi-exclamation-triangle-fill me-2"></i>System Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

// 2. Business Logic: Create Schedule with Conflict Detection & Vehicle Maintenance Safeguard
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_schedule'])) {
    $route_id = $_POST['route_id'];
    $driver_id = $_POST['driver_id'];
    $vehicle_id = $_POST['vehicle_id'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];

    if ($departure_time >= $arrival_time) {
        $message = '<div class="alert alert-danger border-0 shadow-sm animate-fade py-3" style="background-color: #fff5f5; color: #e53e3e; border-left: 4px solid #e53e3e;"><i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Validation Failure:</strong> Destination ETA marker time cannot precede initial departure timestamp.</div>';
    } else {
        try {
            // Requirement C: Vehicle Maintenance Status Health Verification
            $vStmt = $conn->prepare("SELECT status FROM vehicles WHERE id = :vehicle_id");
            $vStmt->execute(['vehicle_id' => $vehicle_id]);
            $vehicleStatus = $vStmt->fetchColumn();

            if ($vehicleStatus === 'Under Maintenance' || $vehicleStatus === 'Decommissioned') {
                $message = '<div class="alert alert-danger border-0 shadow-sm animate-fade py-3" style="background-color: #fff5f5; color: #e53e3e; border-left: 4px solid #e53e3e;"><i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Operational Block:</strong> Selected vehicle is currently flagged as [ ' . htmlspecialchars($vehicleStatus) . ' ] and cannot be dispatched.</div>';
            } else {
                // Requirement A: Overlapping time verification using standard interval formula (:start_time < end_time AND :end_time > start_time)
                $driverCheck = $conn->prepare("SELECT * FROM schedules WHERE driver_id = :driver_id AND (:dep_time < end_time AND :arr_time > start_time)");
                $driverCheck->execute(['driver_id' => $driver_id, 'dep_time' => $departure_time, 'arr_time' => $arrival_time]);
                
                $vehicleCheck = $conn->prepare("SELECT * FROM schedules WHERE vehicle_id = :vehicle_id AND (:dep_time < end_time AND :arr_time > start_time)");
                $vehicleCheck->execute(['vehicle_id' => $vehicle_id, 'dep_time' => $departure_time, 'arr_time' => $arrival_time]);

                if ($driverCheck->rowCount() > 0) {
                    $message = '<div class="alert alert-danger border-0 shadow-sm animate-fade py-3" style="background-color: #fff5f5; color: #e53e3e; border-left: 4px solid #e53e3e;"><i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Driver Conflict Detected:</strong> Selected crew target is already piloting another transport run inside this timestamp block.</div>';
                } elseif ($vehicleCheck->rowCount() > 0) {
                    $message = '<div class="alert alert-danger border-0 shadow-sm animate-fade py-3" style="background-color: #fff5f5; color: #e53e3e; border-left: 4px solid #e53e3e;"><i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Vehicle Conflict Detected:</strong> Selected fleet vehicle framework cannot accept overlapping journey parameters.</div>';
                } else {
                    $stmt = $conn->prepare("INSERT INTO schedules (route_id, driver_id, vehicle_id, start_time, end_time) VALUES (:route_id, :driver_id, :vehicle_id, :departure_time, :arrival_time)");
                    $stmt->execute(['route_id' => $route_id, 'driver_id' => $driver_id, 'vehicle_id' => $vehicle_id, 'departure_time' => $departure_time, 'arrival_time' => $arrival_time]);
                    $message = '<div class="alert alert-success border-0 shadow-sm animate-fade py-3" style="background-color: #e6fffa; color: #319795; border-left: 4px solid #319795;"><i class="bi bi-check-circle-fill me-2"></i><strong>Engine Cleared!</strong> New transit sequence committed down database structure.</div>';
                }
            }
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger border-0 shadow-sm animate-fade py-3" style="background-color: #fff5f5; color: #e53e3e; border-left: 4px solid #e53e3e;">Internal Fault: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// 3. Business Logic: Update/Edit Schedule with Conflict Detection & Vehicle Maintenance Safeguard
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_schedule'])) {
    $id = $_POST['id'];
    $route_id = $_POST['route_id'];
    $driver_id = $_POST['driver_id'];
    $vehicle_id = $_POST['vehicle_id'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];

    if ($departure_time >= $arrival_time) {
        $message = '<div class="alert alert-danger border-0 shadow-sm animate-fade py-3" style="background-color: #fff5f5; color: #e53e3e; border-left: 4px solid #e53e3e;"><i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Validation Failure:</strong> Destination ETA marker time cannot precede initial departure timestamp.</div>';
    } else {
        try {
            // Requirement C: Vehicle Maintenance Status Health Verification on Update
            $vStmt = $conn->prepare("SELECT status FROM vehicles WHERE id = :vehicle_id");
            $vStmt->execute(['vehicle_id' => $vehicle_id]);
            $vehicleStatus = $vStmt->fetchColumn();

            if ($vehicleStatus === 'Under Maintenance' || $vehicleStatus === 'Decommissioned') {
                $message = '<div class="alert alert-danger border-0 shadow-sm animate-fade py-3" style="background-color: #fff5f5; color: #e53e3e; border-left: 4px solid #e53e3e;"><i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Operational Block:</strong> Target modification rejected. Chosen vehicle is currently flagged as [ ' . htmlspecialchars($vehicleStatus) . ' ].</div>';
            } else {
                // Requirement A: Overlapping time verification queries for Update (Ignoring current record ID)
                $driverCheck = $conn->prepare("SELECT * FROM schedules WHERE driver_id = :driver_id AND id != :id AND (:dep_time < end_time AND :arr_time > start_time)");
                $driverCheck->execute(['driver_id' => $driver_id, 'id' => $id, 'dep_time' => $departure_time, 'arr_time' => $arrival_time]);
                
                $vehicleCheck = $conn->prepare("SELECT * FROM schedules WHERE vehicle_id = :vehicle_id AND id != :id AND (:dep_time < end_time AND :arr_time > start_time)");
                $vehicleCheck->execute(['vehicle_id' => $vehicle_id, 'id' => $id, 'dep_time' => $departure_time, 'arr_time' => $arrival_time]);

                if ($driverCheck->rowCount() > 0) {
                    $message = '<div class="alert alert-danger border-0 shadow-sm animate-fade py-3" style="background-color: #fff5f5; color: #e53e3e; border-left: 4px solid #e53e3e;"><i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Driver Conflict Detected:</strong> Selected crew target is assigned to another active transport path inside this modified timeframe.</div>';
                } elseif ($vehicleCheck->rowCount() > 0) {
                    $message = '<div class="alert alert-danger border-0 shadow-sm animate-fade py-3" style="background-color: #fff5f5; color: #e53e3e; border-left: 4px solid #e53e3e;"><i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Vehicle Conflict Detected:</strong> Target vehicle timeline collision detected for the modified parameters.</div>';
                } else {
                    $stmt = $conn->prepare("UPDATE schedules SET route_id = :route_id, driver_id = :driver_id, vehicle_id = :vehicle_id, start_time = :departure_time, end_time = :arrival_time WHERE id = :id");
                    $stmt->execute(['route_id' => $route_id, 'driver_id' => $driver_id, 'vehicle_id' => $vehicle_id, 'departure_time' => $departure_time, 'arrival_time' => $arrival_time, 'id' => $id]);
                    $message = '<div class="alert alert-info border-0 shadow-sm animate-fade py-3" style="background-color: #ebf8ff; color: #2b6cb0; border-left: 4px solid #3182ce;"><i class="bi bi-info-circle-fill me-2"></i>Transit sequence parameters modified successfully.</div>';
                }
            }
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger border-0 shadow-sm animate-fade py-3" style="background-color: #fff5f5; color: #e53e3e; border-left: 4px solid #e53e3e;">Internal Fault: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Pre-fetching select drop-down datasets
$routesList = $conn->query("SELECT id, route_number, start_location, end_location FROM routes")->fetchAll(PDO::FETCH_ASSOC);
$driversList = $conn->query("SELECT id, first_name, last_name FROM drivers WHERE status = 'Available'")->fetchAll(PDO::FETCH_ASSOC);
$vehiclesList = $conn->query("SELECT id, plate_number, model, status FROM vehicles")->fetchAll(PDO::FETCH_ASSOC);

// Complete live master tracking loop query
$query = "SELECT s.id, s.route_id, s.driver_id, s.vehicle_id, r.route_number, d.first_name, d.last_name, v.plate_number, s.start_time AS departure_time, s.end_time AS arrival_time 
          FROM schedules s
          JOIN routes r ON s.route_id = r.id
          JOIN drivers d ON s.driver_id = d.id
          JOIN vehicles v ON s.vehicle_id = v.id
          ORDER BY s.start_time ASC";
$schedules = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
body {
    background-color: #f7fafc !important;
    color: #2d3748 !important;
}
.theme-card {
    background-color: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05) !important;
}
.theme-card-header {
    background-color: #ffffff !important;
    border-bottom: 1px solid #e2e8f0 !important;
    font-weight: 600;
    color: #2d3748;
}
.theme-input {
    background-color: #ffffff !important;
    border: 1px solid #cbd5e0 !important;
    color: #2d3748 !important;
    border-radius: 6px;
}
.theme-input:focus {
    border-color: #3182ce !important;
    box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.15) !important;
}
.table-header-row {
    background-color: #f7fafc !important;
    border-bottom: 2px solid #e2e8f0 !important;
}
.table-header-row th {
    color: #4a5568 !important;
    font-weight: 600;
}
.text-custom-dark {
    color: #2d3748 !important;
}
.animate-fade { animation: fadeIn 0.2s ease-out; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

<div class="row animate-fade mb-5">
    <div class="col-md-12 mb-3">
        <h2 class="fw-bold text-custom-dark mb-1"><i class="bi bi-calendar-range text-primary me-2"></i>Central Scheduling Matrix</h2>
        <p class="text-muted">Generate, validate, and track deployment timelines configurations mapping blocks seamlessly.</p>
    </div>
    
    <div class="col-md-12 mb-2"><?php echo $message; ?></div>

    <div class="col-md-4 mb-4">
        <div class="card theme-card">
            <div class="card-header theme-card-header py-3 text-primary">
                <i class="bi bi-sliders2 me-2"></i>Deploy New Shift Plan
            </div>
            <div class="card-body p-4">
                <form action="schedules.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Route Target Line</label>
                        <select name="route_id" class="form-select theme-input" required>
                            <option value="" disabled selected>Choose line...</option>
                            <?php foreach($routesList as $r): ?>
                                <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['route_number'] . " - " . $r['start_location'] . " to " . $r['end_location']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Pilot Crew Driver</label>
                        <select name="driver_id" class="form-select theme-input" required>
                            <option value="" disabled selected>Select operator...</option>
                            <?php foreach($driversList as $d): ?>
                                <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['first_name'] . " " . $d['last_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Active Fleet Coach Frame</label>
                        <select name="vehicle_id" class="form-select theme-input" required>
                            <option value="" disabled selected>Choose vehicle plate...</option>
                            <?php foreach($vehiclesList as $v): ?>
                                <option value="<?php echo $v['id']; ?>" <?php echo ($v['status'] === 'Under Maintenance' || $v['status'] === 'Decommissioned') ? 'style="color: #e53e3e; background-color: #fff5f5;"' : ''; ?>>
                                    <?php echo htmlspecialchars($v['plate_number'] . " [" . $v['model'] . "]"); ?>
                                    <?php echo ($v['status'] === 'Under Maintenance' || $v['status'] === 'Decommissioned') ? ' - (' . htmlspecialchars($v['status']) . ')' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Departure Clock</label>
                        <input type="datetime-local" name="departure_time" class="form-control theme-input" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Arrival Expected Target</label>
                        <input type="datetime-local" name="arrival_time" class="form-control theme-input" required>
                    </div>
                    <button type="submit" name="add_schedule" class="btn btn-primary w-100 fw-bold py-2 shadow-sm rounded-2 mt-2">
                        <i class="bi bi-save me-1"></i> Run Matrix Validations
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card theme-card">
            <div class="card-header theme-card-header py-3 text-custom-dark">
                <i class="bi bi-clock text-success me-2"></i>Live Fleet Journeys Tracking Blocks
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-custom-dark">
                        <thead class="table-header-row text-uppercase small">
                            <tr>
                                <th class="ps-4 py-3">Line Number</th>
                                <th>Crew Operator</th>
                                <th>Vehicle Key</th>
                                <th>Timeline Window</th>
                                <th class="text-center">Database Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($schedules)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-5">No shift matrices tracked down operational deployment paths.</td></tr>
                            <?php else: ?>
                                <?php foreach ($schedules as $s): ?>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td class="ps-4">
                                            <span class="badge border p-2 fw-bold" style="background-color: #ebf8ff; border-color: #bee3f8 !important; color: #2b6cb0;">
                                                <?php echo htmlspecialchars($s['route_number']); ?>
                                            </span>
                                        </td>
                                        <td class="fw-semibold text-custom-dark"><?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name']); ?></td>
                                        <td><span class="badge bg-light text-dark border px-2 py-2 font-monospace"><?php echo htmlspecialchars($s['plate_number']); ?></span></td>
                                        <td>
                                            <div class="small fw-bold text-dark mb-1"><i class="bi bi-box-arrow-up-right text-success me-1"></i> <?php echo date('M d, H:i', strtotime($s['departure_time'])); ?></div>
                                            <div class="small text-secondary"><i class="bi bi-box-arrow-in-down text-danger me-1"></i> <?php echo date('M d, H:i', strtotime($s['arrival_time'])); ?></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-2 px-2 py-1" data-bs-toggle="modal" data-bs-target="#editScheduleModal<?php echo $s['id']; ?>">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </button>
                                                <a href="schedules.php?cancel_id=<?php echo $s['id']; ?>" onclick="return confirm('Drop and cancel this active route deployment plan?');" class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1">
                                                    <i class="bi bi-trash"></i> Drop
                                                </a>
                                            </div>
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

<?php if (!empty($schedules)): ?>
    <?php foreach ($schedules as $s): ?>
        <div class="modal fade" id="editScheduleModal<?php echo $s['id']; ?>" tabindex="-1" aria-labelledby="editScheduleLabel<?php echo $s['id']; ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="modal-header" style="border-bottom: 1px solid #e2e8f0;">
                        <h5 class="modal-title fw-bold text-custom-dark" id="editScheduleLabel<?php echo $s['id']; ?>"><i class="bi bi-pencil-square text-primary me-2"></i>Update Schedule Parameters</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="schedules.php" method="POST">
                        <div class="modal-body p-4">
                            <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Route Line</label>
                                <select name="route_id" class="form-select theme-input" required>
                                    <?php foreach($routesList as $r): ?>
                                        <option value="<?php echo $r['id']; ?>" <?php echo ($r['id'] == $s['route_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($r['route_number'] . " - " . $r['start_location'] . " to " . $r['end_location']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Pilot Crew Driver</label>
                                <select name="driver_id" class="form-select theme-input" required>
                                    <option value="<?php echo $s['driver_id']; ?>" selected><?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name'] . ' (Current)'); ?></option>
                                    <?php foreach($driversList as $d): ?>
                                        <?php if($d['id'] != $s['driver_id']): ?>
                                            <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['first_name'] . " " . $d['last_name']); ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Active Fleet Vehicle</label>
                                <select name="vehicle_id" class="form-select theme-input" required>
                                    <?php foreach($vehiclesList as $v): ?>
                                        <option value="<?php echo $v['id']; ?>" <?php echo ($v['id'] == $s['vehicle_id']) ? 'selected' : ''; ?> <?php echo ($v['status'] === 'Under Maintenance' || $v['status'] === 'Decommissioned') ? 'style="color: #e53e3e; background-color: #fff5f5;"' : ''; ?>>
                                            <?php echo htmlspecialchars($v['plate_number'] . " [" . $v['model'] . "]"); ?>
                                            <?php echo ($v['status'] === 'Under Maintenance' || $v['status'] === 'Decommissioned') ? ' - (' . htmlspecialchars($v['status']) . ')' : ''; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Departure Clock</label>
                                <input type="datetime-local" name="departure_time" class="form-control theme-input" value="<?php echo date('Y-m-d\TH:i', strtotime($s['departure_time'])); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Arrival Expected Target</label>
                                <input type="datetime-local" name="arrival_time" class="form-control theme-input" value="<?php echo date('Y-m-d\TH:i', strtotime($s['arrival_time'])); ?>" required>
                            </div>
                        </div>
                        <div class="modal-footer" style="border-top: 1px solid #e2e8f0;">
                            <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="edit_schedule" class="btn btn-primary fw-semibold px-4">Update Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>