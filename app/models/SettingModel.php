<?php
require_once BASE_PATH . '/app/core/Model.php';

class SettingModel extends Model {
    protected string $table = 'configuraciones';

    public function get(string $key, string $default = ''): string {
        $row = $this->query("SELECT valor FROM configuraciones WHERE clave = ?", [$key])->fetch();
        return $row ? ($row['valor'] ?? $default) : $default;
    }

    public function set(string $key, string $value): void {
        $this->query(
            "INSERT INTO configuraciones (clave, valor) VALUES (?,?) ON DUPLICATE KEY UPDATE valor = ?",
            [$key, $value, $value]
        );
    }

    public function allSettings(): array {
        $rows   = $this->query("SELECT clave, valor FROM configuraciones")->fetchAll();
        $result = [];
        foreach ($rows as $row) $result[$row['clave']] = $row['valor'];
        return $result;
    }

    public function updateMany(array $data): void {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }
}
