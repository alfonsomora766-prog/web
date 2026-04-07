<?php
require_once BASE_PATH . '/app/core/Controller.php';
require_once BASE_PATH . '/app/models/ActivityModel.php';
require_once BASE_PATH . '/app/models/UserModel.php';
require_once BASE_PATH . '/app/models/NotificationModel.php';
require_once BASE_PATH . '/app/models/SettingModel.php';

class DashboardController extends Controller {

    public function index(): void {
        requireAuth();
        $user      = currentUser();
        $actModel  = new ActivityModel();
        $userModel = new UserModel();
        $notifModel= new NotificationModel();

        // Marcar actividades vencidas
        $actModel->markExpired();

        $userId = hasRole('admin') ? null : $user['id'];
        $stats  = $actModel->getStats($userId);
        $unread = $notifModel->countUnread($user['id']);
        $settings = (new SettingModel())->allSettings();

        $totalUsers = hasRole('admin') ? $userModel->count() : null;
        $usersByRole = hasRole('admin') ? $userModel->countByRole() : [];

        $this->layout('dashboard/index', compact(
            'stats', 'unread', 'settings', 'totalUsers', 'usersByRole', 'user'
        ));
    }

    public function apiStats(): void {
        requireAuth();
        $user     = currentUser();
        $actModel = new ActivityModel();
        $userId   = hasRole('admin') ? null : $user['id'];
        $this->json($actModel->getStats($userId));
    }
}
