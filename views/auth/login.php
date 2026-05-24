<?php require __DIR__ . '/../layout/header.php'; ?>

<section class="auth-grid">
    <div>
        <p class="eyebrow">Zona privada</p>
        <h1>Inicia sesion</h1>
        <p class="lead">Accede al panel para analizar URLs y, si eres empresa, subir logs corporativos.</p>
    </div>

    <form class="form-card" method="POST" action="index.php?route=login">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

        <label for="email">Email</label>
        <input id="email" type="email" name="email" required placeholder="empresa@demo.com">

        <label for="password">Contrasena</label>
        <input id="password" type="password" name="password" required placeholder="password">

        <button class="button" type="submit">Entrar</button>
        <p class="form-help">Demo empresa: empresa@demo.com · Demo usuario: usuario@demo.com · Password: password</p>
    </form>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>
