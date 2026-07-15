<?php include 'conexion.php';
include 'funciones.php'; ?>

<!DOCTYPE html>
<html>

<head>
    <title>Registro de competiciones</title>
    <link rel="stylesheet" href="css/index.css?v=<?= filemtime(__DIR__ . '/css/index.css') ?>">
    <link rel="shortcut icon" href="images/circle-icon.png" type="image/x-icon">
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
            <li><a href="index.php">Inicio</a></li>
            <li><a href="medallero.php">Registro de medallas</a></li>
            <li><a href="partidos.php">Resultados de partidos de boccia</a></li>
            <li><a href="competiciones.php">Registro de competiciones</a></li>
            <li><a href="pasado/pasado.php">Temporadas pasadas</a></li>
        </ul>
    </nav>
    <header>
        <h1>Registro digitalizado de competiciones</h1>
        <h2>Víctor Català Mendoza</h2>
        <button id="theme-toggle" type="button">Modo oscuro</button>
    </header>

    <!-- FORMULARIO DE BÚSQUEDA -->
    <form method="GET">
        Tipo:
        <select name="tipo">
            <option value="">Seleccione uno...</option>
            <option value="autonomico">Autonómico</option>
            <option value="nacional">Nacional</option>
            <!-- <option value="internacional">Internacional</option> -->
        </select>

        Deporte:
        <select name="deporte">
            <option value="">Seleccione uno...</option>
            <option value="slalom">Slalom</option>
            <option value="boccia">Boccia</option>
        </select>

        Municipio:
        <input type="text" name="lugar" id="municipio" placeholder="Buscar por municipio">

        Provincia:
        <input type="text" name="provincia" id="provincia" placeholder="Buscar por provincia">

        CC.AA. / Estado:
        <input type="text" name="comunidad" id="comunidad" placeholder="Buscar por estado/comunidad autónoma">

        País:
        <input type="text" name="pais" id="pais" placeholder="Buscar por país">

        Posición:
        <select name="posicion">
            <option value="">Elige medalla</option>
            <option value="oro">Oro</option>
            <option value="plata">Plata</option>
            <option value="bronce">Bronce</option>
            <option value="participante">Participante</option>
        </select>
        <label class="checkbox-label">
            <input type="checkbox" name="excluir_participante" value="1" <?php echo !empty($_GET['excluir_participante']) ? 'checked' : ''; ?>> Excluir participaciones (solo con medalla)
        </label>
        Año:
        <select name="year">
            <option value="">Elige año</option>
            <?php
            $year_inicio = 2023;
            $year_actual = date('Y');

            for ($i = $year_inicio; $i <= $year_actual; $i++) {
                $seleccionado = ($_GET['year'] ?? '') == $i ? 'selected' : '';
                echo "<option value=\"$i\" $seleccionado>$i</option>";
            }
            ?>
        </select>
        <input type="submit" value="Buscar">
        <button type="button" id="limpiar-filtros">Limpiar búsqueda</button>
    </form>
    <?php
    // Construir consulta con filtros
    $tipo = $_GET['tipo'] ?? '';
    $deporte = $_GET['deporte'] ?? '';
    $lugar = $_GET['lugar'] ?? '';
    $provincia = $_GET['provincia'] ?? '';
    $comunidad = $_GET['comunidad'] ?? '';
    $pais = $_GET['pais'] ?? '';
    $posicion = $_GET['posicion'] ?? '';
    $year = $_GET['year'] ?? '';
    $excluirParticipante = !empty($_GET['excluir_participante']);

    $where = " WHERE 1=1";

    if (!empty($deporte)) {
        $where .= " AND m.deporte LIKE '%" . $conexion->real_escape_string($deporte) . "%'";
    }
    if (!empty($lugar)) {
        $where .= " AND c.lugar LIKE '%" . $conexion->real_escape_string($lugar) . "%'";
    }
    if (!empty($provincia)) {
        $where .= " AND c.provincia LIKE '%" . $conexion->real_escape_string($provincia) . "%'";
    }
    if (!empty($comunidad)) {
        $where .= " AND c.comunidad LIKE '%" . $conexion->real_escape_string($comunidad) . "%'";
    }
    if (!empty($pais)) {
        $where .= " AND c.pais LIKE '%" . $conexion->real_escape_string($pais) . "%'";
    }
    if (!empty($posicion)) {
        $where .= " AND m.posicion = '" . $conexion->real_escape_string($posicion) . "'";
    }
    if (!empty($year)) {
        $where .= " AND c.year = " . intval($year);
    }
    if (!empty($tipo)) {
        $where .= " AND c.tipo = '" . $conexion->real_escape_string($tipo) . "'";
    }
    if ($excluirParticipante) {
        $where .= " AND m.posicion IN ('oro', 'plata', 'bronce')";
    }

    // Listado de competiciones (tabla competiciones) con el recuento de medallas de cada una
    $sql = "SELECT c.tipo, c.competicion, c.lugar, c.provincia, c.comunidad, c.pais, c.year,
            GROUP_CONCAT(DISTINCT m.deporte ORDER BY m.deporte SEPARATOR ' / ') AS deporte,
            SUM(m.posicion = 'oro') AS oro,
            SUM(m.posicion = 'plata') AS plata,
            SUM(m.posicion = 'bronce') AS bronce,
            SUM(m.posicion = 'participante') AS participantes
        FROM competiciones c
        LEFT JOIN medallas m ON m.competicion = c.competicion AND m.year = c.year" . $where . "
        GROUP BY c.competicion, c.year
        ORDER BY c.year ASC, m.id ASC";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0): ?>
        <table>
            <tr>
                <th>Tipo</th>
                <th colspan="2">Competición</th>
                <th>Deporte</th>
                <th>Municipio</th>
                <th>Provincia</th>
                <th>CC.AA. / Estado</th>
                <th>Año</th>
                <!-- <th>País</th> -->
                <th class="oro">O</th>
                <th class="plata">P</th>
                <th class="bronce">B</th>
                <th>NO P</th>
            </tr>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $fila['tipo']; ?></td>
                    <td colspan="2"><?php echo $fila['competicion']; ?></td>
                    <td><?php echo $fila['deporte']; ?></td>
                    <?php
                    if ($fila['lugar'] == $fila['provincia']) {
                        echo '<td colspan="2">' . $fila['lugar'] . '</td>';
                        echo '<td>' . $fila['comunidad'] . '</td>';
                    } elseif ($fila['provincia'] == $fila['comunidad']) {
                        echo '<td colspan="2">' . $fila['provincia'] . '</td>';
                        echo '<td>' . $fila['comunidad'] . '</td>';
                    } else {
                        echo '<td>' . $fila['lugar'] . '</td>';
                        echo '<td>' . $fila['provincia'] . '</td>';
                        echo '<td>' . $fila['comunidad'] . '</td>';
                    }
                    ?>
                    <td><?php echo $fila['year']; ?></td>
                    <?php
                    $pais = $fila['pais'];
                    $codigo = obtenerCodigoPais($pais);
                    ?>
                    <!--
                    <td class="pais">
                        <img class="bandera" src="https://flagcdn.com/h20/<?= $codigo ?>.png" alt="<?= $pais ?>">
                        <?= $pais ?>
                    </td>-->
                    <td class="oro"><?php echo (int) $fila['oro']; ?></td>
                    <td class="plata"><?php echo (int) $fila['plata']; ?></td>
                    <td class="bronce"><?php echo (int) $fila['bronce']; ?></td>
                    <td><?php echo (int) $fila['participantes']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No se han encontrado resultados</p>
    <?php endif; ?>

    <?php
    $sqlResumen = "SELECT
            COUNT(*) AS total_competiciones,
            SUM(deporte LIKE '%Slalom%') AS total_slalom,
            SUM(deporte LIKE '%Boccia%') AS total_boccia,
            SUM(tipo = 'Autonómico') AS total_catalunya,
            SUM(tipo = 'Nacional') AS total_espanna,
            SUM(tipo = 'Nacional' AND comunidad = 'Cataluña') AS total_nacional_catalunya
        FROM (
            SELECT c.tipo, c.competicion, c.year, c.comunidad,
                GROUP_CONCAT(DISTINCT m.deporte) AS deporte
            FROM competiciones c
            LEFT JOIN medallas m ON m.competicion = c.competicion AND m.year = c.year" . $where . "
            GROUP BY c.tipo, c.competicion, c.year, c.comunidad
        ) AS competiciones_filtradas";
    $resultadoResumen = $conexion->query($sqlResumen);
    $resumen = $resultadoResumen ? $resultadoResumen->fetch_assoc() : null;

    if ($resumen): ?>
        <table>
            <tr>
                <th>Total de competiciones: <?php echo (int) $resumen['total_competiciones']; ?></th>
            </tr>
            <tr>
                <td>de Slalom: <?php echo (int) $resumen['total_slalom']; ?></td>
            </tr>
            <tr>
                <td>de Boccia: <?php echo (int) $resumen['total_boccia']; ?></td>
            </tr>
            <tr>
                <td>Autonómicos (Cataluña):
                    <?php echo (int) $resumen['total_catalunya']; ?>
                </td>
            </tr>
            <tr>
                <td>Nacionales (España):
                    <?php echo (int) $resumen['total_espanna']; ?> · Jugados en Cataluña: <?php echo (int) $resumen['total_nacional_catalunya']; ?>
                </td>
            </tr>
        </table>
    <?php else: ?>
        <p>No se han encontrado resultados</p>
    <?php endif; ?>

    <?php
    $sqlNacionalesPorComunidad = "SELECT com.comunidad, COALESCE(t.total, 0) AS total
        FROM (SELECT DISTINCT comunidad FROM competiciones WHERE pais = 'España') com
        LEFT JOIN (
            SELECT c.comunidad, COUNT(DISTINCT c.competicion, c.year) AS total
            FROM competiciones c
            LEFT JOIN medallas m ON m.competicion = c.competicion AND m.year = c.year" . $where . "
            AND c.tipo = 'Nacional' AND c.pais = 'España'
            GROUP BY c.comunidad
        ) t ON t.comunidad = com.comunidad
        ORDER BY total DESC, com.comunidad ASC";
    $resultadoNacionalesPorComunidad = $conexion->query($sqlNacionalesPorComunidad);

    if ($resultadoNacionalesPorComunidad && $resultadoNacionalesPorComunidad->num_rows > 0): ?>
        <table>
            <tr>
                <th colspan="2">Competiciones nacionales por Comunidad Autónoma</th>
            </tr>
            <?php while ($fila = $resultadoNacionalesPorComunidad->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $fila['comunidad']; ?><?php echo $fila['comunidad'] === 'Cataluña' ? ' (propia comunidad)' : ''; ?></td>
                    <td><?php echo (int) $fila['total']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No se han encontrado resultados</p>
    <?php endif; ?>
    <script src="js/script.js?v=<?= filemtime(__DIR__ . '/js/script.js') ?>"></script>
</body>

</html>
