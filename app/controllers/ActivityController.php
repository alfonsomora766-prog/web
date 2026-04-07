<?php
require_once BASE_PATH . '/app/core/Controller.php';
require_once BASE_PATH . '/app/models/ActivityModel.php';
require_once BASE_PATH . '/app/models/UserModel.php';
require_once BASE_PATH . '/app/models/NotificationModel.php';
require_once BASE_PATH . '/app/models/SettingModel.php';
require_once BASE_PATH . '/config/mail.php';

class ActivityController extends Controller {

    private ActivityModel    $actModel;
    private NotificationModel $notifModel;
    private UserModel        $userModel;

    public function __construct() {
        $this->actModel   = new ActivityModel();
        $this->notifModel = new NotificationModel();
        $this->userModel  = new UserModel();
    }

    public function index(): void {
        requireAuth();
        $user     = currentUser();
        $page     = max(1, (int)($_GET['page'] ?? 1));
        $perPage  = 15;

        $filters = [
            'estado'      => $_GET['estado']      ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'search'      => $_GET['q']            ?? '',
        ];

        // Operativo solo ve sus actividades
        if (hasRole('operativo')) {
            $filters['usuario_id'] = $user['id'];
        }

        $total      = $this->actModel->countFiltered($filters);
        $pagination = paginate($total, $perPage, $page);
        $activities = $this->actModel->allWithUsers($filters, $pagination['limit'], $pagination['offset']);
        $settings   = (new SettingModel())->allSettings();
        $users      = hasRole('admin') ? $this->userModel->allWithRoles() : [];

        $this->layout('activities/index', compact('activities', 'pagination', 'filters', 'settings', 'users'));
    }

    public function createPage(): void {
        requireRole('admin', 'operativo');
        $settings = (new SettingModel())->allSettings();
        $this->layout('activities/create', compact('settings'));
    }

    public function store(): void {
        requireRole('admin', 'operativo');
        if (!verifyCsrf()) { flash('danger', 'Token inválido.'); redirect('activities/create'); return; }

        $user      = currentUser();
        $isAdmin   = hasRole('admin');

        $data = [
            'nombre'       => trim($_POST['nombre'] ?? ''),
            'descripcion'  => trim($_POST['descripcion'] ?? ''),
            'requisitos'   => trim($_POST['requisitos'] ?? ''),
            'fecha_inicio' => $_POST['fecha_inicio'] ?? '',
            'fecha_limite' => $_POST['fecha_limite'] ?? '',
            'estado'       => $isAdmin ? 'pendiente' : 'por_aprobar',
            'aprobada'     => $isAdmin ? 1 : 0,
            'creado_por'   => $user['id'],
        ];

        if (empty($data['nombre']) || empty($data['fecha_inicio']) || empty($data['fecha_limite'])) {
            flash('danger', 'Nombre, fecha de inicio y fecha límite son obligatorios.');
            redirect('activities/create');
            return;
        }

        $id = $this->actModel->create($data);

        // Notificaciones
        if (!$isAdmin) {
            $admins   = $this->userModel->getAdmins();
            $adminIds = $this->userModel->getAdminIds();

            $this->notifModel->notifyAll(
                $adminIds,
                '📋 Nueva actividad por aprobar',
                "La actividad \"{$data['nombre']}\" requiere tu aprobación.",
                'warning',
                url('activities')
            );

            $mailer  = new Mailer();
            $actData = $this->actModel->find($id);
            $mailer->sendActivityAlert($admins, $actData ?? $data, 'aprobacion');

            flash('success', 'Actividad enviada para aprobación.');
        } else {
            $allIds = $this->userModel->getAllActiveIds();
            $this->notifModel->notifyAll(
                $allIds,
                '📋 Nueva actividad creada',
                "Se ha creado la actividad \"{$data['nombre']}\".",
                'info',
                url('activities')
            );
            flash('success', 'Actividad creada exitosamente.');
        }

        redirect('activities');
    }

    public function editPage(string $id): void {
        requireRole('admin', 'operativo');
        $activity = $this->actModel->find((int)$id);
        if (!$activity) { flash('danger', 'Actividad no encontrada.'); redirect('activities'); return; }

        $user = currentUser();
        if (hasRole('operativo') && $activity['creado_por'] != $user['id']) {
            flash('danger', 'No tienes permiso para editar esta actividad.');
            redirect('activities');
            return;
        }

        $settings = (new SettingModel())->allSettings();
        $this->layout('activities/edit', compact('activity', 'settings'));
    }

