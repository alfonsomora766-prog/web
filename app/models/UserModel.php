<?php
require_once BASE_PATH . '/app/core/Model.php';

class UserModel extends Model {
    protected string $table = 'usuarios';

    public function findByEmail(string $email): ?array {
        return $this->query(
            "SELECT u.*, r.nombre AS rol FROM usuarios u 
             JOIN roles r ON u.rol_id = r.id 
             WHERE u.email = ? AND u.activo = 1",
            [$email]
        )->fetch() ?: null;
    }

    public function findWithRole(int $id): ?array {
        return $this->query(
            "SELECT u.*, r.nombre AS rol FROM usuarios u 
             JOIN roles r ON u.rol_id = r.id WHERE u.id = ?",
            [$id]
        )->fetch() ?: null;
    }

    public function allWithRoles(): array {
        return $this->query(
            "SELECT u.*, r.nombre AS rol FROM usuarios u 
             JOIN roles r ON u.rol_id = r.id ORDER BY u.created_at DESC"
        )->fetchAll();
    }

    public function getAdmins(): array {
        return $this->query(
            "SELECT u.email, u.nombre FROM usuarios u 
             JOIN roles r ON u.rol_id = r.id WHERE r.nombre = 'admin' AND u.activo = 1"
        )->fetchAll();
    }

    public function create(array $data): int {
        $this->query(
            "INSERT INTO usuarios (nombre, apellido, email, password, rol_id, primer_login, created_by) 
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $data['nombre'], $data['apellido'], $data['email'],
                null, $data['rol_id'], 1, $data['created_by'] ?? null
            ]
        );
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $fields = [];
        $values = [];
        foreach ($data as $key => $val) {
            $fields[] = "$key = ?";
            $values[] = $val;
        }
        $values[] = $id;
        return (bool)$this->query(
            "UPDATE usuarios SET " . implode(', ', $fields) . " WHERE id = ?",
            $values
        )->rowCount();
    }

    public function setPassword(int $id, string $password): bool {
        return $this->update($id, [
            'password'     => password_hash($password, PASSWORD_BCRYPT),
            'primer_login' => 0
        ]);
    }

    public function resetPassword(int $id): bool {
        return $this->update($id, ['password' => null, 'primer_login' => 1]);
    }

    public function getRoles(): array {
        return $this->query("SELECT * FROM roles ORDER BY id")->fetchAll();
    }

    public function countByRole(): array {
        return $this->query(
            "SELECT r.nombre AS rol, COUNT(u.id) AS total 
             FROM roles r LEFT JOIN usuarios u ON u.rol_id = r.id AND u.activo = 1 
             GROUP BY r.id"
        )->fetchAll();
    }

    /** IDs de todos los admins activos */
    public function getAdminIds(): array {
        return array_column(
            $this->query("SELECT id FROM usuarios WHERE rol_id = 1 AND activo = 1")->fetchAll(),
            'id'
        );
    }

    /** IDs de todos los usuarios activos */
    public function getAllActiveIds(): array {
        return array_column(
            $this->query("SELECT id FROM usuarios WHERE activo = 1")->fetchAll(),
            'id'
        );
    }
}
