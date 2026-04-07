<?php
require_once BASE_PATH . '/app/core/Controller.php';
require_once BASE_PATH . '/app/models/SettingModel.php';

class SettingController extends Controller {

    public function index(): void {
        requireRole('admin');
        $settings = (new SettingModel())->allSettings();
        $this->layout('settings/index', compact('settings'));
    }

    public function update(): void {
        requireRole('admin');
        if (!verifyCsrf()) { flash('danger', 'Token inválido.'); redirect('settings'); return; }

        $settingModel = new SettingModel();
        $allowed = ['nombre_sistema', 'color_primario', 'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_from'];

        $data = [];
        foreach ($allowed as $key) {
            if (isset($_POST[$key])) {
                $data[$key] = trim($_POST[$key]);
            }
        }

        // Upload logo
        if (!empty($_FILES['logo']['name'])) {
            $result = $this->uploadImage('logo', 'logo');
            if ($result) $data['logo'] = $result;
        }

        // Upload background
        if (!empty($_FILES['fondo_login']['name'])) {
            $result = $this->uploadImage('fondo_login', 'fondo');
            if ($result) $data['fondo_login'] = $result;
        }

        $settingModel->updateMany($data);
        flash('success', 'Configuración guardada correctamente.');
        redirect('settings');
    }

    private function uploadImage(string $field, string $prefix): ?string {
        $file = $_FILES[$field];
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($file['type'], $allowed)) return null;
        if ($file['size'] > 5 * 1024 * 1024) return null;

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . '_' . time() . '.' . $ext;
        $dest     = BASE_PATH . '/public/uploads/' . $filename;

        if (!is_dir(BASE_PATH . '/public/uploads')) {
            mkdir(BASE_PATH . '/public/uploads', 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return $filename;
        }
        return null;
    }
}
