<?php require __DIR__ . '/../layout/header.php'; ?>

<section class="hero">
    <p class="eyebrow">Modulo 2 · Empresas</p>
    <h1>Analisis de archivos log</h1>
    <p class="lead">Sube un archivo `.log` o `.txt` para detectar cadenas base64, comandos peligrosos, descargas remotas, fallos de login e IPs repetidas.</p>
</section>

<section class="panel">
    <form class="upload-form" method="POST" action="index.php?route=log-upload" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <label for="log_file">Archivo de log</label>
        <input id="log_file" type="file" name="log_file" accept=".log,.txt" required>
        <span class="form-help" data-file-name>Ningun archivo seleccionado</span>
        <button class="button" type="submit">Subir y analizar</button>
    </form>
</section>

<?php if ($analysis): ?>
    <section class="result result-<?= htmlspecialchars($analysis['riskLevel']) ?>">
        <div>
            <p class="eyebrow">Nivel de riesgo</p>
            <h2><?= htmlspecialchars(strtoupper($analysis['riskLevel'])) ?></h2>
            <p><?= (int) $analysis['suspiciousLines'] ?> lineas sospechosas de <?= (int) $analysis['totalLines'] ?> totales.</p>
        </div>
        <div>
            <h3>Amenazas detectadas</h3>
            <?php if ($analysis['threats'] === []): ?>
                <p>No se han detectado patrones peligrosos.</p>
            <?php else: ?>
                <ul class="tag-list">
                    <?php foreach ($analysis['threats'] as $type => $count): ?>
                        <li><?= htmlspecialchars($type) ?>: <?= (int) $count ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>

    <section class="panel">
        <h2>Ejemplos resaltados</h2>
        <?php foreach ($analysis['examples'] as $example): ?>
            <article class="log-line">
                <strong>Linea <?= htmlspecialchars((string) $example['line']) ?></strong>
                <code><?= htmlspecialchars($example['content']) ?></code>
                <span><?= htmlspecialchars(implode(', ', $example['threats'])) ?></span>
            </article>
        <?php endforeach; ?>
        <?php if ($analysis['examples'] === []): ?>
            <p>No hay lineas peligrosas para mostrar.</p>
        <?php endif; ?>
    </section>
<?php endif; ?>

<section class="panel">
    <div class="section-title">
        <h2>Historial de logs</h2>
        <p>Solo se muestran los analisis del usuario empresa conectado.</p>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Archivo</th>
                    <th>Lineas</th>
                    <th>Sospechosas</th>
                    <th>Riesgo</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['archivo']) ?></td>
                        <td><?= (int) $item['lineas_totales'] ?></td>
                        <td><?= (int) $item['lineas_sospechosas'] ?></td>
                        <td><span class="badge badge-<?= htmlspecialchars($item['nivel_riesgo']) ?>"><?= htmlspecialchars($item['nivel_riesgo']) ?></span></td>
                        <td><?= htmlspecialchars($item['fecha']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($history === []): ?>
                    <tr><td colspan="5">Todavia no hay logs analizados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/../layout/footer.php'; ?>

