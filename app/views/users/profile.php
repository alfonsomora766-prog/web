<div class="page-header">
    <div>
        <h1 class="page-title">Mi Perfil</h1>
        <p class="page-subtitle">Gestiona tu información personal</p>
    </div>
</div>

<div class="profile-grid">
    <div class="card profile-card">
        <div class="profile-avatar-big"><?= strtoupper(substr($user['nombre'], 0, 1)) ?></div>
        <div class="profile-info">
            <div class="profile-name"><?= e($user['nombre'] . ' ' . $user['apellido']) ?></div>
            <div class="profile-email"><?= e($user['email']) ?></div>
            <span class="badge badge-info"><?= e($user['rol']) ?></span>
        </div>
    </div>

    <div class="card form-card">
        <form method="POST" action="<?= url('profile') ?>">
            <?= csrf() ?>

            <h3 class="form-section-title">Datos Personales</h3>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-input" value="<?= e($user['nombre']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="apellido" class="form-input" value="<?= e($user['apellido']) ?>" required>
                </div>
            </div>

            <h3 class="form-section-title">Cambiar Contraseña</h3>
            <div class="form-group">
                <label class="form-label">Contraseña Actual</label>
                <input type="password" name="current_password" class="form-input" placeholder="Tu contraseña actual">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nueva Contraseña</label>
                    <input type="password" name="new_password" class="form-input" placeholder="Mínimo 8 caracteres">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirmar Nueva</label>
                    <input type="password" name="confirm_password" class="form-input" placeholder="Repite la contraseña">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
