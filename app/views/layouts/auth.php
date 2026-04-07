<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($settings['nombre_sistema'] ?? APP_NAME) ?> — Acceso</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="auth-body" <?php if (!empty($settings['fondo_login'])): ?>
    style="background-image:url('<?= asset('uploads/' . e($settings['fondo_login'])) ?>');background-size:cover;background-position:center"
<?php endif; ?>>

<div class="auth-overlay"></div>

<div class="auth-container">
    <div class="auth-card">
        <?php if (!empty($settings['logo'])): ?>
            <img src="<?= asset('uploads/' . e($settings['logo'])) ?>" alt="Logo" class="auth-logo">
        <?php else: ?>
            <div class="auth-brand">
                <div class="auth-brand-icon">⚙</div>
                <div class="auth-brand-name"><?= e($settings['nombre_sistema'] ?? 'FUNDACITE Carabobo') ?></div>
                <div class="auth-brand-sub">Sistema de Gestión de Actividades</div>
            </div>
        <?php endif; ?>

        <?php if ($flash): ?>
        <div class="flash flash-<?= e($flash['type']) ?>">
            <?= e($flash['message']) ?>
        </div>
        <?php endif; ?>

        <?php require $content; ?>
    </div>
</div>

<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
