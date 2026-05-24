<?php require __DIR__ . '/../layout/header.php'; ?>

<section class="auth-grid">
    <div>
        <p class="eyebrow">Alta de usuario</p>
        <h1>Crea una cuenta</h1>
        <p class="lead">El rol empresa desbloquea el modulo de logs y permite guardar historiales privados.</p>
    </div>

    <form class="form-card" method="POST" action="index.php?route=register">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

        <label for="email">Email</label>
        <input id="email" type="email" name="email" required>

        <label for="password">Contrasena</label>
        <input id="password" type="password" name="password" minlength="8" required>

        <label for="rol">Rol</label>
        <select id="rol" name="rol" required>
            <option value="usuario">Usuario normal</option>
            <option value="empresa">Empresa</option>
        </select>

        <button class="button" type="submit">Crear cuenta</button>
    </form>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>

