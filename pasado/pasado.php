<?php ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros de temporadas pasadas</title>
    <link rel="stylesheet" href="../css/index.css?v=<?= filemtime(__DIR__ . '/../css/index.css') ?>">
    <link rel="shortcut icon" href="../images/circle-icon.png" type="image/x-icon">
</head>
<body>
    <button id="menu-toggle" type="button" aria-expanded="false" aria-controls="menu-lateral" aria-label="Abrir menú">
        <span class="menu-toggle-barra"></span>
        <span class="menu-toggle-barra"></span>
        <span class="menu-toggle-barra"></span>
    </button>
    <div id="menu-fondo"></div>
    <nav id="menu-lateral" aria-hidden="true">
        <div class="menu-lateral-cabecera">
            <span>Menú</span>
            <button id="menu-cerrar" type="button" aria-label="Cerrar menú">&times;</button>
        </div>
        <ul class="menu-lateral-lista">
            <li><a href="../index.php">Inicio</a></li>
            <li><a href="../medallero.php">Registro de medallas</a></li>
            <li><a href="../partidos.php">Resultados de partidos de boccia</a></li>
            <li><a href="../competiciones.php">Registro de competiciones</a></li>
            <li><a href="pasado.php">Temporadas pasadas</a></li>
        </ul>
    </nav>
    <header>
        <h1>Registros de temporadas pasadas</h1>
        <button id="theme-toggle" type="button">Modo oscuro</button>
    </header>
    <p>Seleccione una temporada para ver los registros: </p>
    <ul class="season-list">
        <li><a href="2425.php">Temporada 2024/2025</a></li>
        <li><a href="2526.php">Temporada 2025/2026</a></li>
        <!-- <li><a href="#">Temporada 20XX/YY</a></li> -->
    </ul>
    <script src="../js/script.js?v=<?= filemtime(__DIR__ . '/../js/script.js') ?>"></script>
</body>
</html>