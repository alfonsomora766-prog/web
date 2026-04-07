<div class="page-header">
    <div>
        <h1 class="page-title">🔔 Notificaciones</h1>
        <p class="page-subtitle"><?= count($notifications) ?> notificaciones</p>
    </div>
    <a href="<?= url('notifications/all') ?>" class="btn btn-outline">✓ Marcar todas como leídas</a>
</div>

<div class="card">
    <?php if (empty($notifications)): ?>
    <div class="empty-state-big">
        <div class="empty-icon">🔔</div>
        <p>No tienes notificaciones.</p>
    </div>
    <?php else: ?>
    <div class="notif-full-list">
        <?php foreach ($notifications as $n): ?>
        <div class="notif-full-item notif-full-<?= e($n['tipo']) ?> <?= $n['leida'] ? 'notif-read' : 'notif-unread' ?>">
            <div class="notif-full-dot"></div>
            <div class="notif-full-content">
                <div class="notif-full-title"><?= e($n['titulo']) ?></div>
                <div class="notif-full-msg"><?= e($n['mensaje']) ?></div>
                <div class="notif-full-time">
                    <?= formatDateTime($n['created_at']) ?>
                    <?php if (!$n['leida']): ?>
                    <span class="badge badge-warning">Nuevo</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!$n['leida']): ?>
            <a href="<?= url('notifications/read/' . $n['id']) ?>" class="btn-action btn-approve" title="Marcar leída">✓</a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