    public function update(string $id): void {
        requireRole('admin', 'operativo');
        if (!verifyCsrf()) { flash('danger', 'Token inválido.'); redirect("activities/edit/$id"); return; }

        $activity = $this->actModel->find((int)$id);
        if (!$activity) { redirect('activities'); return; }

        $user = currentUser();
        if (hasRole('operativo') && $activity['creado_por'] != $user['id']) {
            redirect('activities');
            return;
        }

        $this->actModel->update((int)$id, [
            'nombre'       => trim($_POST['nombre'] ?? ''),
            'descripcion'  => trim($_POST['descripcion'] ?? ''),
            'requisitos'   => trim($_POST['requisitos'] ?? ''),
            'fecha_inicio' => $_POST['fecha_inicio'] ?? '',
            'fecha_limite' => $_POST['fecha_limite'] ?? '',
            'estado'       => $_POST['estado'] ?? $activity['estado'],
        ]);

        flash('success', 'Actividad actualizada.');
        redirect('activities');
    }

    public function delete(string $id): void {
        requireRole('admin');
        $this->actModel->delete((int)$id);
        flash('success', 'Actividad eliminada.');
        redirect('activities');
    }

    public function approve(string $id): void {
        requireRole('admin');
        $user     = currentUser();
        $activity = $this->actModel->find((int)$id);
        if ($activity) {
            $this->actModel->approve((int)$id, $user['id']);

            // Notificar al creador
            $this->notifModel->create(
                $activity['creado_por'],
                '✅ Actividad aprobada',
                "Tu actividad \"{$activity['nombre']}\" fue aprobada.",
                'success',
                url('activities')
            );
            flash('success', 'Actividad aprobada.');
        }
        redirect('activities');
    }

    public function reject(string $id): void {
        requireRole('admin');
        $activity = $this->actModel->find((int)$id);
        if ($activity) {
            $this->actModel->reject((int)$id);
            $this->notifModel->create(
                $activity['creado_por'],
                '❌ Actividad rechazada',
                "Tu actividad \"{$activity['nombre']}\" fue rechazada.",
                'danger',
                url('activities')
            );
            flash('info', 'Actividad rechazada.');
        }
        redirect('activities');
    }

    public function calendar(): void {
        requireAuth();
        $year  = (int)($_GET['year']  ?? date('Y'));
        $month = (int)($_GET['month'] ?? date('m'));
        $activities = $this->actModel->forCalendar($year, $month);
        $settings   = (new SettingModel())->allSettings();
        $this->layout('activities/calendar', compact('activities', 'year', 'month', 'settings'));
    }

    public function apiCalendar(): void {
        requireAuth();
        $year  = (int)($_GET['year']  ?? date('Y'));
        $month = (int)($_GET['month'] ?? date('m'));
        $this->json($this->actModel->forCalendar($year, $month));
    }

    public function timer(): void {
        requireAuth();
        $this->actModel->markExpired();
        $activities = $this->actModel->forTimer();
        $settings   = (new SettingModel())->allSettings();
        $this->layout('activities/timer', compact('activities', 'settings'));
    }

    public function apiTimer(): void {
        requireAuth();
        $this->actModel->markExpired();
        $activities = $this->actModel->forTimer();
        $result = array_map(function($a) {
            $tl = timeLeft($a['fecha_limite']);
            $a['time_left']   = $tl;
            $a['is_expired']  = $tl['expired'];
            return $a;
        }, $activities);
        $this->json($result);
    }

    public function export(): void {
        requireRole('admin', 'operativo');

        $filters = [
            'estado'      => $_GET['estado']      ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
        ];
        if (hasRole('operativo')) {
            $user = currentUser();
            $filters['usuario_id'] = $user['id'];
        }

        $activities = $this->actModel->allWithUsers($filters, 1000, 0);

        // Export CSV/Excel compatible
        $filename = 'actividades_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');
        // BOM para Excel UTF-8
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($out, ['Nombre', 'Descripción', 'Requisitos', 'Fecha Inicio', 'Fecha Límite', 'Estado', 'Responsable'], ';');

        $statusMap = [
            'pendiente'    => 'Por Realizar',
            'realizada'    => 'Realizada',
            'no_realizada' => 'No Realizada',
            'por_aprobar'  => 'Por Aprobar',
        ];

        foreach ($activities as $a) {
            fputcsv($out, [
                $a['nombre'],
                $a['descripcion'],
                $a['requisitos'],
                $a['fecha_inicio'],
                $a['fecha_limite'],
                $statusMap[$a['estado']] ?? $a['estado'],
                $a['creador_nombre'],
            ], ';');
        }

        fclose($out);
        exit;
    }
}
