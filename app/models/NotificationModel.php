<?php
require_once BASE_PATH . '/app/core/Model.php';

class NotificationModel extends Model {
    protected string $table = 'notificaciones';

    public function forUser(int $userId, bool $unreadOnly = false): array {
        $where = $unreadOnly ? 'AND leida = 0' : '';
        return $this->query(
            "SELECT * FROM notificaciones WHERE usuario_id = ? $where ORDER BY created_at DESC LIMIT 50",
            [$userId]
        )->fetchAll();
    }

    public function countUnread(int $userId): int {
        return (int)$this->query(
            "SELECT COUNT(*) FROM notificaciones WHERE usuario_id = ? AND leida = 0",
            [$userId]
        )->fetchColumn();
    }

    public function create(int $userId, string $titulo, string $mensaje, string $tipo = 'info', string $url = ''): int {
        $this->query(
            "INSERT INTO notificaciones (usuario_id, titulo, mensaje, tipo, url) VALUES (?,?,?,?,?)",
            [$userId, $titulo, $mensaje, $tipo, $url]
        );
        return (int)$this->db->lastInsertId();
    }

    public function notifyAll(array $userIds, string $titulo, string $mensaje, string $tipo = 'info', string $url = ''): void {
        foreach ($userIds as $uid) {
            $this->create($uid, $titulo, $mensaje, $tipo, $url);
        }
    }

    public function markRead(int $id, int $userId): bool {
        return (bool)$this->query(
            "UPDATE notificaciones SET leida = 1 WHERE id = ? AND usuario_id = ?",
            [$id, $userId]
        )->rowCount();
    }

    public function markAllRead(int $userId): bool {
        return (bool)$this->query(
            "UPDATE notificaciones SET leida = 1 WHERE usuario_id = ?",
            [$userId]
        )->rowCount();
    }
}
