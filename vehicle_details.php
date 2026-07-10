<?php
require_once 'config/db.php';
include_once 'includes/header.php';

$message = '';
$vehicle_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Redirect to primary menu if ID parameter is invalid or empty
if ($vehicle_id <= 0) {
    header("Location: vehicles.php");
    exit;
}

// Separate Controller Logic: Form-to-Controller Asset Update Process Handler
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_vehicle_profile'])) {
    $plate_number = trim($_POST['plate_number']);
    $model = trim($_POST['model']);
    $capacity = intval($_POST['capacity']);
    $status = $_POST['status'];
    $maintenance_notes = trim($_POST['maintenance_notes']);

    if (!empty($plate_number) && !empty($model) && $capacity > 0) {
        try {
            $stmt = $conn->prepare("UPDATE vehicles SET plate_number = :plate, model = :model, capacity = :cap, status = :status, maintenance_notes = :notes WHERE id = :id");
            $stmt->execute([
                'plate' => $plate_number,
                'model' => $model,
                'cap' => $capacity,
                'status' => $status,
                'notes' => $maintenance_notes,
                'id' => $vehicle_id
            ]);
            $message = '<div class="alert alert-success shadow-sm animate-fade"><i class="bi bi-check-circle-fill"></i> Asset configuration logs rewritten and locked inside production.</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger shadow-sm animate-fade"><i class="bi bi-exclamation-triangle-fill"></i> Execution Aborted: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Query Execution: Fetch primary single entity data structure
$stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = :id");
$stmt->execute(['id' => $vehicle_id]);
$vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vehicle) {
    echo '<div class="container py-5"><div class="alert alert-danger"><strong>Critical Fault:</strong> Fleet asset structural descriptor not recognized inside database data sheets. <a href="vehicles.php">Return to main index</a></div></div>';
    include_once 'includes/footer.php'; // ensure structured footer closes safely if present
    exit;
}

// Subordinate Query Stack: Fetch contextual historical records linked to this vehicle's ID
try {
    // Dynamic Query 1: Historical Schedules assigned to this specific fleet vehicle
    $schedStmt = $conn->prepare("SELECT s.*, r.route_number, r.start_location, r.end_location 
                                 FROM schedules s 
                                 JOIN routes r ON s.route_id = r.id 
                                 WHERE s.vehicle_id = :v_id 
                                 ORDER BY s.departure_time DESC");
    $schedStmt->execute(['v_id' => $vehicle_id]);
    $history_logs = $schedStmt->fetchAll(PDO::FETCH_ASSOC);

    // Dynamic Query 2: Current Active Assigned Driver (Assuming drivers/schedules bridge metrics)
    // Adjust this query structural block according to your database schema names if needed!
    $driverStmt = $conn->prepare("SELECT d.* FROM drivers d 
                                  JOIN schedules s ON s.driver_id = d.id 
                                  WHERE s.vehicle_id = :v_id AND s.departure_time >= NOW() 
                                  LIMIT 1");
    $driverStmt->execute(['v_id' => $vehicle_id]);
    $active_driver = $driverStmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Graceful fallback arrays to prevent screen crashes if schemas are not fully created yet
    $history_logs = [];
    $active_driver = null;
}
?>

<style>
.animate-fade { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
.meta-box { border-left: 4px solid #3182ce; background-color: #f7fafc; border-radius: 0 8px 8px 0; }
</style>

<div class="container-fluid py-4 animate-fade" style="background-color: #f7fafc; min-height: 85vh;">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <a href="vehicles.php" class="btn btn-sm btn-outline-secondary px-3 rounded-pill mb-2">
                    <i class="bi bi-arrow-left"></i> Back to Fleet Index
                </a>
                <h2 class="fw-bold" style="color: #2d3748;">
                    <i class="bi bi-bus-front text-primary me-2"></i>Asset File: <span class="text-monospace font-monospace text-uppercase bg-dark text-white px-2 py-0.5 rounded small"><?php echo htmlspecialchars($vehicle['plate_number']); ?></span>
                </h2>
                <p class="text-muted mb-0">Granular tracking node dashboard, maintenance scheduling, and historical system routes checklist execution telemetry.</p>
            </div>
            <span class="badge px-4 py-2 fs-6 rounded-pill shadow-sm <?php 
                echo ($vehicle['status'] == 'Operational') ? 'bg-success text-white' : (($vehicle['status'] == 'Maintenance') ? 'bg-warning text-dark' : 'bg-danger text-white'); 
            ?>">
                <i class="bi bi-activity me-1"></i> <?php echo htmlspecialchars($vehicle['status']); ?>
            </span>
        </div>
    </div>

    <?php if (!empty($message)) echo $message; ?>

    <div class="row">
        <div class="col-xl-5 col-lg-6 mb-4">
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 14px;">
                <div class="card-header bg-primary text-white fw-bold py-3" style="border-radius: 14px 14px 0 0;">
                    <i class="bi bi-pencil-square me-2"></i>Edit Dynamic Configuration Parameters
                </div>
                <div class="card-body p-4">
                    <form action="vehicle_details.php?id=<?php echo $vehicle_id; ?>" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary text-uppercase">Registration String</label>
                                    <input type="text" name="plate_number" class="form-control font-monospace text-uppercase fw-bold py-2" 
                                           value="<?php echo htmlspecialchars($vehicle['plate_number']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary text-uppercase">Max Seating Capacity</label>
                                    <input type="number" name="capacity" class="form-control py-2" 
                                           value="<?php echo htmlspecialchars($vehicle['capacity']); ?>" required min="1">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Asset Blueprint Model Specification</label>
                            <input type="text" name="model" class="form-control py-2" 
                                   value="<?php echo htmlspecialchars($vehicle['model']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Deployment Lifecycle Flag</label>
                            <select name="status" class="form-select py-2">
                                <option value="Operational" <?php echo $vehicle['status'] === 'Operational' ? 'selected' : ''; ?>>Operational / Active Transit</option>
                                <option value="Maintenance" <?php echo $vehicle['status'] === 'Maintenance' ? 'selected' : ''; ?>>Maintenance Workshop</option>
                                <option value="Out of Service" <?php echo $vehicle['status'] === 'Out of Service' ? 'selected' : ''; ?>>Out of Service / Offline</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary text-uppercase"><i class="bi bi-journal-medical me-1 text-danger"></i> Depot Operational Logs & Diagnostics</label>
                            <textarea name="maintenance_notes" class="form-control" rows="5" placeholder="Append active diagnostic errors, service due dates, or technician comments..."><?php echo htmlspecialchars($vehicle['maintenance_notes'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" name="update_vehicle_profile" class="btn text-white w-100 fw-bold py-2.5 rounded-3 shadow" style="background-color: #2b6cb0;">
                            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Update Profile Details
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm bg-white mt-4 p-4" style="border-radius: 14px;">
                <h5 class="fw-bold mb-3" style="color: #4a5568;"><i class="bi bi-person-badge text-muted me-2"></i>Active Driver Assignment</h5>
                <?php if ($active_driver): ?>
                    <div class="p-3 bg-light rounded border border-light animate-fade">
                        <div class="fw-bold text-dark fs-5"><i class="bi bi-person-circle text-primary me-2"></i><?php echo htmlspecialchars($active_driver['name']); ?></div>
                        <div class="text-secondary small mt-1">License: <strong><?php echo htmlspecialchars($active_driver['license_number']); ?></strong></div>
                        <div class="text-secondary small">Contact: <?php echo htmlspecialchars($active_driver['phone'] ?? 'N/A'); ?></div>
                    </div>
                <?php else: ?>
                    <div class="text-muted small border p-3 rounded" style="border-style: dashed !important;">
                        <i class="bi bi-info-circle-fill text-warning me-1"></i> No deployment crew shifts bound to this hardware entity on future rosters.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-xl-7 col-lg-6 mb-4">
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 14px;">
                <div class="card-header bg-dark text-white fw-bold py-3" style="border-radius: 14px 14px 0 0;">
                    <i class="bi bi-clock-history me-2"></i>Complete Deployment Schedule Registry Records
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary small text-uppercase fw-semibold" style="border-bottom: 2px solid #e2e8f0;">
                                <tr>
                                    <th class="ps-4 py-3">Route Reference</th>
                                    <th>Station Vector Path</th>
                                    <th>Departure Date/Time</th>
                                    <th class="text-end pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($history_logs)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <i class="bi bi-calendar2-x fs-3 d-block mb-2 text-secondary"></i>
                                            No tracking dispatch sheets or journey records generated inside the core database matching this unit.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($history_logs as $log): ?>
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <td class="ps-4 fw-bold text-primary">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 rounded">
                                                    <i class="bi bi-signpost-split-fill me-1"></i><?php echo htmlspecialchars($log['route_number']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="small fw-semibold text-dark"><?php echo htmlspecialchars($log['start_location']); ?></div>
                                                <div class="small text-muted"><i class="bi bi-arrow-down small"></i> <?php echo htmlspecialchars($log['end_location']); ?></div>
                                            </td>
                                            <td class="text-monospace font-monospace text-secondary small">
                                                <?php echo date('Y-m-d H:i', strtotime($log['departure_time'])); ?>
                                            </td>
                                            <td class="text-end pe-4">
                                                <?php if (strtotime($log['departure_time']) < time()): ?>
                                                    <span class="badge bg-light text-secondary border rounded-pill">Completed</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill animate-pulse">Scheduled</span>
                                                <?php endif; ?>
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
</body>
</html>