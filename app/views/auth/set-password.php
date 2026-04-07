<form method="POST" action="<?= url('set-password') ?>" class="auth-form">
    <?= csrf() ?>
    <h2 class="auth-title">Crear Contraseña</h2>
    <p class="auth-subtitle">Es tu primer acceso. Define tu contraseña personal.</p>

    <div class="form-group">
        <label class="form-label">Nueva contraseña</label>
        <div class="input-wrapper">
            <input type="password" name="password" id="pass1" class="form-input" placeholder="Mínimo 8 caracteres" required>
            <button type="button" class="input-eye" onclick="togglePass('pass1',this)">👁</button>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Confirmar contraseña</label>
        <div class="input-wrapper">
            <input type="password" name="confirm" id="pass2" class="form-input" placeholder="Repite tu contraseña" required>
            <button type="button" class="input-eye" onclick="togglePass('pass2',this)">👁</button>
        </div>
    </div>

    <div class="password-strength" id="passStrength"></div>

    <button type="submit" class="btn btn-primary btn-full">Guardar y Entrar →</button>
</form>
<script>
document.getElementById('pass1').addEventListener('input', function() {
    const val = this.value;
    const el  = document.getElementById('passStrength');
    let strength = 0;
    if (val.length >= 8) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/[0-9]/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;
    const labels = ['', 'Débil', 'Regular', 'Buena', 'Fuerte'];
    const colors = ['', '#e74c3c', '#e67e22', '#f1c40f', '#2ecc71'];
    el.innerHTML = val ? `<div class="strength-bar"><div style="width:${strength*25}%;background:${colors[strength]};height:4px;border-radius:2px;transition:.3s"></div></div><span style="color:${colors[strength]};font-size:.75rem">${labels[strength]}</span>` : '';
});
</script>
