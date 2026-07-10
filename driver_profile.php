<?php
require_once 'config/db.php';
include_once 'includes/header.php';

$message = '';
$driver_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// --- Handle Inline Profile Update Form Submission ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $license_number = trim($_POST['license_number']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $status = $_POST['status'];

    if (!empty($driver_id) && !empty($license_number) && !empty($first_name) && !empty($last_name)) {
        try {
            $stmt = $conn->prepare("UPDATE drivers SET license_number = :license_number, first_name = :first_name, last_name = :last_name, phone = :phone, status = :status WHERE id = :id");
            $stmt->execute([
                'license_number' => $license_number,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'phone' => $phone,
                'status' => $status,
                'id' => $driver_id
            ]);
            $message = '<div class="alert alert-success alert-dismissible fade show" role="alert"><i class="bi bi-check-circle-fill"></i> Profile metrics updated successfully!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> Error updating record: ' . htmlspecialchars($e->getMessage()) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }
    }
}

// --- Fetch Master Driver Record ---
$stmt = $conn->prepare("SELECT * FROM drivers WHERE id = :id");
$stmt->execute(['id' => $driver_id]);
$driver = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$driver) {
    echo '<div class="container mt-5"><div class="alert alert-danger fw-bold"><i class="bi bi-exclamation-octagon-fill me-2"></i> Driver profile registry match not found. <a href="drivers.php" class="alert-link">Return to Management Terminal</a></div></div>';
    exit;
}

