<?php 
require_once 'config/db.php';
include_once 'includes/header.php'; 

$message = '';

// --- Handle Add Driver Action ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_driver'])) {
    $license_number = trim($_POST['license_number']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $status = isset($_POST['status']) ? $_POST['status'] : 'Available';

    if (!empty($license_number) && !empty($first_name) && !empty($last_name)) {
        try {
            $stmt = $conn->prepare("INSERT INTO drivers (license_number, first_name, last_name, phone, status) VALUES (:license_number, :first_name, :last_name, :phone, :status)");
            $stmt->execute([
                'license_number' => $license_number,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'phone' => $phone,
                'status' => $status
            ]);
            $message = '<div class="alert alert-success alert-dismissible fade show" role="alert"><i class="bi bi-check-circle-fill"></i> Driver registered successfully!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> Error: ' . htmlspecialchars($e->getMessage()) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }
    }
}

// --- Handle Update Driver Action ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_driver'])) {
    $id = intval($_POST['driver_id']);
    $license_number = trim($_POST['license_number']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $status = $_POST['status'];

    if (!empty($id) && !empty($license_number) && !empty($first_name) && !empty($last_name)) {
        try {
            $stmt = $conn->prepare("UPDATE drivers SET license_number = :license_number, first_name = :first_name, last_name = :last_name, phone = :phone, status = :status WHERE id = :id");
            $stmt->execute([
                'license_number' => $license_number,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'phone' => $phone,
                'status' => $status,
                'id' => $id
            ]);
            $message = '<div class="alert alert-success alert-dismissible fade show" role="alert"><i class="bi bi-check-circle-fill"></i> Driver information updated successfully!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> Error: ' . htmlspecialchars($e->getMessage()) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }
    }
}

// --- Handle Search & Filter Parameters ---
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status_filter']) ? trim($_GET['status_filter']) : '';

// Build dynamic database query
$sql = "SELECT * FROM drivers WHERE 1=1";
$params = [];

if ($search_query !== '') {
    $sql .= " AND (license_number LIKE :search OR first_name LIKE :search OR last_name LIKE :search)";
    $params['search'] = '%' . $search_query . '%';
}

