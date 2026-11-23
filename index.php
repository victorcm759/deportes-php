<?php
include 'ical.php';

// null = calendario sin configurar o sin poder cargarse
$proximas = proximasCompeticiones();
$e = fn($texto) => htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es" data-theme="dark">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Registros Deportivos</title>
    <link rel="stylesheet" href="css/index.css?v=<?= filemtime(__DIR__ . '/css/index.css') ?>">
    <link rel="shortcut icon" href="images/circle-icon.png" type="image/x-icon">
</head>

<body>
    <header>
        <img src="images/logo.jpg" alt="Logo">
        <h1>Mi registro deportivo</h1>
        <h2>Víctor Català Mendoza</h2>
    </header>
    <h2>¡Bienvenidos!</h2>
    <p>Este es mi registro deportivo, desde todas las medallas que conseguí en slalom y en boccia, hasta los resultados
        de cada partido que gané. Se tienen registros desde 2023 (2024 en boccia)</p>
    <ul class="lista">
        <li><button><a href="">Mi historia deportiva (próximamente)</a></button></li>
        <li><button><a href="medallero.php">Registro de medallas</a></button></li>
        <li><button><a href="partidos.php">Resultados de partidos de boccia</a></button></li>
        <li><button><a href="competiciones.php">Registro de competiciones</a></button></li>
    </ul>

    <?php if ($proximas !== null): ?>
        <h2>Próximas competiciones</h2>
        <?php if (count($proximas) === 0): ?>
            <p>No hay competiciones programadas</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Fecha</th>
                    <th>Deporte</th>
                    <th>Competición</th>
                    <th>Sede</th>
                    <th>Provincia</th>
                    <th>Comunidad</th>
                </tr>
                <?php foreach ($proximas as $evento):
                    [$deporte, $competicion] = separarDeporte($evento['titulo']);
                    $ubicacion = separarUbicacion($evento['lugar']);
                    $sede = $ubicacion['sede'] ?: '-';
                    $provincia = $ubicacion['provincia'] ?: '-';
                    $comunidad = $ubicacion['comunidad'] ?: '-';
                    ?>
                    <tr>
                        <td><?= $e(formatoFechasEvento($evento)) ?></td>
                        <td><?= $e($deporte ?: '-') ?></td>
                        <td><?= $e($competicion) ?></td>
                        <?php // Celdas unidas cuando se repiten, como en la tabla de competiciones
                        if ($sede === $provincia): ?>
                            <td colspan="2"><?= $e($sede) ?></td>
                            <td><?= $e($comunidad) ?></td>
                        <?php elseif ($provincia === $comunidad): ?>
                            <td><?= $e($sede) ?></td>
                            <td colspan="2"><?= $e($provincia) ?></td>
                        <?php else: ?>
                            <td><?= $e($sede) ?></td>
                            <td><?= $e($provincia) ?></td>
                            <td><?= $e($comunidad) ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    <?php endif; ?>

    <h3>&copy; 2026 Víctor Català Mendoza</h3>
    <script src="js/script.js"></script>
</body>

</html>