// --- Fetch Driver Schedule Engine Logs & Route Histories ---
// Assumes table structural naming conventions: schedules (id, route_id, vehicle_id, driver_id, departure_time, status)
// Linked tables: routes (route_number, start_location, end_location), vehicles (plate_number, model)
try {
    $sched_stmt = $conn->prepare("
        SELECT s.*, r.route_number, r.start_location, r.end_location, v.plate_number, v.model 
        FROM schedules s
        JOIN routes r ON s.route_id = r.id
        JOIN vehicles v ON s.vehicle_id = v.id
        WHERE s.driver_id = :driver_id
        ORDER BY s.departure_time DESC
    ");
    $sched_stmt->execute(['driver_id' => $driver_id]);
    $assignments = $sched_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Graceful fallback to avoid app crashes if your structural schema changes names slightly
    $assignments = [];
}
?>

<div class="container-fluid" style="background-color: #f7fafc; min-height: 90vh; padding: 25px;">
    
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="drivers.php" class="text-decoration-none text-primary"><i class="bi bi-arrow-left-circle me-1"></i> Drivers Directory</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profile Terminal</li>
                </ol>
            </nav>
            <?php echo $message; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow-sm border-0 mb-4" style="background-color: #ffffff;">
                <div class="card-body text-center pt-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: #2d3748;"><?php echo htmlspecialchars($driver['first_name'] . ' ' . $driver['last_name']); ?></h4>
                    <p class="text-muted small mb-3">System Identity Token: #<?php echo $driver['id']; ?></p>
                    
                    <div class="mb-3">
                        <?php if ($driver['status'] == 'Available'): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6"><i class="bi bi-check-circle-fill me-1"></i> Available</span>
                        <?php elseif ($driver['status'] == 'On Duty'): ?>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-6"><i class="bi bi-cone-striped me-1"></i> On Duty</span>
                        <?php else: ?>
                            <span class="badge bg-warning-subtle text-warning dark border border-warning-subtle px-3 py-2 fs-6"><i class="bi bi-slash-circle me-1"></i> On Leave</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0" style="background-color: #ffffff;">
                <div class="card-header fw-bold text-white bg-dark">
                    <i class="bi bi-sliders me-1"></i> Administrative Registry Update
                </div>
                <div class="card-body">
                    <form action="driver_profile.php?id=<?php echo $driver['id']; ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">License Number ID</label>
                            <input type="text" name="license_number" class="form-control fw-bold" value="<?php echo htmlspecialchars($driver['license_number']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($driver['first_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($driver['last_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Phone Contact Channel</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($driver['phone']); ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted small">System Fleet Status Allocation</label>
                            <select name="status" class="form-select">
                                <option value="Available" <?php echo $driver['status'] === 'Available' ? 'selected' : ''; ?>>Available</option>
                                <option value="On Duty" <?php echo $driver['status'] === 'On Duty' ? 'selected' : ''; ?>>On Duty</option>
                                <option value="On Leave" <?php echo $driver['status'] === 'On Leave' ? 'selected' : ''; ?>>On Leave</option>
                            </select>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary w-100 fw-bold" style="background-color: #3182ce; border: none;">
                            <i class="bi bi-save2-fill me-1"></i> Update Profile Metadata
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card shadow-sm border-0 mb-4" style="background-color: #ffffff;">
                <div class="card-header bg-secondary text-white fw-bold">
                    <i class="bi bi-truck me-1"></i> Currently Associated Fleet Asset
                </div>
                <div class="card-body bg-light-subtle">
                    <?php 
                    // Pinpoint current active schedule line assignment to map vehicle parameters
                    $active_vehicle = null;
                    foreach ($assignments as $asg) {
                        if ($driver['status'] === 'On Duty') {
                            $active_vehicle = $asg;
                            break;
                        }
                    }
                    if ($active_vehicle): 
                    ?>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="bg-primary-subtle text-primary p-3 rounded">
                                    <i class="bi bi-bus-front-fill fs-2"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h5 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($active_vehicle['model']); ?></h5>
                                <p class="mb-0 text-muted">License Registration Tag: <code class="bg-dark text-white px-2 py-0.5 rounded fw-bold"><?php echo htmlspecialchars($active_vehicle['plate_number']); ?></code></p>
                            </div>
                            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                <span class="badge bg-danger animate-pulse px-3 py-2"><i class="bi bi-geo-alt-fill me-1"></i> Currently En Route</span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-muted py-2">
                            <i class="bi bi-info-circle-fill text-secondary me-1"></i> No active fleet vehicle or running shift link registered for this asset profile block.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow-sm border-0" style="background-color: #ffffff;">
                <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-calendar-week me-1"></i> Dispatch Schedule History & Route Matrices</span>
                    <span class="badge bg-light text-dark fw-bold"><?php echo count($assignments); ?> Runs Found</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small fw-bold">
                                <tr>
                                    <th class="ps-3">Shift ID</th>
                                    <th>Route Code</th>
                                    <th>Service Terminal Corridor</th>
                                    <th>Assigned Bus Plate</th>
                                    <th>Departure Stamp</th>
                                    <th class="text-end pe-3">Run Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($assignments)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="bi bi-folder-x fs-1 d-block mb-2 text-black-50"></i>
                                            No system logs or route allocations found for this specific driver.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($assignments as $row): ?>
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <td class="ps-3 text-muted">#<?php echo $row['id']; ?></td>
                                            <td><span class="badge bg-dark fw-bold"><?php echo htmlspecialchars($row['route_number']); ?></span></td>
                                            <td class="fw-semibold text-truncate" style="max-width: 240px;">
                                                <?php echo htmlspecialchars($row['start_location']); ?> 
                                                <i class="bi bi-arrow-right text-primary mx-1 small"></i> 
                                                <?php echo htmlspecialchars($row['end_location']); ?>
                                            </td>
                                            <td><code class="text-secondary fw-bold"><?php echo htmlspecialchars($row['plate_number']); ?></code></td>
                                            <td class="small text-muted"><?php echo date('Y-m-d H:i', strtotime($row['departure_time'])); ?></td>
                                            <td class="text-end pe-3">
                                                <?php if (isset($row['status']) && $row['status'] == 'Completed'): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Completed</span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Active Run</span>
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