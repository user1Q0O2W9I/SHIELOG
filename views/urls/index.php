<?php require __DIR__ . '/../layout/header.php'; ?>

<section class="hero">
    <p class="eyebrow">Modulo 1</p>
    <h1>Analisis heuristico de URLs</h1>
    <p class="lead">Introduce una URL y la aplicacion calculara una puntuacion de riesgo usando reglas habituales en deteccion de phishing.</p>
</section>

<section class="panel">
    <form class="url-form" method="POST" action="index.php?route=url-analyze">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <label for="url">URL a analizar</label>
        <div class="inline-form">
            <input id="url" type="url" name="url" placeholder="https://example.com/login" required>
            <button class="button" type="submit">Analizar</button>
        </div>
    </form>
</section>

<?php if ($analysis): ?>
    <?php $class = strtolower($analysis['result']); ?>
    <section class="result result-<?= htmlspecialchars($class) ?>">
        <div>
            <p class="eyebrow">Resultado</p>
            <h2><?= htmlspecialchars($analysis['result']) ?></h2>
            <p>Puntuacion: <strong><?= (int) $analysis['score'] ?></strong></p>
        </div>
        <div>
            <h3>Reglas activadas</h3>
            <?php if ($analysis['rules'] === []): ?>
                <p>No se han detectado indicadores de riesgo importantes.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($analysis['rules'] as $rule): ?>
                        <li><?= htmlspecialchars($rule['message']) ?> (+<?= (int) $rule['points'] ?>)</li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<section class="panel">
    <div class="section-title">
        <h2>Historial de URLs</h2>
        <p>Ultimos analisis guardados en la base de datos.</p>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>URL</th>
                    <th>Puntos</th>
                    <th>Resultado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $item): ?>
                    <tr>
                        <td class="truncate"><?= htmlspecialchars($item['url']) ?></td>
                        <td><?= (int) $item['puntuacion'] ?></td>
                        <td><span class="badge badge-<?= strtolower($item['resultado']) ?>"><?= htmlspecialchars($item['resultado']) ?></span></td>
                        <td><?= htmlspecialchars($item['fecha']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($history === []): ?>
                    <tr><td colspan="4">Todavia no hay analisis guardados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>

