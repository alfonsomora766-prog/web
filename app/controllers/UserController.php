<?php
require_once BASE_PATH . '/app/core/Controller.php';
require_once BASE_PATH . '/app/models/UserModel.php';
require_once BASE_PATH . '/app/models/NotificationModel.php';
require_once BASE_PATH . '/app/models/SettingModel.php';

class UserController extends Controller {

    public function index(): void {
        requireRole('admin');
        $users    = (new UserModel())->allWithRoles();
        $settings = (new SettingModel())->allSettings();
        $this->layout('users/index', compact('users', 'settings'));
    }

    public function createPage(): void {
        requireRole('admin');
        $roles    = (new UserModel())->getRoles();
        $settings = (new SettingModel())->allSettings();
        $this->layout('users/create', compact('roles', 'settings'));
    }

    public function store(): void {
        requireRole('admin');
        if (!verifyCsrf()) { flash('danger', 'Token inválido.'); redirect('users/create'); return; }

        $userModel = new UserModel();
        $current   = currentUser();

        $email = trim($_POST['email'] ?? '');
        if ($userModel->findByEmail($email)) {
            flash('danger', 'El correo ya está registrado.');
            redirect('users/create');
            return;
        }

        $id = $userModel->create([
            'nombre'     => trim($_POST['nombre'] ?? ''),
            'apellido'   => trim($_POST['apellido'] ?? ''),
            'email'      => $email,
            'rol_id'     => (int)($_POST['rol_id'] ?? 3),
            'created_by' => $current['id'],
        ]);

        // Notificar al nuevo usuario
        (new NotificationModel())->create(
            $id,
            '👋 Bienvenido/a a FUNDACITE',
            'Tu cuenta ha sido creada. Inicia sesión para establecer tu contraseña.',
            'info',
            url('login')
        );

        flash('success', 'Usuario creado. El usuario definirá su contraseña en el primer inicio de sesión.');
        redirect('users');
    }

    public function editPage(string $id): void {
        requireRole('admin');
        $editUser = (new UserModel())->findWithRole((int)$id);
        if (!$editUser) { flash('danger', 'Usuario no encontrado.'); redirect('users'); return; }
        $roles    = (new UserModel())->getRoles();
        $settings = (new SettingModel())->allSettings();
        $this->layout('users/edit', compact('editUser', 'roles', 'settings'));
    }

    public function update(string $id): void {
        requireRole('admin');
        if (!verifyCsrf()) { flash('danger', 'Token inválido.'); redirect("users/edit/$id"); return; }

        $userModel = new UserModel();
        $data = [
            'nombre'   => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'email'    => trim($_POST['email'] ?? ''),
            'rol_id'   => (int)($_POST['rol_id'] ?? 3),
        ];

        // Reset password si se solicita
        if (!empty($_POST['reset_password'])) {
            $userModel->resetPassword((int)$id);
            flash('info', 'Contraseña restablecida. El usuario deberá crear una nueva al iniciar sesión.');
        }

        $userModel->update((int)$id, $data);
        flash('success', 'Usuario actualizado.');
        redirect('users');
    }

    public function delete(string $id): void {
        requireRole('admin');
        $current = currentUser();
        if ((int)$id === $current['id']) {
            flash('danger', 'No puedes eliminar tu propia cuenta.');
            redirect('users');
            return;
        }
        (new UserModel())->delete((int)$id);
        flash('success', 'Usuario eliminado.');
        redirect('users');
    }

    public function toggle(string $id): void {
        requireRole('admin');
        $userModel = new UserModel();
        $user = $userModel->find((int)$id);
        if ($user) {
            $userModel->update((int)$id, ['activo' => $user['activo'] ? 0 : 1]);
            flash('success', 'Estado del usuario actualizado.');
        }
        redirect('users');
    }

    public function profilePage(): void {
        requireAuth();
        $user     = currentUser();
        $settings = (new SettingModel())->allSettings();
        $this->layout('users/profile', compact('user', 'settings'));
    }

    public function updateProfile(): void {
        requireAuth();
        if (!verifyCsrf()) { flash('danger', 'Token inválido.'); redirect('profile'); return; }

        $user      = currentUser();
        $userModel = new UserModel();

        $data = [
            'nombre'   => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
        ];

        // Cambio de contraseña
        $newPass = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!empty($newPass)) {
            $currentPass = $_POST['current_password'] ?? '';
            $dbUser = $userModel->find($user['id']);

            if (!password_verify($currentPass, $dbUser['password'] ?? '')) {
                flash('danger', 'La contraseña actual es incorrecta.');
                redirect('profile');
                return;
            }
            if ($newPass !== $confirm) {
                flash('danger', 'Las contraseñas nuevas no coinciden.');
                redirect('profile');
                return;
            }
            if (strlen($newPass) < 8) {
                flash('danger', 'La nueva contraseña debe tener al menos 8 caracteres.');
                redirect('profile');
                return;
            }
            $data['password'] = password_hash($newPass, PASSWORD_BCRYPT);
        }

        $userModel->update($user['id'], $data);

        // Actualizar sesión
        $_SESSION['user']['nombre']   = $data['nombre'];
        $_SESSION['user']['apellido'] = $data['apellido'];

        flash('success', 'Perfil actualizado correctamente.');
        redirect('profile');
    }
}
