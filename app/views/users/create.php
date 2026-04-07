<div class="page-header">
    <div>
        <h1 class="page-title">Nuevo Usuario</h1>
        <p class="page-subtitle">El usuario recibirá instrucciones para crear su contraseña</p>
    </div>
    <a href="<?= url('users') ?>" class="btn btn-outline">← Volver</a>
</div>

<div class="card form-card">
    <form method="POST" action="<?= url('users/create') ?>">
        <?= csrf() ?>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Nombre *</label>
                <input type="text" name="nombre" class="form-input" placeholder="Pedro"
                       value="<?= e($_POST['nombre'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Apellido *</label>
                <input type="text" name="apellido" class="form-input" placeholder="González"
                       value="<?= e($_POST['apellido'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Correo Electrónico *</label>
                <input type="email" name="email" class="form-input" placeholder="usuario@fundacite.gob.ve"
                       value="<?= e($_POST['email'] ?? '') ?>" required>
                <p class="form-hint">Este correo recibirá alertas del sistema.</p>
            </div>
            <div class="form-group">
                <label class="form-label">Rol del Usuario *</label>
                <select name="rol_id" class="form-select" required>
                    <?php foreach ($roles as $rol): ?>
                    <option value="<?= $rol['id'] ?>" <?= ($_POST['rol_id'] ?? 3) == $rol['id'] ? 'selected' : '' ?>>
                        <?= e(ucfirst($rol['nombre'])) ?> — <?= e($rol['descripcion']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="info-box">
            🔐 El usuario iniciará sesión por primera vez <strong>sin contraseña</strong> y deberá crear una contraseña al entrar.
        </div>

        <div class="form-actions">
            <a href="<?= url('users') ?>" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">Crear Usuario</button>
        </div>
    </form>
</div>
