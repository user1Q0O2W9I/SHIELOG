<?php

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showLogin(): void
    {
        $title = 'Iniciar sesion';
        require __DIR__ . '/../views/auth/login.php';
    }

    public function showRegister(): void
    {
        $title = 'Crear cuenta';
        require __DIR__ . '/../views/auth/register.php';
    }

    public function register(): void
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $role = $_POST['rol'] ?? 'usuario';

        if (!$email || strlen($password) < 8 || !in_array($role, ['usuario', 'empresa'], true)) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Revisa el email, el rol y usa una contrasena de al menos 8 caracteres.',
            ];
            redirect('register');
        }

        if ($this->userModel->emailExists($email)) {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Ya existe una cuenta con ese email.'];
            redirect('register');
        }

        $this->userModel->create($email, $password, $role);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Cuenta creada. Ya puedes iniciar sesion.'];
        redirect('login');
    }

    public function login(): void
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (!$email || $password === '') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Introduce email y contrasena.'];
            redirect('login');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Credenciales incorrectas.'];
            redirect('login');
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'email' => $user['email'],
            'rol' => $user['rol'],
        ];

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Sesion iniciada correctamente.'];
        redirect('urls');
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        redirect('login');
    }
}

