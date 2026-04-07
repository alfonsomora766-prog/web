<?php
require_once BASE_PATH . '/app/core/Model.php';

class ActivityModel extends Model {
    protected string $table = 'actividades';

    public function allWithUsers(array $filters = [], int $limit = 20, int $offset = 0): array {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['estado'])) {
            $where[] = 'a.estado = ?';
            $params[] = $filters['estado'];
        }
        if (!empty($filters['fecha_desde'])) {
            $where[] = 'a.fecha_limite >= ?';
            $params[] = $filters['fecha_desde'];
        }
        if (!empty($filters['fecha_hasta'])) {
            $where[] = 'a.fecha_limite <= ?';
            $params[] = $filters['fecha_hasta'] . ' 23:59:59';
        }
        if (!empty($filters['usuario_id'])) {
            $where[] = 'a.creado_por = ?';
            $params[] = $filters['usuario_id'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(a.nombre LIKE ? OR a.descripcion LIKE ?)';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        if (isset($filters['aprobada'])) {
            $where[] = 'a.aprobada = ?';
            $params[] = $filters['aprobada'];
        }

        $whereStr = implode(' AND ', $where);
        $params[] = $limit;
        $params[] = $offset;

        return $this->query(
            "SELECT a.*, 
                    CONCAT(u.nombre, ' ', u.apellido) AS creador_nombre,
                    u.email AS creador_email
             FROM actividades a
             JOIN usuarios u ON a.creado_por = u.id
             WHERE {$whereStr}
             ORDER BY a.fecha_limite ASC
             LIMIT ? OFFSET ?",
            $params
        )->fetchAll();
    }

    public function countFiltered(array $filters = []): int {
        $where  = ['1=1'];
        $params = [];
        if (!empty($filters['estado']))     { $where[] = 'estado = ?'; $params[] = $filters['estado']; }
        if (!empty($filters['usuario_id'])) { $where[] = 'creado_por = ?'; $params[] = $filters['usuario_id']; }
        if (!empty($filters['search']))     { $where[] = '(nombre LIKE ? OR descripcion LIKE ?)'; $params[] = '%'.$filters['search'].'%'; $params[] = '%'.$filters['search'].'%'; }
        return (int)$this->query("SELECT COUNT(*) FROM actividades WHERE " . implode(' AND ', $where), $params)->fetchColumn();
    }

    public function create(array $data): int {
        $this->query(
            "INSERT INTO actividades (nombre, descripcion, requisitos, fecha_inicio, fecha_limite, estado, aprobada, creado_por) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['nombre'], $data['descripcion'], $data['requisitos'],
                $data['fecha_inicio'], $data['fecha_limite'],
                $data['estado'], $data['aprobada'] ?? 0, $data['creado_por']
            ]
        );
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $fields = [];
        $values = [];
        foreach ($data as $key => $val) { $fields[] = "$key = ?"; $values[] = $val; }
        $values[] = $id;
        return (bool)$this->query("UPDATE actividades SET " . implode(', ', $fields) . " WHERE id = ?", $values)->rowCount();
    }

    public function approve(int $id, int $adminId): bool {
        return (bool)$this->query(
            "UPDATE actividades SET aprobada = 1, estado = 'pendiente', aprobado_por = ?, fecha_aprobacion = NOW() WHERE id = ?",
            [$adminId, $id]
        )->rowCount();
    }

    public function reject(int $id): bool {
        return (bool)$this->query("UPDATE actividades SET estado = 'no_realizada', aprobada = 0 WHERE id = ?", [$id])->rowCount();
    }

    public function getStats(?int $userId = null): array {
        $where  = $userId ? 'WHERE creado_por = ?' : '';
        $params = $userId ? [$userId] : [];

        $totals = $this->query(
            "SELECT 
                COUNT(*) AS total,
                SUM(estado = 'pendiente') AS pendientes,
                SUM(estado = 'realizada') AS realizadas,
                SUM(estado = 'no_realizada') AS no_realizadas,
                SUM(estado = 'por_aprobar') AS por_aprobar
             FROM actividades $where",
            $params
        )->fetch();

        $byMonth = $this->query(
            "SELECT DATE_FORMAT(fecha_inicio, '%Y-%m') AS mes, COUNT(*) AS total 
             FROM actividades $where GROUP BY mes ORDER BY mes DESC LIMIT 12",
            $params
        )->fetchAll();

        $upcomingWhere  = "WHERE a.estado = 'pendiente' AND a.fecha_limite >= NOW()";
        $upcomingParams = [];
        if ($userId) {
            $upcomingWhere  .= " AND a.creado_por = ?";
            $upcomingParams[] = $userId;
        }

        $upcoming = $this->query(
            "SELECT a.nombre, a.fecha_limite, CONCAT(u.nombre,' ',u.apellido) AS creador
             FROM actividades a JOIN usuarios u ON a.creado_por = u.id
             $upcomingWhere
             ORDER BY a.fecha_limite ASC LIMIT 5",
            $upcomingParams
        )->fetchAll();

        $byUser = $userId ? [] : $this->query(
            "SELECT CONCAT(u.nombre,' ',u.apellido) AS usuario, COUNT(a.id) AS total
             FROM actividades a JOIN usuarios u ON a.creado_por = u.id
             GROUP BY a.creado_por ORDER BY total DESC LIMIT 10"
        )->fetchAll();

        return compact('totals', 'byMonth', 'upcoming', 'byUser');
    }

    public function forCalendar(int $year, int $month): array {
        return $this->query(
            "SELECT a.*, CONCAT(u.nombre,' ',u.apellido) AS creador_nombre
             FROM actividades a JOIN usuarios u ON a.creado_por = u.id
             WHERE (YEAR(a.fecha_inicio) = ? AND MONTH(a.fecha_inicio) = ?)
                OR (YEAR(a.fecha_limite) = ? AND MONTH(a.fecha_limite) = ?)
             ORDER BY a.fecha_limite ASC",
            [$year, $month, $year, $month]
        )->fetchAll();
    }

    public function forTimer(): array {
        return $this->query(
            "SELECT a.*, CONCAT(u.nombre,' ',u.apellido) AS creador_nombre
             FROM actividades a JOIN usuarios u ON a.creado_por = u.id
             WHERE a.estado = 'pendiente' AND a.aprobada = 1
             ORDER BY a.fecha_limite ASC"
        )->fetchAll();
    }

    public function markExpired(): int {
        return (int)$this->query(
            "UPDATE actividades SET estado = 'no_realizada' 
             WHERE estado = 'pendiente' AND aprobada = 1 AND fecha_limite < NOW()"
        )->rowCount();
    }

    public function getExpiredRecently(): array {
        return $this->query(
            "SELECT a.*, CONCAT(u.nombre,' ',u.apellido) AS creador_nombre, u.email AS creador_email
             FROM actividades a JOIN usuarios u ON a.creado_por = u.id
             WHERE a.estado = 'no_realizada' 
             AND a.fecha_limite >= DATE_SUB(NOW(), INTERVAL 1 HOUR)"
        )->fetchAll();
    }
}
