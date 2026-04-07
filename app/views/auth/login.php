<form method="POST" action="<?= url('login') ?>" class="auth-form">
    <?= csrf() ?>
    <h2 class="auth-title">Iniciar Sesión</h2>
    <p class="auth-subtitle">Accede con tu correo institucional</p>

    <div class="form-group">
        <label class="form-label">Correo electrónico</label>
        <input type="email" name="email" class="form-input" placeholder="usuario@fundacite.gob.ve"
               value="<?= e($_POST['email'] ?? '') ?>" required autofocus>
    </div>

    <div class="form-group">
        <label class="form-label">Contraseña</label>
        <div class="input-wrapper">
            <input type="password" name="password" id="passInput" class="form-input" placeholder="••••••••">
            <button type="button" class="input-eye" onclick="togglePass('passInput',this)">👁</button>
        </div>
        <p class="form-hint">Si es tu primer acceso, deja la contraseña vacía.</p>
    </div>

    <button type="submit" class="btn btn-primary btn-full">
        Entrar al sistema →
    </button>
</form>
