<?php
$totals    = $stats['totals'];
$byMonth   = $stats['byMonth'];
$upcoming  = $stats['upcoming'];
$byUser    = $stats['byUser'];
$pct = $totals['total'] > 0 ? round(($totals['realizadas'] / $totals['total']) * 100) : 0;
?>
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Bienvenido/a, <?= e($user['nombre']) ?> — <?= date('l, d \d\e F \d\e Y') ?></p>
</div>

<!-- KPI Cards -->
<div class="kpi-grid">
    <div class="kpi-card kpi-total">
        <div class="kpi-icon">◈</div>
        <div class="kpi-value"><?= $totals['total'] ?></div>
        <div class="kpi-label">Total Actividades</div>
    </div>
    <div class="kpi-card kpi-pending">
        <div class="kpi-icon">◉</div>
        <div class="kpi-value"><?= $totals['pendientes'] ?></div>
        <div class="kpi-label">Por Realizar</div>
    </div>
    <div class="kpi-card kpi-done">
        <div class="kpi-icon">✓</div>
        <div class="kpi-value"><?= $totals['realizadas'] ?></div>
        <div class="kpi-label">Realizadas</div>
    </div>
    <div class="kpi-card kpi-failed">
        <div class="kpi-icon">✕</div>
        <div class="kpi-value"><?= $totals['no_realizadas'] ?></div>
        <div class="kpi-label">No Realizadas</div>
    </div>
    <?php if (hasRole('admin')): ?>
    <div class="kpi-card kpi-users">
        <div class="kpi-icon">◎</div>
        <div class="kpi-value"><?= $totalUsers ?></div>
        <div class="kpi-label">Usuarios</div>
    </div>
    <?php endif; ?>
    <div class="kpi-card kpi-approve">
        <div class="kpi-icon">⏳</div>
        <div class="kpi-value"><?= $totals['por_aprobar'] ?></div>
        <div class="kpi-label">Por Aprobar</div>
    </div>
</div>

<!-- Cumplimiento -->
<div class="section-grid">
    <div class="card">
        <div class="card-header">
            <h3>Cumplimiento General</h3>
            <span class="badge badge-<?= $pct >= 70 ? 'success' : ($pct >= 40 ? 'warning' : 'danger') ?>"><?= $pct ?>%</span>
        </div>
        <div class="progress-big">
            <div class="progress-fill" style="width:<?= $pct ?>%" data-pct="<?= $pct ?>"></div>
        </div>
        <div class="progress-labels">
            <span>0%</span><span>50%</span><span>100%</span>
        </div>

        <!-- Pie Chart Canvas -->
        <canvas id="pieChart" height="200" style="margin-top:20px"></canvas>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Actividades por Mes</h3>
        </div>
        <canvas id="barChart" height="250"></canvas>
    </div>
</div>

<!-- Próximas a vencer -->
<div class="card" style="margin-top:1.5rem">
    <div class="card-header">
        <h3>⏰ Próximas a Vencer</h3>
        <a href="<?= url('activities') ?>" class="btn btn-sm btn-outline">Ver todas</a>
    </div>
    <?php if (empty($upcoming)): ?>
        <p class="empty-state">No hay actividades próximas a vencer.</p>
    <?php else: ?>
    <div class="table-wrapper">
        <table class="table">
            <thead><tr><th>Actividad</th><th>Responsable</th><th>Fecha Límite</th><th>Tiempo Restante</th></tr></thead>
            <tbody>
            <?php foreach ($upcoming as $act):
                $tl = timeLeft($act['fecha_limite']); ?>
            <tr>
                <td><?= e($act['nombre']) ?></td>
                <td><?= e($act['creador']) ?></td>
                <td><?= formatDateTime($act['fecha_limite']) ?></td>
                <td>
                    <?php if ($tl['expired']): ?>
                        <span class="badge badge-danger">Vencida</span>
                    <?php elseif ($tl['days'] <= 2): ?>
                        <span class="badge badge-warning"><?= $tl['text'] ?></span>
                    <?php else: ?>
                        <span class="badge badge-info"><?= $tl['text'] ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php if (hasRole('admin') && !empty($byUser)): ?>
<div class="card" style="margin-top:1.5rem">
    <div class="card-header"><h3>📊 Actividades por Usuario</h3></div>
    <canvas id="userChart" height="200"></canvas>
</div>
<?php endif; ?>

<script>
const byMonthData = <?= json_encode(array_reverse($byMonth)) ?>;
const byUserData  = <?= json_encode($byUser) ?>;
const totals = {
    realizadas:    <?= (int)$totals['realizadas'] ?>,
    pendientes:    <?= (int)$totals['pendientes'] ?>,
    no_realizadas: <?= (int)$totals['no_realizadas'] ?>,
    por_aprobar:   <?= (int)$totals['por_aprobar'] ?>
};

// Animate progress bar
document.querySelectorAll('.progress-fill').forEach(el => {
    const pct = el.dataset.pct;
    el.style.width = '0';
    setTimeout(() => el.style.width = pct + '%', 200);
});

window.addEventListener('load', () => {
    initCharts(byMonthData, byUserData, totals);
});
</script>
