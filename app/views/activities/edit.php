<div class="page-header">
    <div>
        <h1 class="page-title">Editar Actividad</h1>
        <p class="page-subtitle"><?= e($activity['nombre']) ?></p>
    </div>
    <a href="<?= url('activities') ?>" class="btn btn-outline">← Volver</a>
</div>

<div class="card form-card">
    <form method="POST" action="<?= url('activities/edit/' . $activity['id']) ?>">
        <?= csrf() ?>

        <div class="form-row">
            <div class="form-group form-col-full">
                <label class="form-label">Nombre de la Actividad *</label>
                <input type="text" name="nombre" class="form-input"
                       value="<?= e($activity['nombre']) ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Fecha de Inicio *</label>
                <input type="date" name="fecha_inicio" class="form-input"
                       value="<?= e($activity['fecha_inicio']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Fecha Límite *</label>
                <input type="datetime-local" name="fecha_limite" class="form-input"
                       value="<?= e(str_replace(' ', 'T', $activity['fecha_limite'])) ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-textarea" rows="4"><?= e($activity['descripcion']) ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Requisitos</label>
            <textarea name="requisitos" class="form-textarea" rows="4"><?= e($activity['requisitos']) ?></textarea>
        </div>

        <?php if (hasRole('admin')): ?>
        <div class="form-group">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
                <option value="pendiente"    <?= $activity['estado']==='pendiente'    ? 'selected':'' ?>>🟡 Por Realizar</option>
                <option value="realizada"    <?= $activity['estado']==='realizada'    ? 'selected':'' ?>>🟢 Realizada</option>
                <option value="no_realizada" <?= $activity['estado']==='no_realizada' ? 'selected':'' ?>>🔴 No Realizada</option>
                <option value="por_aprobar"  <?= $activity['estado']==='por_aprobar'  ? 'selected':'' ?>>⏳ Por Aprobar</option>
            </select>
        </div>
        <?php endif; ?>

        <div class="form-actions">
            <a href="<?= url('activities') ?>" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>
