<div class="page-header">
    <div>
        <h1 class="page-title">⚙ Configuración del Sistema</h1>
        <p class="page-subtitle">Personalización visual y correo electrónico</p>
    </div>
</div>

<div class="settings-grid">
    <form method="POST" action="<?= url('settings') ?>" enctype="multipart/form-data">
        <?= csrf() ?>

        <!-- Identidad Visual -->
        <div class="card form-card">
            <h3 class="form-section-title">🎨 Identidad Visual</h3>

            <div class="form-group">
                <label class="form-label">Nombre del Sistema</label>
                <input type="text" name="nombre_sistema" class="form-input"
                       value="<?= e($settings['nombre_sistema'] ?? 'FUNDACITE Carabobo') ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Logo Institucional</label>
                    <?php if (!empty($settings['logo'])): ?>
                    <div class="current-image">
                        <img src="<?= asset('uploads/' . e($settings['logo'])) ?>" alt="Logo actual" style="max-height:80px;border-radius:4px">
                        <p class="form-hint">Logo actual</p>
                    </div>
                    <?php endif; ?>
                    <input type="file" name="logo" class="form-input" accept="image/*">
                    <p class="form-hint">PNG o SVG recomendado. Máx 5MB.</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Fondo de Pantalla del Login</label>
                    <?php if (!empty($settings['fondo_login'])): ?>
                    <div class="current-image">
                        <img src="<?= asset('uploads/' . e($settings['fondo_login'])) ?>" alt="Fondo actual"
                             style="max-height:80px;width:100%;object-fit:cover;border-radius:4px">
                    </div>
                    <?php endif; ?>
                    <input type="file" name="fondo_login" class="form-input" accept="image/*">
                    <p class="form-hint">Imagen JPG/PNG para el fondo del login.</p>
                </div>
            </div>
        </div>

        <!-- Configuración de Correo -->
        <div class="card form-card">
            <h3 class="form-section-title">📧 Configuración de Correo (SMTP)</h3>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Servidor SMTP</label>
                    <input type="text" name="smtp_host" class="form-input"
                           value="<?= e($settings['smtp_host'] ?? 'smtp.gmail.com') ?>" placeholder="smtp.gmail.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Puerto</label>
                    <input type="number" name="smtp_port" class="form-input"
                           value="<?= e($settings['smtp_port'] ?? '587') ?>" placeholder="587">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Usuario SMTP</label>
                    <input type="email" name="smtp_user" class="form-input"
                           value="<?= e($settings['smtp_user'] ?? '') ?>" placeholder="correo@gmail.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Contraseña SMTP</label>
                    <input type="password" name="smtp_pass" class="form-input"
                           value="<?= e($settings['smtp_pass'] ?? '') ?>" placeholder="contraseña o app password">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Correo Remitente</label>
                <input type="email" name="smtp_from" class="form-input"
                       value="<?= e($settings['smtp_from'] ?? 'noreply@fundacite.gob.ve') ?>">
            </div>
            <div class="info-box">
                ℹ Para Gmail usa una <strong>contraseña de aplicación</strong>, no tu contraseña normal. Activa la verificación en 2 pasos primero.
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-full">💾 Guardar Configuración</button>
        </div>
    </form>
</div>
