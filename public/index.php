<?php
// =====================================================
// FUNDACITE — Punto de Entrada Principal (Front Controller)
// =====================================================

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/app/core/Router.php';
require_once BASE_PATH . '/app/core/App.php';
require_once BASE_PATH . '/app/core/Controller.php';
require_once BASE_PATH . '/app/core/Model.php';

$app = new App();
$app->run();
