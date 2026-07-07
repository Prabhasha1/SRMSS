<?php 
require_once 'config/db.php';
include_once 'includes/header.php'; 

$message = '';

// 1. Functional Requirement: Delete Route
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM routes WHERE id = :id");
        $stmt->execute(['id' => $delete_id]);
        $message = '<div class="alert alert-success border-0 shadow-sm animate-fade py-3" style="background-color: #e6fffa; color: #319795; border-left: 4px solid #319795;"><i class="bi bi-check-circle-fill me-2"></i>Route wiped from database layers safely.</div>';
    } catch (PDOException $e) {
        $message = '<div class="alert alert-danger border-0 shadow-sm animate-fade py-3" style="background-color: #fff5f5; color: #e53e3e; border-left: 4px solid #e53e3e;"><i class="bi bi-exclamation-triangle-fill me-2"></i>Deletion Error: Check if this route is tied to active schedules.</div>';
    }
}

// 2. Functional Requirement: Add Route
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_route'])) {
    $route_number = trim($_POST['route_number']);
    $start_location = trim($_POST['start_location']);
    $end_location = trim($_POST['end_location']);
    $distance_km = trim($_POST['distance_km']);

    if (!empty($route_number) && !empty($start_location) && !empty($end_location) && !empty($distance_km)) {
        try {
            $stmt = $conn->prepare("INSERT INTO routes (route_number, start_location, end_location, distance_km) VALUES (:route_number, :start_location, :end_location, :distance_km)");
            $stmt->execute([
                'route_number' => $route_number,
                'start_location' => $start_location,
                'end_location' => $end_location,
                'distance_km' => $distance_km
            ]);
            $message = '<div class="alert alert-success border-0 shadow-sm animate-fade py-3" style="background-color: #e6fffa; color: #319795; border-left: 4px solid #319795;"><i class="bi bi-check-circle-fill me-2"></i>New route vector registered successfully.</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger border-0 shadow-sm animate-fade py-3" style="background-color: #fff5f5; color: #e53e3e; border-left: 4px solid #e53e3e;"><i class="bi bi-exclamation-triangle-fill me-2"></i>Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// 3. Functional Requirement: Edit/Update Route
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_route'])) {
    $id = $_POST['id'];
    $route_number = trim($_POST['route_number']);
    $start_location = trim($_POST['start_location']);
    $end_location = trim($_POST['end_location']);
    $distance_km = trim($_POST['distance_km']);

    if (!empty($id) && !empty($route_number) && !empty($start_location) && !empty($end_location) && !empty($distance_km)) {
        try {
            $stmt = $conn->prepare("UPDATE routes SET route_number = :route_number, start_location = :start_location, end_location = :end_location, distance_km = :distance_km WHERE id = :id");
            $stmt->execute([
                'id' => $id,
                'route_number' => $route_number,
                'start_location' => $start_location,
                'end_location' => $end_location,
                'distance_km' => $distance_km
            ]);
            $message = '<div class="alert alert-info border-0 shadow-sm animate-fade py-3" style="background-color: #ebf8ff; color: #2b6cb0; border-left: 4px solid #3182ce;"><i class="bi bi-info-circle-fill me-2"></i>Route matrix parameters modified successfully.</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger border-0 shadow-sm animate-fade py-3" style="background-color: #fff5f5; color: #e53e3e; border-left: 4px solid #e53e3e;"><i class="bi bi-exclamation-triangle-fill me-2"></i>Modification Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// 4. Functional Requirement: Search Route
$search_query = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM routes WHERE route_number LIKE :search OR start_location LIKE :search OR end_location LIKE :search ORDER BY id DESC");
    $stmt->execute(['search' => "%$search_query%"]);
    $routes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $routes = $conn->query("SELECT * FROM routes ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<style>
/* Exact alignment with your clean corporate UI dashboard theme */
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
        <h2 class="fw-bold text-custom-dark mb-1"><i class="bi bi-map text-primary me-2"></i>Route Network Hub</h2>
        <p class="text-muted">Perform real-time asset alignment, data tracking, and algorithmic path manipulation.</p>
    </div>
    
    <div class="col-md-12 mb-2"><?php echo $message; ?></div>

    <div class="col-md-12 mb-4">
        <div class="card theme-card p-3">
            <form action="routes.php" method="GET" class="row g-2">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text theme-input border-end-0 bg-light"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control theme-input border-start-0" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search routes by system code, origin transit hub, or final terminal drop points...">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold h-100 rounded-2 shadow-sm">
                        <i class="bi bi-sliders me-1"></i> Filter Matrix
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card theme-card">
            <div class="card-header theme-card-header py-3 text-primary">
                <i class="bi bi-plus-circle me-2"></i> Add New Route Target
            </div>
            <div class="card-body p-4">
                <form action="routes.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Route Code ID</label>
                        <input type="text" name="route_number" class="form-control theme-input" placeholder="e.g. R-705" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Origin Hub Location</label>
                        <input type="text" name="start_location" class="form-control theme-input" placeholder="City A Depot" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Terminal Drop Point</label>
                        <input type="text" name="end_location" class="form-control theme-input" placeholder="City B Depot" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Route Distance (KM)</label>
                        <input type="number" step="0.01" min="0" name="distance_km" class="form-control theme-input" placeholder="0.00" required>
                    </div>
                    <button type="submit" name="add_route" class="btn btn-primary w-100 fw-bold py-2 shadow-sm rounded-2 mt-2">
                        <i class="bi bi-save me-1"></i> Save Route Matrix
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card theme-card">
            <div class="card-header theme-card-header py-3 text-custom-dark">
                <i class="bi bi-hdd-network text-success me-2"></i> Active Transit Pipelines
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-custom-dark">
                        <thead class="table-header-row text-uppercase small">
                            <tr>
                                <th class="ps-4 py-3">Route ID</th>
                                <th>Start Journey</th>
                                <th>End Journey</th>
                                <th>Metrics</th>
                                <th class="text-center">Database Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($routes)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-5">No active network data segments fit the search criteria.</td></tr>
                            <?php else: ?>
                                <?php foreach ($routes as $route): ?>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td class="ps-4">
                                            <span class="badge border p-2 fw-bold" style="background-color: #ebf8ff; border-color: #bee3f8 !important; color: #2b6cb0;">
                                                <?php echo htmlspecialchars($route['route_number']); ?>
                                            </span>
                                        </td>
                                        <td class="fw-semibold text-custom-dark"><?php echo htmlspecialchars($start_loc = $route['start_location']); ?></td>
                                        <td class="fw-semibold text-custom-dark"><?php echo htmlspecialchars($route['end_location']); ?></td>
                                        <td><span class="text-success fw-bold"><?php echo htmlspecialchars($route['distance_km']); ?></span> <small class="text-muted">KM</small></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-2 px-2 py-1" data-bs-toggle="modal" data-bs-target="#editRouteModal<?php echo $route['id']; ?>">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </button>
                                                <a href="routes.php?delete_id=<?php echo $route['id']; ?>" onclick="return confirm('Wipe route parameters permanently?');" class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1">
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

<?php if (!empty($routes)): ?>
    <?php foreach ($routes as $route): ?>
        <div class="modal fade" id="editRouteModal<?php echo $route['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $route['id']; ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; background-color: #ffffff;">
                    <div class="modal-header" style="border-bottom: 1px solid #e2e8f0;">
                        <h5 class="modal-title fw-bold text-custom-dark" id="editModalLabel<?php echo $route['id']; ?>"><i class="bi bi-pencil-square text-primary me-2"></i>Modify Route Parameters</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="routes.php" method="POST">
                        <div class="modal-body p-4">
                            <input type="hidden" name="id" value="<?php echo $route['id']; ?>">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Route Code ID</label>
                                <input type="text" name="route_number" class="form-control theme-input" value="<?php echo htmlspecialchars($route['route_number']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Origin Location Hub</label>
                                <input type="text" name="start_location" class="form-control theme-input" value="<?php echo htmlspecialchars($route['start_location']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Terminal Drop Point</label>
                                <input type="text" name="end_location" class="form-control theme-input" value="<?php echo htmlspecialchars($route['end_location']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Route Length Distance (KM)</label>
                                <input type="number" step="0.01" min="0" name="distance_km" class="form-control theme-input" value="<?php echo htmlspecialchars($route['distance_km']); ?>" required>
                            </div>
                        </div>
                        <div class="modal-footer" style="border-top: 1px solid #e2e8f0;">
                            <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="edit_route" class="btn btn-primary fw-semibold px-4">Update Changes</button>
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