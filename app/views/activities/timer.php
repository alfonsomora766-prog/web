<div class="page-header">
    <div>
        <h1 class="page-title">◷ Cronómetros en Vivo</h1>
        <p class="page-subtitle">Control de cumplimiento en tiempo real</p>
    </div>
    <div class="page-actions">
        <button onclick="refreshTimers()" class="btn btn-outline">↻ Actualizar</button>
    </div>
</div>

<?php
// Group by day
$byDay = [];
foreach ($activities as $act) {
    $day = date('Y-m-d', strtotime($act['fecha_limite']));
    $byDay[$day][] = $act;
}
ksort($byDay);
$dayNames = ['Sunday'=>'Domingo','Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miércoles',
             'Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sábado'];
$monthNames = ['01'=>'ene','02'=>'feb','03'=>'mar','04'=>'abr','05'=>'may','06'=>'jun',
               '07'=>'jul','08'=>'ago','09'=>'sep','10'=>'oct','11'=>'nov','12'=>'dic'];
?>

<?php if (empty($activities)): ?>
<div class="empty-state-big">
    <div class="empty-icon">◷</div>
    <p>No hay actividades activas con cuenta regresiva.</p>
    <?php if (hasRole('admin','operativo')): ?>
    <a href="<?= url('activities/create') ?>" class="btn btn-primary">+ Crear Actividad</a>
    <?php endif; ?>
</div>
<?php else: ?>

<?php foreach ($byDay as $day => $acts):
    $dt      = new DateTime($day);
    $dayName = $dayNames[$dt->format('l')] ?? $dt->format('l');
    $isToday = $day === date('Y-m-d');
    $isPast  = $day < date('Y-m-d');
?>
<div class="timer-day-section <?= $isPast ? 'timer-day-past' : ($isToday ? 'timer-day-today' : '') ?>">
    <div class="timer-day-header">
        <span class="timer-day-name"><?= $isToday ? '📍 Hoy — ' : '' ?><?= $dayName ?></span>
        <span class="timer-day-date"><?= $dt->format('d') ?> de <?= $monthNames[$dt->format('m')] ?> <?= $dt->format('Y') ?></span>
        <span class="timer-day-count"><?= count($acts) ?> actividad<?= count($acts) > 1 ? 'es' : '' ?></span>
    </div>

    <div class="timer-cards-grid">
    <?php foreach ($acts as $act):
        $tl      = timeLeft($act['fecha_limite']);
        $expired = $tl['expired'];
    ?>
    <div class="timer-card <?= $expired ? 'timer-expired' : (($tl['days'] ?? 99) <= 1 ? 'timer-urgent' : '') ?>"
         data-deadline="<?= e($act['fecha_limite']) ?>"
         data-id="<?= $act['id'] ?>">
        <div class="timer-card-header">
            <div class="timer-status-dot <?= $expired ? 'dot-red' : (($tl['days'] ?? 99) <= 1 ? 'dot-orange' : 'dot-green') ?>"></div>
            <span class="timer-card-title"><?= e($act['nombre']) ?></span>
        </div>
        <?php if ($act['descripcion']): ?>
        <p class="timer-card-desc"><?= e(substr($act['descripcion'], 0, 100)) ?></p>
        <?php endif; ?>

        <!-- Countdown Display -->
        <?php if ($expired): ?>
        <div class="countdown-expired">
            <div class="countdown-label">⛔ VENCIDA</div>
        </div>
        <?php else: ?>
        <div class="countdown" id="cd-<?= $act['id'] ?>">
            <div class="cd-unit">
                <span class="cd-num" id="cd-d-<?= $act['id'] ?>"><?= str_pad($tl['days'] ?? 0, 2, '0', STR_PAD_LEFT) ?></span>
                <span class="cd-label">días</span>
            </div>
            <div class="cd-sep">:</div>
            <div class="cd-unit">
                <span class="cd-num" id="cd-h-<?= $act['id'] ?>"><?= str_pad($tl['hours'] ?? 0, 2, '0', STR_PAD_LEFT) ?></span>
                <span class="cd-label">horas</span>
            </div>
            <div class="cd-sep">:</div>
            <div class="cd-unit">
                <span class="cd-num" id="cd-m-<?= $act['id'] ?>"><?= str_pad($tl['minutes'] ?? 0, 2, '0', STR_PAD_LEFT) ?></span>
                <span class="cd-label">min</span>
            </div>
            <div class="cd-sep">:</div>
            <div class="cd-unit">
                <span class="cd-num" id="cd-s-<?= $act['id'] ?>"><?= str_pad($tl['seconds'] ?? 0, 2, '0', STR_PAD_LEFT) ?></span>
                <span class="cd-label">seg</span>
            </div>
        </div>
        <!-- Progress bar temporal -->
        <?php
            $totalTime = strtotime($act['fecha_limite']) - strtotime($act['fecha_inicio']);
            $elapsed   = time() - strtotime($act['fecha_inicio']);
            $pct       = $totalTime > 0 ? min(100, max(0, round($elapsed / $totalTime * 100))) : 0;
        ?>
        <div class="timer-progress">
            <div class="timer-progress-fill <?= $pct > 80 ? 'fill-danger' : ($pct > 60 ? 'fill-warning' : 'fill-ok') ?>"
                 style="width:<?= $pct ?>%"></div>
        </div>
        <div class="timer-progress-labels">
            <span>Inicio: <?= formatDate($act['fecha_inicio']) ?></span>
            <span><?= $pct ?>% transcurrido</span>
            <span>Límite: <?= formatDateTime($act['fecha_limite']) ?></span>
        </div>
        <?php endif; ?>

        <div class="timer-card-footer">
            <span class="timer-resp">👤 <?= e($act['creador_nombre']) ?></span>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>

<script>
// Countdown live update
const countdowns = <?= json_encode(array_map(fn($a) => [
    'id'       => $a['id'],
    'deadline' => $a['fecha_limite'],
], $activities)) ?>;

function updateCountdowns() {
    const now = new Date();
    countdowns.forEach(item => {
        const end  = new Date(item.deadline);
        const diff = end - now;
        if (diff <= 0) {
            const el = document.getElementById('cd-' + item.id);
            if (el) {
                el.innerHTML = '<div class="countdown-expired"><div class="countdown-label">⛔ VENCIDA</div></div>';
                el.closest('.timer-card').classList.add('timer-expired');
            }
            return;
        }
        const days  = Math.floor(diff / 86400000);
        const hours = Math.floor((diff % 86400000) / 3600000);
        const mins  = Math.floor((diff % 3600000)  / 60000);
        const secs  = Math.floor((diff % 60000)    / 1000);

        const pad = n => String(n).padStart(2, '0');
        const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
        set('cd-d-' + item.id, pad(days));
        set('cd-h-' + item.id, pad(hours));
        set('cd-m-' + item.id, pad(mins));
        set('cd-s-' + item.id, pad(secs));
    });
}

setInterval(updateCountdowns, 1000);
updateCountdowns();

function refreshTimers() {
    window.location.reload();
}
</script>
<?php endif; ?>
