<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($settings['nombre_sistema'] ?? APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <?php if (isset($extraCss)) echo $extraCss; ?>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <?php if (!empty($settings['logo'])): ?>
            <img src="<?= asset('uploads/' . e($settings['logo'])) ?>" alt="Logo" class="sidebar-logo">
        <?php else: ?>
            <div class="sidebar-brand">
                <span class="brand-icon">⚙</span>
                <div>
                    <div class="brand-name"><?= e($settings['nombre_sistema'] ?? 'FUNDACITE') ?></div>
                    <div class="brand-sub">Carabobo</div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <nav class="sidebar-nav">
        <a href="<?= url('dashboard') ?>" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], 'dashboard') ? 'active' : '' ?>">
            <span class="nav-icon">◈</span> Dashboard
        </a>
        <a href="<?= url('activities') ?>" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], 'activities') ? 'active' : '' ?>">
            <span class="nav-icon">◉</span> Actividades
        </a>
        <a href="<?= url('activities/calendar') ?>" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], 'calendar') ? 'active' : '' ?>">
            <span class="nav-icon">◫</span> Calendario
        </a>
        <a href="<?= url('activities/timer') ?>" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], 'timer') ? 'active' : '' ?>">
            <span class="nav-icon">◷</span> Cronómetros
        </a>
        <?php if (hasRole('admin')): ?>
        <div class="nav-divider">Administración</div>
        <a href="<?= url('users') ?>" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], 'users') ? 'active' : '' ?>">
            <span class="nav-icon">◎</span> Usuarios
        </a>
        <a href="<?= url('settings') ?>" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], 'settings') ? 'active' : '' ?>">
            <span class="nav-icon">◌</span> Configuración
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($user['nombre'] ?? 'U', 0, 1)) ?></div>
            <div class="user-details">
                <div class="user-name"><?= e(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? '')) ?></div>
                <div class="user-role"><?= e($user['rol'] ?? '') ?></div>
            </div>
        </div>
        <a href="<?= url('profile') ?>" class="btn-icon" title="Perfil">✎</a>
        <a href="<?= url('logout') ?>" class="btn-icon btn-logout" title="Cerrar sesión">⏻</a>
    </div>
</aside>

<!-- Main Content -->
<div class="main-wrapper">
    <!-- Top Bar -->
    <header class="topbar">
        <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
        <div class="topbar-title" id="pageTitle"></div>
        <div class="topbar-actions">
            <!-- Notificaciones -->
            <div class="notif-wrapper">
                <button class="notif-btn" onclick="toggleNotifPanel()" id="notifBtn">
                    🔔
                    <span class="notif-badge" id="notifBadge" style="display:none">0</span>
                </button>
                <div class="notif-panel" id="notifPanel">
                    <div class="notif-header">
                        <span>Notificaciones</span>
                        <a href="<?= url('notifications/all') ?>" class="notif-all">Leer todas</a>
                    </div>
                    <div class="notif-list" id="notifList">
                        <div class="notif-empty">Cargando...</div>
                    </div>
                    <div class="notif-footer">
                        <a href="<?= url('notifications') ?>">Ver todas</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Message -->
    <?php $flash = getFlash(); if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>" id="flashMsg">
        <?= e($flash['message']) ?>
        <button onclick="this.parentElement.remove()">×</button>
    </div>
    <?php endif; ?>

    <!-- Page Content -->
    <main class="content">
        <?php require $content; ?>
    </main>
</div>

<script src="<?= asset('js/main.js') ?>"></script>
<?php if (isset($extraJs)) echo $extraJs; ?>
<script>
// Notificaciones en tiempo real
function loadNotifications() {
    fetch('<?= url('notifications/api') ?>')
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('notifBadge');
            const list  = document.getElementById('notifList');
            if (data.unread > 0) {
                badge.style.display = 'flex';
                badge.textContent   = data.unread > 9 ? '9+' : data.unread;
            } else {
                badge.style.display = 'none';
            }
            if (data.notifications.length === 0) {
                list.innerHTML = '<div class="notif-empty">Sin notificaciones nuevas</div>';
                return;
            }
            list.innerHTML = data.notifications.slice(0, 6).map(n => `
                <a href="<?= url('notifications/read') ?>/${n.id}?redirect=${encodeURIComponent(n.url || '<?= url('dashboard') ?>')}" class="notif-item notif-${n.tipo}">
                    <div class="notif-text">${n.titulo}</div>
                    <div class="notif-time">${n.created_at}</div>
                </a>
            `).join('');
        }).catch(() => {});
}
loadNotifications();
setInterval(loadNotifications, 30000);
</script>
</body>
</html>
