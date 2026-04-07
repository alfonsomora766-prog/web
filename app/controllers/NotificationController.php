<?php
require_once BASE_PATH . '/app/core/Controller.php';
require_once BASE_PATH . '/app/models/NotificationModel.php';
require_once BASE_PATH . '/app/models/SettingModel.php';

class NotificationController extends Controller {

    public function index(): void {
        requireAuth();
        $user         = currentUser();
        $notifications = (new NotificationModel())->forUser($user['id']);
        $settings     = (new SettingModel())->allSettings();
        $this->layout('notifications/index', compact('notifications', 'settings'));
    }

    public function markRead(string $id): void {
        requireAuth();
        $user = currentUser();
        (new NotificationModel())->markRead((int)$id, $user['id']);
        $ref = $_GET['redirect'] ?? url('notifications');
        header('Location: ' . $ref);
        exit;
    }

    public function markAllRead(): void {
        requireAuth();
        $user = currentUser();
        (new NotificationModel())->markAllRead($user['id']);
        redirect('notifications');
    }

    public function api(): void {
        requireAuth();
        $user  = currentUser();
        $notifModel = new NotificationModel();
        $this->json([
            'unread'        => $notifModel->countUnread($user['id']),
            'notifications' => $notifModel->forUser($user['id'], true),
        ]);
    }
}
