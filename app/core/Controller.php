<?php
// =====================================================
// FUNDACITE - Controlador Base
// =====================================================

class Controller {
    protected function view(string $view, array $data = []): void {
        extract($data);
        $viewFile = BASE_PATH . '/app/views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            die('Vista no encontrada: ' . e($view));
        }
        require $viewFile;
    }

    protected function layout(string $view, array $data = [], string $layout = 'main'): void {
        // Auto-inject current user so all layouts always have $user
        if (!isset($data['user'])) {
            $data['user'] = currentUser() ?? [];
        }
        extract($data);

        // Use a private-style variable name to avoid collision with view variables
        $__contentFile = BASE_PATH . '/app/views/' . str_replace('.', '/', $view) . '.php';
        $__layoutFile  = BASE_PATH . '/app/views/layouts/' . $layout . '.php';
        if (!file_exists($__contentFile)) die('Vista no encontrada: ' . e($view));
        if (!file_exists($__layoutFile))  die('Layout no encontrado: '  . e($layout));

        // $content is what the layout uses to require the inner view
        $content = $__contentFile;
        require $__layoutFile;
    }

    protected function json(mixed $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    protected function back(): void {
        $ref = $_SERVER['HTTP_REFERER'] ?? url('dashboard');
        header('Location: ' . $ref);
        exit;
    }
}
