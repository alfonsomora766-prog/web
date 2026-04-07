<?php
// =====================================================
// FUNDACITE - Bootstrap de Rutas
// =====================================================

class App {
    private Router $router;

    public function __construct() {
        $this->router = new Router();
        $this->registerRoutes();
    }

    private function registerRoutes(): void {
        $r = $this->router;

        // Auth
        $r->get('',               'AuthController',         'loginPage');
        $r->get('login',          'AuthController',         'loginPage');
        $r->post('login',         'AuthController',         'login');
        $r->get('logout',         'AuthController',         'logout');
        $r->get('set-password',   'AuthController',         'setPasswordPage');
        $r->post('set-password',  'AuthController',         'setPassword');

        // Dashboard
        $r->get('dashboard',      'DashboardController',    'index');

        // Actividades
        $r->get('activities',              'ActivityController', 'index');
        $r->get('activities/create',       'ActivityController', 'createPage');
        $r->post('activities/create',      'ActivityController', 'store');
        $r->get('activities/edit/{id}',    'ActivityController', 'editPage');
        $r->post('activities/edit/{id}',   'ActivityController', 'update');
        $r->get('activities/delete/{id}',  'ActivityController', 'delete');
        $r->get('activities/approve/{id}', 'ActivityController', 'approve');
        $r->get('activities/reject/{id}',  'ActivityController', 'reject');
        $r->get('activities/calendar',     'ActivityController', 'calendar');
        $r->get('activities/timer',        'ActivityController', 'timer');
        $r->get('activities/export',       'ActivityController', 'export');
        $r->get('activities/api/calendar', 'ActivityController', 'apiCalendar');

        // Usuarios
        $r->get('users',               'UserController', 'index');
        $r->get('users/create',        'UserController', 'createPage');
        $r->post('users/create',       'UserController', 'store');
        $r->get('users/edit/{id}',     'UserController', 'editPage');
        $r->post('users/edit/{id}',    'UserController', 'update');
        $r->get('users/delete/{id}',   'UserController', 'delete');
        $r->get('users/toggle/{id}',   'UserController', 'toggle');
        $r->get('profile',             'UserController', 'profilePage');
        $r->post('profile',            'UserController', 'updateProfile');

        // Notificaciones
        $r->get('notifications',         'NotificationController', 'index');
        $r->get('notifications/read/{id}','NotificationController','markRead');
        $r->get('notifications/all',     'NotificationController', 'markAllRead');
        $r->get('notifications/api',     'NotificationController', 'api');

        // Configuración
        $r->get('settings',    'SettingController', 'index');
        $r->post('settings',   'SettingController', 'update');

        // API datos tiempo real
        $r->get('api/timer',   'ActivityController', 'apiTimer');
        $r->get('api/stats',   'DashboardController', 'apiStats');
    }

    public function run(): void {
        $this->router->resolve();
    }
}
