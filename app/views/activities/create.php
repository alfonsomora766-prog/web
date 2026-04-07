<div class="page-header">
    <div>
        <h1 class="page-title">Nueva Actividad</h1>
        <p class="page-subtitle">Completa los datos de la actividad</p>
    </div>
    <a href="<?= url('activities') ?>" class="btn btn-outline">← Volver</a>
</div>

<div class="card form-card">
    <form method="POST" action="<?= url('activities/create') ?>">
        <?= csrf() ?>

        <div class="form-row">
            <div class="form-group form-col-full">
                <label class="form-label">Nombre de la Actividad *</label>
                <input type="text" name="nombre" class="form-input" placeholder="Ej: Reunión de planificación Q1"
                       value="<?= e($_POST['nombre'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Fecha de Inicio *</label>
                <input type="date" name="fecha_inicio" class="form-input"
                       value="<?= e($_POST['fecha_inicio'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Fecha Límite / Entrega *</label>
                <input type="datetime-local" name="fecha_limite" class="form-input"
                       value="<?= e($_POST['fecha_limite'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-textarea" rows="4"
                      placeholder="Describe el objetivo y alcance de la actividad..."><?= e($_POST['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Requisitos a Cumplir</label>
            <textarea name="requisitos" class="form-textarea" rows="4"
                      placeholder="Lista los requisitos necesarios para completar esta actividad..."><?= e($_POST['requisitos'] ?? '') ?></textarea>
        </div>

        <?php if (hasRole('admin')): ?>
        <div class="form-group">
            <label class="form-label">Estado Inicial</label>
            <select name="estado" class="form-select">
                <option value="pendiente">🟡 Por Realizar</option>
                <option value="realizada">🟢 Realizada</option>
            </select>
        </div>
        <?php else: ?>
        <div class="info-box">
            ℹ Esta actividad quedará pendiente de aprobación por un administrador.
        </div>
        <?php endif; ?>

        <div class="form-actions">
            <a href="<?= url('activities') ?>" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">Crear Actividad</button>
        </div>
    </form>
</div>
