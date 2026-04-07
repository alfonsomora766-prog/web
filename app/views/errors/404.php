<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Página no encontrada</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Sora',sans-serif;background:#0d1b2a;color:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh;text-align:center;padding:2rem}
        .err-code{font-size:8rem;font-weight:700;color:#1a6cad;line-height:1;letter-spacing:-4px}
        .err-title{font-size:1.5rem;margin:1rem 0 .5rem;color:#e2e8f0}
        .err-msg{color:#94a3b8;margin-bottom:2rem}
        .btn{display:inline-block;padding:.75rem 2rem;background:#1a6cad;color:#fff;border-radius:8px;text-decoration:none;font-weight:500;transition:.2s}
        .btn:hover{background:#1557a0}
        .grid{position:absolute;inset:0;background-image:linear-gradient(rgba(26,108,173,.1) 1px,transparent 1px),linear-gradient(90deg,rgba(26,108,173,.1) 1px,transparent 1px);background-size:40px 40px;pointer-events:none}
    </style>
</head>
<body>
    <div class="grid"></div>
    <div>
        <div class="err-code">404</div>
        <div class="err-title">Página no encontrada</div>
        <p class="err-msg">La ruta que buscas no existe en el sistema FUNDACITE.</p>
        <a href="<?= defined('BASE_URL') ? BASE_URL . '/dashboard' : '/dashboard' ?>" class="btn">← Ir al Dashboard</a>
    </div>
</body>
</html>
