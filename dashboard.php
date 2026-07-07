<?php 
// Include the shared navigation bar and layout container
include_once 'includes/header.php'; 
?>

<div class="row">
    <div class="col-md-12">
        <h2 class="fw-bold mb-1">Depot Management Modules</h2>
        <p class="text-muted">Welcome to your operational dashboard control center.</p>
        <hr>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card h-100 shadow-sm border-0 border-start border-primary border-4">
            <div class="card-body">
                <h5 class="card-title fw-bold text-primary"><i class="bi bi-map"></i> Route Management</h5>
                <p class="card-text text-muted small">Add, edit, view, and search public transport route networks.</p>
                <a href="routes.php" class="btn btn-primary btn-sm mt-2">Open Module</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card h-100 shadow-sm border-0 border-start border-success border-4">
            <div class="card-body">
                <h5 class="card-title fw-bold text-success"><i class="bi bi-calendar-event"></i> Schedule Engine</h5>
                <p class="card-text text-muted small">Create operational shifts, map timelines, and run conflict detection algorithms.</p>
                <a href="schedules.php" class="btn btn-success btn-sm mt-2">Open Module</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card h-100 shadow-sm border-0 border-start border-warning border-4">
            <div class="card-body">
                <h5 class="card-title fw-bold text-warning"><i class="bi bi-file-earmark-pdf"></i> Reporting Hub</h5>
                <p class="card-text text-muted small">Monitor real-time logs and export administrative database records to valid compliance PDFs.</p>
                <a href="reports.php" class="btn btn-warning btn-sm mt-2">Open Module</a>
            </div>
        </div>
    </div>
</div>

</div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>