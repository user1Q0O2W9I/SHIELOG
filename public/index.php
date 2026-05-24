<?php

session_start();

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/UrlAnalysis.php';
require_once __DIR__ . '/../models/LogAnalysis.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/UrlController.php';
require_once __DIR__ . '/../controllers/LogController.php';

function redirect(string $route): void
{
    header('Location: index.php?route=' . $route);
    exit;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Debes iniciar sesion para acceder.'];
        redirect('login');
    }
}

function requireCompany(): void
{
    requireLogin();

    if (currentUser()['rol'] !== 'empresa') {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Esta seccion es exclusiva para empresas.'];
        redirect('urls');
    }
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function validateCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('Token CSRF invalido.');
    }
}

$route = $_GET['route'] ?? 'home';
$method = $_SERVER['REQUEST_METHOD'];

$authController = new AuthController();
$urlController = new UrlController();
$logController = new LogController();

// Router frontal: todas las rutas pasan por este switch para mantener la app sencilla.
switch ($route) {
    case 'home':
        $urlController->index();
        break;
    case 'urls':
        $urlController->index();
        break;
    case 'url-analyze':
        validateCsrf();
        $urlController->analyze();
        break;
    case 'login':
        if ($method === 'POST') {
            validateCsrf();
            $authController->login();
        }
        $authController->showLogin();
        break;
    case 'register':
        if ($method === 'POST') {
            validateCsrf();
            $authController->register();
        }
        $authController->showRegister();
        break;
    case 'logout':
        $authController->logout();
        break;
    case 'logs':
        requireCompany();
        $logController->index();
        break;
    case 'log-upload':
        requireCompany();
        validateCsrf();
        $logController->uploadAndAnalyze();
        break;
    default:
        http_response_code(404);
        require __DIR__ . '/../views/errors/404.php';
}