if ($status_filter !== '') {
    $sql .= " AND status = :status";
    $params['status'] = $status_filter;
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$is_filtered = ($search_query !== '' || $status_filter !== '');
?>

<div class="row" style="background-color: #f7fafc; min-height: 80vh; padding: 20px;">
    <div class="col-md-12">
        <h2 class="fw-bold" style="color: #2d3748;"><i class="bi bi-person-badge"></i> Driver Management</h2>
        <p class="text-muted">Register, update information, and track active driver duty statuses.</p>
        <hr style="border-color: #e2e8f0;">
        <?php echo $message; ?>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0" style="background-color: #ffffff;">
            <div class="card-header fw-bold text-white" style="background-color: #3182ce;">
                <i class="bi bi-plus-circle me-1"></i> Register New Driver
            </div>
            <div class="card-body" style="color: #2d3748;">
                <form action="drivers.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">License Number</label>
                        <input type="text" name="license_number" class="form-control" style="border-color: #e2e8f0;" required placeholder="e.g. B8374829">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">First Name</label>
                        <input type="text" name="first_name" class="form-control" style="border-color: #e2e8f0;" required placeholder="First name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Last Name</label>
                        <input type="text" name="last_name" class="form-control" style="border-color: #e2e8f0;" required placeholder="Last name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Contact</label>
                        <input type="text" name="phone" class="form-control" style="border-color: #e2e8f0;" placeholder="Contact number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Initial Status</label>
                        <select name="status" class="form-select" style="border-color: #e2e8f0;">
                            <option value="Available">Available</option>
                            <option value="On Duty">On Duty</option>
                            <option value="On Leave">On Leave</option>
                        </select>
                    </div>
                    <button type="submit" name="add_driver" class="btn text-white w-100 fw-bold" style="background-color: #3182ce;">
                        <i class="bi bi-person-plus-fill me-1"></i> Register Driver
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0 mb-3" style="background-color: #ffffff;">
            <div class="card-body p-3">
                <form method="GET" action="drivers.php" class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name or license..." value="<?php echo htmlspecialchars($search_query); ?>" style="border-color: #e2e8f0;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="status_filter" class="form-select" style="border-color: #e2e8f0;">
                            <option value="">All Statuses</option>
                            <option value="Available" <?php echo $status_filter === 'Available' ? 'selected' : ''; ?>>Available</option>
                            <option value="On Duty" <?php echo $status_filter === 'On Duty' ? 'selected' : ''; ?>>On Duty</option>
                            <option value="On Leave" <?php echo $status_filter === 'On Leave' ? 'selected' : ''; ?>>On Leave</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-1">
                        <button type="submit" class="btn btn-primary w-100 fw-semibold" style="background-color: #3182ce; border: none;">
                            <i class="bi bi-funnel-fill me-1"></i> Filter
                        </button>
                        <?php if ($is_filtered): ?>
                            <a href="drivers.php" class="btn btn-outline-secondary fw-semibold" title="Clear Search & Filter">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0" style="background-color: #ffffff;">
            <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-ul me-1"></i> Active Driver Logs & Assignments</span>
                <span class="badge bg-secondary"><?php echo count($drivers); ?> Registered</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="color: #2d3748;">
                        <thead class="table-light text-muted fw-semibold" style="border-bottom: 2px solid #e2e8f0;">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>License No</th>
                                <th>Full Name</th>
                                <th>Phone</th>
                                <th>Assignment Status</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($drivers)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <?php if ($is_filtered): ?>
                                            No drivers found matching your search criteria. <a href="drivers.php" class="text-primary text-decoration-none">Clear filter</a>
                                        <?php else: ?>
                                            No drivers registered inside the registry.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($drivers as $driver): ?>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td class="ps-3 text-muted"><?php echo $driver['id']; ?></td>
                                        <td><code class="text-dark fw-bold"><?php echo htmlspecialchars($driver['license_number']); ?></code></td>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($driver['first_name'] . ' ' . $driver['last_name']); ?></td>
                                        <td><?php echo !empty($driver['phone']) ? htmlspecialchars($driver['phone']) : '<span class="text-muted small">N/A</span>'; ?></td>
                                        <td>
                                            <?php if ($driver['status'] == 'Available'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1.5"><i class="bi bi-check-circle-fill me-1"></i> Available</span>
                                            <?php elseif ($driver['status'] == 'On Duty'): ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1.5"><i class="bi bi-cone-striped me-1"></i> Assigned / On Duty</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning dark border border-warning-subtle px-2 py-1.5"><i class="bi bi-slash-circle me-1"></i> On Leave</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-3">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-secondary edit-driver-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editDriverModal" 
                                                    data-id="<?php echo $driver['id']; ?>"
                                                    data-license="<?php echo htmlspecialchars($driver['license_number']); ?>"
                                                    data-first="<?php echo htmlspecialchars($driver['first_name']); ?>"
                                                    data-last="<?php echo htmlspecialchars($driver['last_name']); ?>"
                                                    data-phone="<?php echo htmlspecialchars($driver['phone']); ?>"
                                                    data-status="<?php echo htmlspecialchars($driver['status']); ?>">
                                                <i class="bi bi-pencil-square"></i> Edit info
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

<div class="modal fade" id="editDriverModal" tabindex="-1" aria-labelledby="editDriverModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #2d3748;">
                <h5 class="modal-title" id="editDriverModalLabel"><i class="bi bi-pencil-square me-1"></i> Update Driver Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="drivers.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="driver_id" id="modal_driver_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">License Number</label>
                        <input type="text" name="license_number" id="modal_license_number" class="form-control" required style="border-color: #e2e8f0;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">First Name</label>
                        <input type="text" name="first_name" id="modal_first_name" class="form-control" required style="border-color: #e2e8f0;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Last Name</label>
                        <input type="text" name="last_name" id="modal_last_name" class="form-control" required style="border-color: #e2e8f0;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Contact</label>
                        <input type="text" name="phone" id="modal_phone" class="form-control" style="border-color: #e2e8f0;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Duty Assignment Status</label>
                        <select name="status" id="modal_status" class="form-select" style="border-color: #e2e8f0;">
                            <option value="Available">Available</option>
                            <option value="On Duty">On Duty</option>
                            <option value="On Leave">On Leave</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #f7fafc;">
                    <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_driver" class="btn text-white fw-bold" style="background-color: #3182ce;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const editButtons = document.querySelectorAll('.edit-driver-btn');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const license = this.getAttribute('data-license');
            const first = this.getAttribute('data-first');
            const last = this.getAttribute('data-last');
            const phone = this.getAttribute('data-phone');
            const status = this.getAttribute('data-status');

            document.getElementById('modal_driver_id').value = id;
            document.getElementById('modal_license_number').value = license;
            document.getElementById('modal_first_name').value = first;
            document.getElementById('modal_last_name').value = last;
            document.getElementById('modal_phone').value = phone;
            document.getElementById('modal_status').value = status;
        });
    });
});
</script>
</body>
</html>