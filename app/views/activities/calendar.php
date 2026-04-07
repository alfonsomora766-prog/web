<?php
$prevMonth = $month == 1 ? 12 : $month - 1;
$prevYear  = $month == 1 ? $year - 1 : $year;
$nextMonth = $month == 12 ? 1 : $month + 1;
$nextYear  = $month == 12 ? $year + 1 : $year;
$firstDay  = (int)date('w', mktime(0,0,0,$month,1,$year));
$daysInMonth = (int)date('t', mktime(0,0,0,$month,1,$year));

// Index activities by date
$actByDate = [];
foreach ($activities as $act) {
    $d = date('Y-m-d', strtotime($act['fecha_limite']));
    $actByDate[$d][] = $act;
    $d2 = date('Y-m-d', strtotime($act['fecha_inicio']));
    if ($d2 !== $d) $actByDate[$d2][] = $act;
}

$monthNames = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Calendario</h1>
        <p class="page-subtitle"><?= $monthNames[$month] ?> <?= $year ?></p>
    </div>
    <div class="page-actions">
        <a href="?year=<?= $prevYear ?>&month=<?= $prevMonth ?>" class="btn btn-outline">‹ Anterior</a>
        <a href="?year=<?= date('Y') ?>&month=<?= date('m') ?>" class="btn btn-outline">Hoy</a>
        <a href="?year=<?= $nextYear ?>&month=<?= $nextMonth ?>" class="btn btn-outline">Siguiente ›</a>
    </div>
</div>

<div class="calendar-grid">
    <div class="cal-days-header">
        <?php foreach (['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'] as $d): ?>
        <div class="cal-day-name"><?= $d ?></div>
        <?php endforeach; ?>
    </div>
    <div class="cal-body">
        <?php
        // Empty cells
        for ($i = 0; $i < $firstDay; $i++) echo '<div class="cal-cell cal-empty"></div>';
        // Days
        for ($day = 1; $day <= $daysInMonth; $day++):
            $dateStr = sprintf('%d-%02d-%02d', $year, $month, $day);
            $isToday = $dateStr === date('Y-m-d');
            $hasActs = !empty($actByDate[$dateStr]);
        ?>
        <div class="cal-cell <?= $isToday ? 'cal-today' : '' ?> <?= $hasActs ? 'cal-has-events' : '' ?>"
             <?= $hasActs ? 'onclick="showDayActivities(\'' . $dateStr . '\')"' : '' ?>>
            <span class="cal-day-num"><?= $day ?></span>
            <?php if ($hasActs): ?>
            <div class="cal-events">
                <?php foreach (array_slice($actByDate[$dateStr], 0, 3) as $act): ?>
                <div class="cal-event cal-event-<?= $act['estado'] ?>"
                     title="<?= e($act['nombre']) ?>">
                    <?= e(substr($act['nombre'], 0, 20)) ?>
                </div>
                <?php endforeach; ?>
                <?php if (count($actByDate[$dateStr]) > 3): ?>
                <div class="cal-more">+<?= count($actByDate[$dateStr]) - 3 ?> más</div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endfor; ?>
    </div>
</div>

<!-- Modal detalle -->
<div class="modal" id="dayModal">
    <div class="modal-overlay" onclick="closeModal()"></div>
    <div class="modal-card">
        <div class="modal-header">
            <h3 id="modalDate">Actividades del día</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
    </div>
</div>

<script>
const actByDate = <?= json_encode($actByDate) ?>;

function showDayActivities(dateStr) {
    const acts = actByDate[dateStr] || [];
    const date = new Date(dateStr + 'T12:00:00');
    document.getElementById('modalDate').textContent =
        date.toLocaleDateString('es-VE', {weekday:'long', year:'numeric', month:'long', day:'numeric'});

    const statusMap = {
        'pendiente':'🟡 Por Realizar','realizada':'🟢 Realizada',
        'no_realizada':'🔴 No Realizada','por_aprobar':'⏳ Por Aprobar'
    };

    document.getElementById('modalBody').innerHTML = acts.map(a => `
        <div class="modal-act">
            <div class="modal-act-header">
                <strong>${a.nombre}</strong>
                <span class="badge badge-${a.estado}">${statusMap[a.estado]||a.estado}</span>
            </div>
            ${a.descripcion ? `<p class="modal-act-desc">${a.descripcion}</p>` : ''}
            ${a.requisitos  ? `<div class="modal-act-req"><strong>Requisitos:</strong> ${a.requisitos}</div>` : ''}
            <div class="modal-act-dates">
                📅 Inicio: ${a.fecha_inicio} &nbsp;|&nbsp; ⏰ Límite: ${a.fecha_limite}
            </div>
        </div>
    `).join('') || '<p class="empty-state">Sin actividades</p>';

    document.getElementById('dayModal').classList.add('open');
}
function closeModal() {
    document.getElementById('dayModal').classList.remove('open');
}
</script>
