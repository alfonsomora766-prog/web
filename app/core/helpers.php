<?php
// =====================================================
// FUNDACITE - Funciones Helper Globales
// =====================================================

function url(string $path = ''): string {
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string {
    return BASE_URL . '/public/' . ltrim($path, '/');
}

function redirect(string $path): void {
    header('Location: ' . url($path));
    exit;
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

function hasRole(string ...$roles): bool {
    $user = currentUser();
    return $user && in_array($user['rol'], $roles);
}

function requireAuth(): void {
    if (!isLoggedIn()) {
        redirect('login');
    }
}

function requireRole(string ...$roles): void {
    requireAuth();
    if (!hasRole(...$roles)) {
        redirect('dashboard');
    }
}

function e(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function formatDate(string $date): string {
    return date('d/m/Y', strtotime($date));
}

function formatDateTime(string $datetime): string {
    return date('d/m/Y H:i', strtotime($datetime));
}

function timeLeft(string $deadline): array {
    $now  = new DateTime();
    $end  = new DateTime($deadline);
    $diff = $now->diff($end);

    if ($now > $end) {
        return ['expired' => true, 'text' => 'Vencida', 'seconds' => 0];
    }

    $totalSeconds = $end->getTimestamp() - $now->getTimestamp();
    return [
        'expired' => false,
        'days'    => $diff->days,
        'hours'   => $diff->h,
        'minutes' => $diff->i,
        'seconds' => $diff->s,
        'total'   => $totalSeconds,
        'text'    => $diff->days . 'd ' . $diff->h . 'h ' . $diff->i . 'm'
    ];
}

function statusBadge(string $status): string {
    return match($status) {
        'realizada'    => '<span class="badge badge-success">✅ Realizada</span>',
        'no_realizada' => '<span class="badge badge-danger">🔴 No Realizada</span>',
        'por_aprobar'  => '<span class="badge badge-warning">⏳ Por Aprobar</span>',
        default        => '<span class="badge badge-pending">🟡 Por Realizar</span>',
    };
}

function csrf(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

function verifyCsrf(): bool {
    return isset($_POST['csrf_token']) && $_POST['csrf_token'] === ($_SESSION['csrf_token'] ?? '');
}

function paginate(int $total, int $perPage, int $current): array {
    $pages = (int)ceil($total / $perPage);
    return [
        'total'   => $total,
        'pages'   => $pages,
        'current' => $current,
        'prev'    => $current > 1 ? $current - 1 : null,
        'next'    => $current < $pages ? $current + 1 : null,
        'offset'  => ($current - 1) * $perPage,
        'limit'   => $perPage,
    ];
}
