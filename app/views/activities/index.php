<div class="page-header">
    <div>
        <h1 class="page-title">Actividades</h1>
        <p class="page-subtitle"><?= $pagination['total'] ?> actividades encontradas</p>
    </div>
    <div class="page-actions">
        <?php if (hasRole('admin','operativo')): ?>
        <a href="<?= url('activities/create') ?>" class="btn btn-primary">+ Nueva Actividad</a>
        <?php endif; ?>
        <?php if (hasRole('admin','operativo')): ?>
        <a href="<?= url('activities/export') ?>?<?= http_build_query($filters) ?>" class="btn btn-outline">⬇ Excel</a>
        <?php endif; ?>
    </div>
</div>

<!-- Filtros -->
<div class="card filters-card">
    <form method="GET" action="<?= url('activities') ?>" class="filters-form">
        <input type="text" name="q" placeholder="🔍 Buscar actividad..." class="form-input" value="<?= e($filters['search']) ?>">
        <select name="estado" class="form-select">
            <option value="">Todos los estados</option>
            <option value="pendiente"    <?= $filters['estado']==='pendiente'    ? 'selected':'' ?>>🟡 Por Realizar</option>
            <option value="realizada"    <?= $filters['estado']==='realizada'    ? 'selected':'' ?>>🟢 Realizada</option>
            <option value="no_realizada" <?= $filters['estado']==='no_realizada' ? 'selected':'' ?>>🔴 No Realizada</option>
            <option value="por_aprobar"  <?= $filters['estado']==='por_aprobar'  ? 'selected':'' ?>>⏳ Por Aprobar</option>
        </select>
        <input type="date" name="fecha_desde" class="form-input" value="<?= e($filters['fecha_desde']) ?>" placeholder="Desde">
        <input type="date" name="fecha_hasta" class="form-input" value="<?= e($filters['fecha_hasta']) ?>" placeholder="Hasta">
        <?php if (hasRole('admin') && !empty($users)): ?>
        <select name="usuario_id" class="form-select">
            <option value="">Todos los usuarios</option>
            <?php foreach ($users as $u): ?>
            <option value="<?= $u['id'] ?>"><?= e($u['nombre'].' '.$u['apellido']) ?></option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <a href="<?= url('activities') ?>" class="btn btn-outline">Limpiar</a>
    </form>
</div>

<!-- Tabla -->
<div class="card">
    <?php if (empty($activities)): ?>
    <div class="empty-state-big">
        <div class="empty-icon">◫</div>
        <p>No se encontraron actividades con esos filtros.</p>
        <a href="<?= url('activities') ?>" class="btn btn-outline">Ver todas</a>
    </div>
    <?php else: ?>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Actividad</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Límite</th>
                    <th>Responsable</th>
                    <th>Estado</th>
                    <th>Tiempo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($activities as $act):
                $tl = timeLeft($act['fecha_limite']);
            ?>
            <tr class="<?= $act['estado'] === 'no_realizada' ? 'row-danger' : '' ?>">
                <td>
                    <div class="act-name"><?= e($act['nombre']) ?></div>
                    <?php if ($act['descripcion']): ?>
                    <div class="act-desc"><?= e(substr($act['descripcion'], 0, 80)) ?>...</div>
                    <?php endif; ?>
                </td>
                <td><?= formatDate($act['fecha_inicio']) ?></td>
                <td><?= formatDateTime($act['fecha_limite']) ?></td>
                <td><?= e($act['creador_nombre']) ?></td>
                <td><?= statusBadge($act['estado']) ?></td>
                <td>
                    <?php if ($tl['expired']): ?>
                        <span class="badge badge-danger">Vencida</span>
                    <?php elseif ($tl['days'] <= 1): ?>
                        <span class="badge badge-warning"><?= $tl['text'] ?></span>
                    <?php else: ?>
                        <span class="time-left"><?= $tl['text'] ?></span>
                    <?php endif; ?>
                </td>
                <td class="actions-cell">
                    <?php if (hasRole('admin') || (hasRole('operativo') && $act['creado_por'] == ($user['id'] ?? 0))): ?>
                    <a href="<?= url('activities/edit/' . $act['id']) ?>" class="btn-action btn-edit" title="Editar">✎</a>
                    <?php endif; ?>
                    <?php if (hasRole('admin') && $act['estado'] === 'por_aprobar'): ?>
                    <a href="<?= url('activities/approve/' . $act['id']) ?>" class="btn-action btn-approve" title="Aprobar">✓</a>
                    <a href="<?= url('activities/reject/' . $act['id']) ?>" class="btn-action btn-reject" title="Rechazar">✕</a>
                    <?php endif; ?>
                    <?php if (hasRole('admin')): ?>
                    <a href="<?= url('activities/delete/' . $act['id']) ?>" class="btn-action btn-delete"
                       title="Eliminar" onclick="return confirm('¿Eliminar esta actividad?')">🗑</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <?php if ($pagination['pages'] > 1): ?>
    <div class="pagination">
        <?php if ($pagination['prev']): ?>
        <a href="?page=<?= $pagination['prev'] ?>&<?= http_build_query($filters) ?>" class="page-btn">‹</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $pagination['pages']; $i++): ?>
        <a href="?page=<?= $i ?>&<?= http_build_query($filters) ?>"
           class="page-btn <?= $i == $pagination['current'] ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($pagination['next']): ?>
        <a href="?page=<?= $pagination['next'] ?>&<?= http_build_query($filters) ?>" class="page-btn">›</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
