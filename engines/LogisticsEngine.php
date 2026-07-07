<?php
class LogisticsEngine {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }

    // 1. Route Management & General Metrics Business Logic
    public function getSystemMetrics() {
        return [
            'routes'    => $this->db->query("SELECT COUNT(*) FROM routes")->fetchColumn(),
            'drivers'   => $this->db->query("SELECT COUNT(*) FROM drivers")->fetchColumn(),
            'vehicles'  => $this->db->query("SELECT COUNT(*) FROM vehicles")->fetchColumn(),
            'schedules' => $this->db->query("SELECT COUNT(*) FROM schedules")->fetchColumn()
        ];
    }

    // 2. Scheduling Engine & Reporting Module Logic (Processes Filters)
    public function generateFilteredReport($filters) {
        $whereClauses = [];
        $bindings = [];

        if (!empty($filters['route_id'])) {
            $whereClauses[] = "s.route_id = :route_id";
            $bindings['route_id'] = $filters['route_id'];
        }
        if (!empty($filters['driver_id'])) {
            $whereClauses[] = "s.driver_id = :driver_id";
            $bindings['driver_id'] = $filters['driver_id'];
        }
        if (!empty($filters['vehicle_id'])) {
            $whereClauses[] = "s.vehicle_id = :vehicle_id";
            $bindings['vehicle_id'] = $filters['vehicle_id'];
        }
        if (!empty($filters['start_date'])) {
            $whereClauses[] = "s.departure_time >= :start_date";
            $bindings['start_date'] = $filters['start_date'] . " 00:00:00";
        }
        if (!empty($filters['end_date'])) {
            $whereClauses[] = "s.departure_time <= :end_date";
            $bindings['end_date'] = $filters['end_date'] . " 23:59:59";
        }

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

        $stmt = $this->db->prepare($queryStr);
        $stmt->execute($bindings);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>