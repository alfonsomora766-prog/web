<?php
// =====================================================
// FUNDACITE - Servicio de Correo (PHPMailer nativo)
// =====================================================

class Mailer {
    private string $host;
    private int $port;
    private string $user;
    private string $pass;
    private string $from;
    private string $fromName;

    public function __construct() {
        $db = Database::getConnection();
        $configs = $db->query("SELECT clave, valor FROM configuraciones WHERE clave LIKE 'smtp%' OR clave = 'nombre_sistema'")->fetchAll();
        $cfg = array_column($configs, 'valor', 'clave');

        $this->host     = $cfg['smtp_host'] ?? 'smtp.gmail.com';
        $this->port     = (int)($cfg['smtp_port'] ?? 587);
        $this->user     = $cfg['smtp_user'] ?? '';
        $this->pass     = $cfg['smtp_pass'] ?? '';
        $this->from     = $cfg['smtp_from'] ?? 'noreply@fundacite.gob.ve';
        $this->fromName = $cfg['nombre_sistema'] ?? APP_NAME;
    }

    public function send(string $to, string $subject, string $body): bool {
        if (empty($this->user)) return false; // SMTP no configurado

        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=utf-8',
            'From: ' . $this->fromName . ' <' . $this->from . '>',
            'Reply-To: ' . $this->from,
            'X-Mailer: PHP/' . phpversion()
        ];

        return mail($to, $subject, $body, implode("\r\n", $headers));
    }

    public function sendActivityAlert(array $users, array $activity, string $type): void {
        $subjects = [
            'nueva'      => '📋 Nueva actividad: ' . $activity['nombre'],
            'vence'      => '⏰ Actividad próxima a vencer: ' . $activity['nombre'],
            'aprobacion' => '✅ Actividad requiere aprobación: ' . $activity['nombre'],
            'vencida'    => '🔴 Actividad no completada: ' . $activity['nombre'],
        ];

        $subject = $subjects[$type] ?? 'Notificación - ' . APP_NAME;
        $body = $this->buildTemplate($activity, $type);

        foreach ($users as $user) {
            if (!empty($user['email'])) {
                $this->send($user['email'], $subject, $body);
            }
        }
    }

    private function buildTemplate(array $activity, string $type): string {
        $color = match($type) {
            'nueva'      => '#2196F3',
            'vence'      => '#FF9800',
            'aprobacion' => '#9C27B0',
            'vencida'    => '#F44336',
            default      => '#1a3a5c'
        };

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head><meta charset="utf-8"></head>
        <body style="font-family:Arial,sans-serif;background:#f5f5f5;padding:20px;">
          <div style="max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.1)">
            <div style="background:{$color};padding:30px;text-align:center">
              <h1 style="color:#fff;margin:0;font-size:24px">FUNDACITE Carabobo</h1>
              <p style="color:rgba(255,255,255,.8);margin:8px 0 0">Sistema de Gestión de Actividades</p>
            </div>
            <div style="padding:30px">
              <h2 style="color:#333">{$activity['nombre']}</h2>
              <p style="color:#666">{$activity['descripcion']}</p>
              <table style="width:100%;border-collapse:collapse;margin:20px 0">
                <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#999">Fecha inicio</td>
                    <td style="padding:8px;border-bottom:1px solid #eee;font-weight:bold">{$activity['fecha_inicio']}</td></tr>
                <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#999">Fecha límite</td>
                    <td style="padding:8px;border-bottom:1px solid #eee;font-weight:bold;color:{$color}">{$activity['fecha_limite']}</td></tr>
              </table>
              <a href="{BASE_URL}" style="display:inline-block;background:{$color};color:#fff;padding:12px 24px;border-radius:4px;text-decoration:none">Ver en el sistema</a>
            </div>
            <div style="background:#f9f9f9;padding:15px;text-align:center;color:#999;font-size:12px">
              Este es un mensaje automático de {APP_NAME}. Por favor no responda este correo.
            </div>
          </div>
        </body>
        </html>
        HTML;
    }
}
