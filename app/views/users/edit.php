<div class="page-header">
    <div>
        <h1 class="page-title">Editar Usuario</h1>
        <p class="page-subtitle"><?= e($editUser['nombre'] . ' ' . $editUser['apellido']) ?></p>
    </div>
    <a href="<?= url('users') ?>" class="btn btn-outline">← Volver</a>
</div>

<div class="card form-card">
    <form method="POST" action="<?= url('users/edit/' . $editUser['id']) ?>">
        <?= csrf() ?>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-input" value="<?= e($editUser['nombre']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Apellido</label>
                <input type="text" name="apellido" class="form-input" value="<?= e($editUser['apellido']) ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Correo</label>
                <input type="email" name="email" class="form-input" value="<?= e($editUser['email']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Rol</label>
                <select name="rol_id" class="form-select">
                    <?php foreach ($roles as $rol): ?>
                    <option value="<?= $rol['id'] ?>" <?= $editUser['rol_id'] == $rol['id'] ? 'selected' : '' ?>>
                        <?= e(ucfirst($rol['nombre'])) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="card-section">
            <h4 class="section-title">🔑 Restablecer Contraseña</h4>
            <label class="checkbox-label">
                <input type="checkbox" name="reset_password" value="1" id="resetPass">
                <span>Forzar al usuario a crear una nueva contraseña en su próximo inicio de sesión</span>
            </label>
        </div>

        <div class="form-actions">
            <a href="<?= url('users') ?>" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>
