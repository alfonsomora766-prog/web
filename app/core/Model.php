<?php
// =====================================================
// FUNDACITE - Modelo Base
// =====================================================

class Model {
    protected PDO $db;
    protected string $table = '';

    public function __construct() {
        $this->db = Database::getConnection();
    }

    protected function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function find(int $id): ?array {
        return $this->query("SELECT * FROM {$this->table} WHERE id = ?", [$id])->fetch() ?: null;
    }

    public function all(string $order = 'id DESC'): array {
        return $this->query("SELECT * FROM {$this->table} ORDER BY {$order}")->fetchAll();
    }

    public function count(): int {
        return (int)$this->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    public function delete(int $id): bool {
        return (bool)$this->query("DELETE FROM {$this->table} WHERE id = ?", [$id])->rowCount();
    }
}
