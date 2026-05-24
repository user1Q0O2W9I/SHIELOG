<?php
$user = currentUser();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'ShieldLog') ?> | ShieldLog</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header class="site-header">
        <nav class="nav">
            <a class="brand" href="index.php?route=urls">ShieldLog</a>
            <div class="nav-links">
                <a href="index.php?route=urls">Analizar URL</a>
                <?php if ($user && $user['rol'] === 'empresa'): ?>
                    <a href="index.php?route=logs">Analizar logs</a>
                <?php endif; ?>
                <?php if ($user): ?>
                    <span class="user-pill"><?= htmlspecialchars($user['email']) ?> · <?= htmlspecialchars($user['rol']) ?></span>
                    <a href="index.php?route=logout">Salir</a>
                <?php else: ?>
                    <a href="index.php?route=login">Login</a>
                    <a class="button small" href="index.php?route=register">Registro</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="container">
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

