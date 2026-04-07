<?php
// =====================================================
// FUNDACITE - Router (Front Controller)
// =====================================================

class Router {
    private array $routes = [];

    public function get(string $uri, string $controller, string $method): void {
        $this->routes['GET'][$uri] = compact('controller', 'method');
    }

    public function post(string $uri, string $controller, string $method): void {
        $this->routes['POST'][$uri] = compact('controller', 'method');
    }

    public function resolve(): void {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $rawUri     = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $rawUri     = rawurldecode($rawUri);
        $basePath   = BASE_URL_PATH;
        $uri        = trim(substr($rawUri, strlen($basePath)), '/');
        // Remove /public prefix if accessed directly
        $uri        = preg_replace('#^public/?#', '', $uri);

        // Match exact route
        if (isset($this->routes[$httpMethod][$uri])) {
            $this->dispatch($this->routes[$httpMethod][$uri]);
            return;
        }

        // Match parametrized routes  e.g. activities/edit/5
        foreach ($this->routes[$httpMethod] as $pattern => $handler) {
            $regex = preg_replace('#\{[^/]+\}#', '([^/]+)', $pattern);
            if (preg_match('#^' . $regex . '$#', $uri, $matches)) {
                array_shift($matches);
                $this->dispatch($handler, $matches);
                return;
            }
        }

        // 404
        http_response_code(404);
        if (file_exists(BASE_PATH . '/app/views/errors/404.php')) {
            require BASE_PATH . '/app/views/errors/404.php';
        } else {
            echo '<h1>404 - Página no encontrada</h1>';
        }
    }

    private function dispatch(array $handler, array $params = []): void {
        $controllerFile = BASE_PATH . '/app/controllers/' . $handler['controller'] . '.php';
        if (!file_exists($controllerFile)) {
            http_response_code(500);
            die('Controller no encontrado: ' . e($handler['controller']));
        }
        require_once $controllerFile;
        $ctrl = new $handler['controller']();
        call_user_func_array([$ctrl, $handler['method']], $params);
    }
}
