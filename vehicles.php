<?php 
require_once 'config/db.php';
include_once 'includes/header.php'; 

$message = '';

// Business Logic: Handle Status Switch Toggles via POST/AJAX or direct form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_status'])) {
    $id = intval($_POST['vehicle_id']);
    $new_status = $_POST['status'] === 'Operational' ? 'Operational' : ($_POST['status'] === 'Maintenance' ? 'Maintenance' : 'Out of Service');
    
    if (!empty($id)) {
        try {
            $stmt = $conn->prepare("UPDATE vehicles SET status = :status WHERE id = :id");
            $stmt->execute(['status' => $new_status, 'id' => $id]);
            $message = '<div class="alert alert-success alert-dismissible fade show animate-fade" role="alert">
                            <strong><i class="bi bi-toggle-on"></i> Status Updated!</strong> Vehicle status adjusted instantly via matrix control.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger animate-fade"><strong><i class="bi bi-exclamation-triangle-fill"></i> Toggle Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Business Logic: Handle Update Vehicle Details & Maintenance Data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_vehicle'])) {
    $id = intval($_POST['vehicle_id']);
    $plate_number = trim($_POST['plate_number']);
    $model = trim($_POST['model']);
    $capacity = intval($_POST['capacity']);
    $status = $_POST['status'];
    $maintenance_notes = trim($_POST['maintenance_notes']);

    if (!empty($id) && !empty($plate_number) && !empty($model)) {
        try {
            $stmt = $conn->prepare("UPDATE vehicles SET plate_number = :plate_number, model = :model, capacity = :capacity, status = :status, maintenance_notes = :notes WHERE id = :id");
            $stmt->execute([
                'plate_number' => $plate_number,
                'model' => $model,
                'capacity' => $capacity,
                'status' => $status,
                'notes' => $maintenance_notes,
                'id' => $id
            ]);
            $message = '<div class="alert alert-success alert-dismissible fade show animate-fade" role="alert">
                            <strong><i class="bi bi-check-circle-fill"></i> Success!</strong> Fleet asset profile and maintenance records modified.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger animate-fade"><strong><i class="bi bi-exclamation-triangle-fill"></i> Profile Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Business Logic: Handle New Fleet Asset Registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_vehicle'])) {
    $plate_number = trim($_POST['plate_number']);
    $model = trim($_POST['model']);
    $capacity = intval($_POST['capacity']);
    $status = isset($_POST['status']) ? $_POST['status'] : 'Operational';
    $maintenance_notes = trim($_POST['maintenance_notes']);

    if (!empty($plate_number) && !empty($model) && !empty($capacity)) {
        try {
            $stmt = $conn->prepare("INSERT INTO vehicles (plate_number, model, capacity, status, maintenance_notes) VALUES (:plate_number, :model, :capacity, :status, :notes)");
            $stmt->execute([
                'plate_number' => $plate_number,
                'model' => $model,
                'capacity' => $capacity,
                'status' => $status,
                'notes' => $maintenance_notes
            ]);
            $message = '<div class="alert alert-success alert-dismissible fade show animate-fade" role="alert">
                            <strong><i class="bi bi-check-circle-fill"></i> Success!</strong> New asset logged into system inventory successfully.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger animate-fade"><strong><i class="bi bi-exclamation-triangle-fill"></i> Collision Error:</strong> Registration Plate number already unique to another asset.</div>';
        }
    }
}

// Search and Multi-Filtering Business Logic
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status_filter']) ? trim($_GET['status_filter']) : '';
$capacity_filter = isset($_GET['capacity_filter']) ? trim($_GET['capacity_filter']) : '';

$where_clauses = [];
$params = [];

if (!empty($search)) {
    $where_clauses[] = "(plate_number LIKE :search OR model LIKE :search)";
    $params['search'] = '%' . $search . '%';
}

if (!empty($status_filter)) {
    $where_clauses[] = "status = :status_filter";
    $params['status_filter'] = $status_filter;
}

if (!empty($capacity_filter)) {
    if ($capacity_filter === '1-20') {
        $where_clauses[] = "capacity BETWEEN 1 AND 20";
    } elseif ($capacity_filter === '21-40') {
        $where_clauses[] = "capacity BETWEEN 21 AND 40";
    } elseif ($capacity_filter === '41+') {
        $where_clauses[] = "capacity >= 41";
    }
}

$query = "SELECT * FROM vehicles";
if (count($where_clauses) > 0) {
    $query .= " WHERE " . implode(' AND ', $where_clauses);
}
$query .= " ORDER BY id DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

$is_filtered = !empty($search) || !empty($status_filter) || !empty($capacity_filter);
?>

<style>
/* Corporate Light Mode Theme Parameters Matching System */
.animate-fade { animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
.hover-card { transition: transform 0.3s ease, box-shadow 0.3s ease; border-radius: 12px !important; }
.hover-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.06) !important; }
.form-switch .form-check-input { cursor: pointer; width: 2.5em; height: 1.25em; }
/* Added soft hover transition effect for link badges */
.plate-link { transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out; display: inline-block; text-decoration: none; }
.plate-link:hover { opacity: 0.85; transform: scale(1.02); }

</style>

<div class="container-fluid py-4 animate-fade" style="background-color: #f7fafc; min-height: 85vh;">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold" style="color: #2d3748;"><i class="bi bi-bus-front text-warning me-2"></i>Vehicle & Maintenance Management</h2>
            <p class="text-muted">Register system transit entities, run maintenance update validations, and monitor operational lifecycle logs.</p>
            <div class="mt-2"><?php echo $message; ?></div>
            <hr style="border-color: #e2e8f0;">
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px;">
                <form method="GET" action="vehicles.php" class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted" style="border-color: #e2e8f0;">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 py-2" style="border-color: #e2e8f0;" 
                                   placeholder="Search plate number or model..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="status_filter" class="form-select py-2" style="border-color: #e2e8f0;">
                            <option value="">-- All Operational Statuses --</option>
                            <option value="Operational" <?php echo ($status_filter === 'Operational') ? 'selected' : ''; ?>>Operational</option>
                            <option value="Maintenance" <?php echo ($status_filter === 'Maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                            <option value="Out of Service" <?php echo ($status_filter === 'Out of Service') ? 'selected' : ''; ?>>Out of Service</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="capacity_filter" class="form-select py-2" style="border-color: #e2e8f0;">
                            <option value="">-- All Seating Capacities --</option>
                            <option value="1-20" <?php echo ($capacity_filter === '1-20') ? 'selected' : ''; ?>>1 - 20 Seats</option>
                            <option value="21-40" <?php echo ($capacity_filter === '21-40') ? 'selected' : ''; ?>>21 - 40 Seats</option>
                            <option value="41+" <?php echo ($capacity_filter === '41+') ? 'selected' : ''; ?>>41+ Seats</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn text-white w-100 fw-bold py-2" style="background-color: #3182ce;">
                            <i class="bi bi-filter me-1"></i> Filter
                        </button>
                        <?php if ($is_filtered): ?>
                            <a href="vehicles.php" class="btn btn-outline-danger py-2 px-3 fw-bold text-nowrap" title="Clear Search & Filters">
                                <i class="bi bi-x-lg"></i> Clear
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 bg-white hover-card">
                <div class="card-header fw-bold text-white py-3" style="background-color: #3182ce; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <i class="bi bi-plus-circle me-2"></i>Register New Vehicle Asset
                </div>
                <div class="card-body p-4" style="color: #2d3748;">
                    <form action="vehicles.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">License Plate String</label>
                            <input type="text" name="plate_number" class="form-control py-2" style="border-color: #e2e8f0;" placeholder="e.g. WP-7729" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Vehicle Model / Specification</label>
                            <input type="text" name="model" class="form-control py-2" style="border-color: #e2e8f0;" placeholder="e.g. Mercedes Benz Sprinter" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Total Cabin Seating Capacity</label>
                            <input type="number" name="capacity" class="form-control py-2" style="border-color: #e2e8f0;" placeholder="Total seat metrics count" required min="1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Initial Operation Status</label>
                            <select name="status" class="form-select py-2" style="border-color: #e2e8f0;">
                                <option value="Operational">Operational</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Out of Service">Out of Service</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Initial Diagnostic / Maintenance Notes</label>
                            <textarea name="maintenance_notes" class="form-control" style="border-color: #e2e8f0;" rows="2" placeholder="Optional diagnostics remarks..."></textarea>
                        </div>
                        <button type="submit" name="add_vehicle" class="btn text-white w-100 fw-bold py-2 mt-2 shadow-sm rounded-3" style="background-color: #3182ce;">
                            <i class="bi bi-save me-1"></i> Commit Asset to Inventory
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 bg-white hover-card">
                <div class="card-header bg-dark text-white fw-bold py-3 d-flex justify-content-between align-items-center" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <span><i class="bi bi-cpu me-2"></i>System Fleet Registry Matrix</span>
                    <span class="badge bg-secondary"><?php echo count($vehicles); ?> Units Found</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="color: #2d3748;">
                            <thead class="table-light text-secondary small text-uppercase fw-semibold" style="border-bottom: 2px solid #e2e8f0;">
                                <tr>
                                    <th class="ps-4 py-3">License Plate</th>
                                    <th>Asset Model</th>
                                    <th>Capacity Details</th>
                                    <th>Status Marker Switch</th>
                                    <th>Maintenance Records Excerpt</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($vehicles)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                            No fleet transport units match your criteria or are currently saved inside dataset systems.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($vehicles as $v): ?>
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <td class="ps-4">
                                                <a href="vehicle_details.php?id=<?php echo urlencode($v['id']); ?>" class="plate-link" title="View Full Vehicle Profile & History">
                                                    <span class="badge bg-dark text-white px-3 py-2 fw-mono font-monospace shadow-sm" style="letter-spacing: 0.5px;">
                                                        <?php echo htmlspecialchars($v['plate_number']); ?>
                                                    </span>
                                                </a>
                                            </td>
                                            <td class="fw-semibold text-dark"><?php echo htmlspecialchars($v['model']); ?></td>
                                            <td><span class="text-secondary fw-bold"><?php echo htmlspecialchars($v['capacity']); ?></span> <small class="text-muted">Seats</small></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <form action="vehicles.php" method="POST" class="status-toggle-form m-0">
                                                        <input type="hidden" name="vehicle_id" value="<?php echo $v['id']; ?>">
                                                        <input type="hidden" name="toggle_status" value="1">
                                                        <div class="form-check form-switch p-0 m-0 d-inline-block">
                                                            <input class="form-check-input ms-0 status-switch-input" type="checkbox" role="switch" 
                                                                   name="status" value="<?php echo $v['status'] == 'Operational' ? 'Maintenance' : 'Operational'; ?>"
                                                                   <?php echo ($v['status'] == 'Operational') ? 'checked' : ''; ?>>
                                                        </div>
                                                    </form>
                                                    <?php if($v['status'] == 'Operational'): ?>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small"><i class="bi bi-check-circle-fill"></i> Active</span>
                                                    <?php elseif($v['status'] == 'Maintenance'): ?>
                                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded-pill small"><i class="bi bi-tools"></i> Shop</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill small"><i class="bi bi-exclamation-octagon-fill"></i> Down</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate small text-muted" style="max-width: 160px;" title="<?php echo htmlspecialchars($v['maintenance_notes'] ?? ''); ?>">
                                                    <?php echo !empty($v['maintenance_notes']) ? htmlspecialchars($v['maintenance_notes']) : '<em>No diagnostic history.</em>'; ?>
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-secondary px-3 py-1.5 rounded-pill manage-vehicle-btn" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editVehicleModal"
                                                        data-id="<?php echo $v['id']; ?>"
                                                        data-plate="<?php echo htmlspecialchars($v['plate_number']); ?>"
                                                        data-model="<?php echo htmlspecialchars($v['model']); ?>"
                                                        data-capacity="<?php echo htmlspecialchars($v['capacity']); ?>"
                                                        data-status="<?php echo htmlspecialchars($v['status']); ?>"
                                                        data-notes="<?php echo htmlspecialchars($v['maintenance_notes'] ?? ''); ?>">
                                                    <i class="bi bi-gear-fill me-1"></i> Manage Desk
                                                </button>
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

<div class="modal fade" id="editVehicleModal" tabindex="-1" aria-labelledby="editVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header text-white py-3" style="background-color: #2d3748; border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold" id="editVehicleModalLabel"><i class="bi bi-sliders me-2 text-warning"></i>Manage Fleet Asset Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="vehicles.php" method="POST">
                <div class="modal-body p-4" style="color: #2d3748;">
                    <input type="hidden" name="vehicle_id" id="modal_vehicle_id">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label small fw-bold text-secondary">License Plate</label>
                                <input type="text" name="plate_number" id="modal_plate_number" class="form-control" style="border-color: #e2e8f0;" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label small fw-bold text-secondary">Cabin Passenger Capacity</label>
                                <input type="number" name="capacity" id="modal_capacity" class="form-control" style="border-color: #e2e8f0;" required min="1">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 mt-2">
                        <label class="form-label small fw-bold text-secondary">Vehicle Model Specification</label>
                        <input type="text" name="model" id="modal_model" class="form-control" style="border-color: #e2e8f0;" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Update Operational Status</label>
                        <select name="status" id="modal_status" class="form-select" style="border-color: #e2e8f0;">
                            <option value="Operational">Operational</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Out of Service">Out of Service</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-secondary"><i class="bi bi-journal-text me-1"></i> Depot Maintenance Activity & Diagnostic Service Logs</label>
                        <textarea name="maintenance_notes" id="modal_maintenance_notes" class="form-control" style="border-color: #e2e8f0;" rows="4" placeholder="Input structural diagnostic history notes here..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 py-3" style="background-color: #f7fafc; border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="btn btn-secondary px-4 py-2 small fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_vehicle" class="btn text-white px-4 py-2 fw-bold shadow-sm" style="background-color: #3182ce;">Commit Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Existing Manage Desk Telemetry Handler Deck
    const manageButtons = document.querySelectorAll('.manage-vehicle-btn');
    manageButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const plate = this.getAttribute('data-plate');
            const model = this.getAttribute('data-model');
            const capacity = this.getAttribute('data-capacity');
            const status = this.getAttribute('data-status');
            const notes = this.getAttribute('data-notes');

            document.getElementById('modal_vehicle_id').value = id;
            document.getElementById('modal_plate_number').value = plate;
            document.getElementById('modal_model').value = model;
            document.getElementById('modal_capacity').value = capacity;
            document.getElementById('modal_status').value = status;
            document.getElementById('modal_maintenance_notes').value = notes;
        });
    });

    // 2. New Asynchronous Instant Form-Switch Toggle Handler Deck
    const statusSwitches = document.querySelectorAll('.status-switch-input');
    statusSwitches.forEach(sw => {
        sw.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>
</body>
</html>