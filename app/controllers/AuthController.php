<?php
require_once BASE_PATH . '/app/core/Controller.php';
require_once BASE_PATH . '/app/models/UserModel.php';
require_once BASE_PATH . '/app/models/SettingModel.php';

class AuthController extends Controller {

    public function loginPage(): void {
        if (isLoggedIn()) { redirect('dashboard'); return; }
        $settings = (new SettingModel())->allSettings();
        $flash    = getFlash();
        $this->layout('auth/login', compact('settings', 'flash'), 'auth');
    }

    public function login(): void {
        if (!verifyCsrf()) { flash('danger', 'Token inválido.'); redirect('login'); return; }

        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            flash('danger', 'Credenciales incorrectas.');
            redirect('login');
            return;
        }

        // Primer login sin contraseña
        if ($user['primer_login'] == 1 && empty($user['password'])) {
            $_SESSION['temp_user_id'] = $user['id'];
            redirect('set-password');
            return;
        }

        if (!password_verify($pass, $user['password'] ?? '')) {
            flash('danger', 'Contraseña incorrecta.');
            redirect('login');
            return;
        }

        // Si primer login pero tiene contraseña antigua => forzar cambio
        if ($user['primer_login'] == 1) {
            $_SESSION['temp_user_id'] = $user['id'];
            redirect('set-password');
            return;
        }

        $this->startSession($user);
        redirect('dashboard');
    }

    public function setPasswordPage(): void {
        if (isLoggedIn()) { redirect('dashboard'); return; }
        if (empty($_SESSION['temp_user_id'])) { redirect('login'); return; }
        $settings = (new SettingModel())->allSettings();
        $flash    = getFlash();
        $this->layout('auth/set-password', compact('settings', 'flash'), 'auth');
    }

    public function setPassword(): void {
        if (!verifyCsrf()) { flash('danger', 'Token inválido.'); redirect('set-password'); return; }

        $userId = $_SESSION['temp_user_id'] ?? null;
        if (!$userId) { redirect('login'); return; }

        $pass    = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';

        if (strlen($pass) < 8) {
            flash('danger', 'La contraseña debe tener al menos 8 caracteres.');
            redirect('set-password');
            return;
        }
        if ($pass !== $confirm) {
            flash('danger', 'Las contraseñas no coinciden.');
            redirect('set-password');
            return;
        }

        $userModel = new UserModel();
        $userModel->setPassword($userId, $pass);
        $user = $userModel->findWithRole($userId);

        unset($_SESSION['temp_user_id']);
        $this->startSession($user);
        flash('success', '¡Bienvenido/a! Tu contraseña ha sido establecida.');
        redirect('dashboard');
    }

    public function logout(): void {
        session_destroy();
        redirect('login');
    }

    private function startSession(array $user): void {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user']    = [
            'id'       => $user['id'],
            'nombre'   => $user['nombre'],
            'apellido' => $user['apellido'],
            'email'    => $user['email'],
            'rol'      => $user['rol'],
            'foto'     => $user['foto'] ?? '',
        ];
    }
}
