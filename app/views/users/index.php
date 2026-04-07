<div class="page-header">
    <div>
        <h1 class="page-title">Usuarios</h1>
        <p class="page-subtitle"><?= count($users) ?> usuarios registrados</p>
    </div>
    <a href="<?= url('users/create') ?>" class="btn btn-primary">+ Nuevo Usuario</a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Primer Login</th>
                    <th>Creado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $u): ?>
            <tr class="<?= !$u['activo'] ? 'row-muted' : '' ?>">
                <td>
                    <div class="user-cell">
                        <div class="user-avatar-sm"><?= strtoupper(substr($u['nombre'], 0, 1)) ?></div>
                        <div>
                            <div class="user-fullname"><?= e($u['nombre'] . ' ' . $u['apellido']) ?></div>
                        </div>
                    </div>
                </td>
                <td><?= e($u['email']) ?></td>
                <td>
                    <?php $rolColors = ['admin'=>'danger','operativo'=>'warning','visualizacion'=>'info'];
                    $rolColor = $rolColors[$u['rol']] ?? 'info'; ?>
                    <span class="badge badge-<?= $rolColor ?>"><?= e($u['rol']) ?></span>
                </td>
                <td>
                    <span class="badge badge-<?= $u['activo'] ? 'success' : 'muted' ?>">
                        <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
                    </span>
                </td>
                <td>
                    <?php if ($u['primer_login']): ?>
                    <span class="badge badge-warning">Pendiente</span>
                    <?php else: ?>
                    <span class="badge badge-success">Completado</span>
                    <?php endif; ?>
                </td>
                <td><?= formatDate($u['created_at']) ?></td>
                <td class="actions-cell">
                    <a href="<?= url('users/edit/' . $u['id']) ?>" class="btn-action btn-edit" title="Editar">✎</a>
                    <a href="<?= url('users/toggle/' . $u['id']) ?>" class="btn-action btn-outline"
                       title="<?= $u['activo'] ? 'Desactivar' : 'Activar' ?>">
                       <?= $u['activo'] ? '⏸' : '▶' ?>
                    </a>
                    <?php $currentUser = currentUser();
                    if ($u['id'] != $currentUser['id']): ?>
                    <a href="<?= url('users/delete/' . $u['id']) ?>" class="btn-action btn-delete"
                       title="Eliminar" onclick="return confirm('¿Eliminar al usuario <?= e(addslashes($u['nombre'])) ?>?')">🗑</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
