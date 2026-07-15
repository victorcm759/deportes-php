<?php include 'conexion.php';
include 'funciones.php'; ?>

<!DOCTYPE html>
<html>

<head>
    <title>Registro de medallas</title>
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
        <h1>Registro digitalizado de medallas</h1>
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

        <!-- Paí­s:
        <input type="text" name="pais" id="pais" placeholder="Buscar por paí­s">
        -->
        Posición:
        <select name="posicion">
            <option value="">Elige medalla</option>
            <option value="oro">Oro</option>
            <option value="plata">Plata</option>
            <option value="bronce">Bronce</option>
            <option value="participante">Participante</option>
        </select>
        <label class="checkbox-label">
            <input type="checkbox" name="excluir_participante" value="1" <?php echo !empty($_GET['excluir_participante']) ? 'checked' : ''; ?>> Excluir participantes (solo medallas)
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
        $where .= " AND deporte LIKE '%" . $conexion->real_escape_string($deporte) . "%'";
    }
    if (!empty($lugar)) {
        $where .= " AND lugar LIKE '%" . $conexion->real_escape_string($lugar) . "%'";
    }
    if (!empty($provincia)) {
        $where .= " AND provincia LIKE '%" . $conexion->real_escape_string($provincia) . "%'";
    }
    if (!empty($comunidad)) {
        $where .= " AND comunidad LIKE '%" . $conexion->real_escape_string($comunidad) . "%'";
    }
    if (!empty($pais)) {
        $where .= " AND pais LIKE '%" . $conexion->real_escape_string($pais) . "%'";
    }
    if (!empty($posicion)) {
        $where .= " AND posicion = '" . $conexion->real_escape_string($posicion) . "'";
    }
    if (!empty($year)) {
        $where .= " AND year = " . intval($year);
    }
    if (!empty($tipo)) {
        $where .= " AND tipo = '" . $conexion->real_escape_string($tipo) . "'";
    }
    if ($excluirParticipante) {
        $where .= " AND posicion IN ('oro', 'plata', 'bronce')";
    }

    $whereCompeticiones = " WHERE 1=1";
    if (!empty($tipo)) {
        $whereCompeticiones .= " AND c.tipo = '" . $conexion->real_escape_string($tipo) . "'";
    }
    if (!empty($lugar)) {
        $whereCompeticiones .= " AND c.lugar LIKE '%" . $conexion->real_escape_string($lugar) . "%'";
    }
    if (!empty($provincia)) {
        $whereCompeticiones .= " AND c.provincia LIKE '%" . $conexion->real_escape_string($provincia) . "%'";
    }
    if (!empty($comunidad)) {
        $whereCompeticiones .= " AND c.comunidad LIKE '%" . $conexion->real_escape_string($comunidad) . "%'";
    }
    if (!empty($posicion)) {
        $whereCompeticiones .= " AND m.posicion = '" . $conexion->real_escape_string($posicion) . "'";
    }
    if (!empty($year)) {
        $whereCompeticiones .= " AND c.year = " . intval($year);
    }
    if ($excluirParticipante) {
        $whereCompeticiones .= " AND m.posicion IN ('oro', 'plata', 'bronce')";
    }

    $sql = "SELECT * FROM medallas" . $where;
    $sqlResumenSlalom = "SELECT
        SUM(CASE WHEN LOWER(posicion) = 'oro' THEN 1 ELSE 0 END) AS oro,
        SUM(CASE WHEN LOWER(posicion) = 'plata' THEN 1 ELSE 0 END) AS plata,
        SUM(CASE WHEN LOWER(posicion) = 'bronce' THEN 1 ELSE 0 END) AS bronce
    FROM medallas" . $where . " AND LOWER(deporte) LIKE '%slalom%' AND LOWER(posicion) IN ('oro', 'plata', 'bronce')";
    $sqlResumenBoccia = "SELECT
        SUM(CASE WHEN LOWER(posicion) = 'oro' THEN 1 ELSE 0 END) AS oro,
        SUM(CASE WHEN LOWER(posicion) = 'plata' THEN 1 ELSE 0 END) AS plata,
        SUM(CASE WHEN LOWER(posicion) = 'bronce' THEN 1 ELSE 0 END) AS bronce
    FROM medallas" . $where . " AND LOWER(deporte) LIKE '%boccia%' AND LOWER(posicion) IN ('oro', 'plata', 'bronce')";
    $sqlResumen = "SELECT
        SUM(posicion = 'oro') AS oro,
        SUM(posicion = 'plata') AS plata,
        SUM(posicion = 'bronce') AS bronce,
        SUM(posicion IN ('participante')) AS total_participantes,
        SUM(posicion IN ('oro', 'plata', 'bronce')) AS total_medallas,
        (SELECT COUNT(DISTINCT c.competicion, c.year) FROM competiciones c JOIN medallas m ON m.competicion = c.competicion AND m.year = c.year" . $whereCompeticiones . " AND m.deporte = 'Slalom' AND m.posicion IN ('oro', 'plata', 'bronce', 'participante')) AS total_competiciones_slalom,
        (SELECT COUNT(DISTINCT c.competicion, c.year) FROM competiciones c JOIN medallas m ON m.competicion = c.competicion AND m.year = c.year" . $whereCompeticiones . " AND m.deporte = 'Boccia' AND m.posicion IN ('oro', 'plata', 'bronce', 'participante')) AS total_competiciones_boccia,
        (SELECT COUNT(DISTINCT c.competicion, c.year) FROM competiciones c JOIN medallas m ON m.competicion = c.competicion AND m.year = c.year" . $whereCompeticiones . " AND m.deporte = 'Slalom' AND c.tipo = 'autonomico' AND m.posicion IN ('oro', 'plata', 'bronce', 'participante')) AS total_catalunya_slalom,
        (SELECT COUNT(DISTINCT c.competicion, c.year) FROM competiciones c JOIN medallas m ON m.competicion = c.competicion AND m.year = c.year" . $whereCompeticiones . " AND m.deporte = 'Boccia' AND c.tipo = 'autonomico' AND m.posicion IN ('oro', 'plata', 'bronce', 'participante')) AS total_catalunya_boccia,
        (SELECT COUNT(DISTINCT c.competicion, c.year) FROM competiciones c JOIN medallas m ON m.competicion = c.competicion AND m.year = c.year" . $whereCompeticiones . " AND m.deporte = 'Slalom' AND c.tipo = 'nacional' AND m.posicion IN ('oro', 'plata', 'bronce', 'participante')) AS total_espanna_slalom,
        (SELECT COUNT(DISTINCT c.competicion, c.year) FROM competiciones c JOIN medallas m ON m.competicion = c.competicion AND m.year = c.year" . $whereCompeticiones . " AND m.deporte = 'Boccia' AND c.tipo = 'nacional' AND m.posicion IN ('oro', 'plata', 'bronce', 'participante')) AS total_espanna_boccia,
        (SELECT COUNT(DISTINCT c.competicion, c.year) FROM competiciones c JOIN medallas m ON m.competicion = c.competicion AND m.year = c.year" . $whereCompeticiones . " AND c.tipo = 'nacional' AND c.comunidad = 'Cataluña' AND m.posicion IN ('oro', 'plata', 'bronce', 'participante')) AS total_nacional_catalunya,
        COUNT(*) AS total_registros
    FROM medallas" . $where . " AND posicion IN ('oro', 'plata', 'bronce', 'participante')";

    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Posición</th>
                <th colspan="2">Competición</th>
                <th>Deporte</th>
                <th>Municipio</th>
                <th>Provincia</th>
                <th>CC.AA. / Estado</th>
                <!-- <th>País</th> -->
                <th>Año</th>
            </tr>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <?php
                $clase = '';
                switch ($fila['posicion']) {
                    case 'oro':
                        $clase = 'oro';
                        break;
                    case 'plata':
                        $clase = 'plata';
                        break;
                    case 'bronce':
                        $clase = 'bronce';
                        break;
                }
                ?>
                <tr>
                    <td><?php echo $fila['id']; ?></td>
                    <td><?php echo $fila['tipo']; ?></td>
                    <td class="<?php echo $fila['posicion']; ?>">
                        <?php echo iconoMedalla($fila['posicion']); ?>
                    </td>
                    <td><?php echo $fila['competicion']; ?></td>
                    <td><?php echo $fila['division']; ?></td>
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
                    $pais = $fila['pais'];
                    $codigo = obtenerCodigoPais($pais);
                    ?>
                    <!-- <td class="pais">
                        <img class="bandera" src="https://flagcdn.com/h20/<?= $codigo ?>.png" alt="<?= $pais ?>">
                        <?= $pais ?>
                    </td> -->
                    <td><?php echo $fila['year']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No se han encontrado resultados</p>
    <?php endif; ?>

    <?php
    $resultado2 = $conexion->query($sqlResumen);
    $resumen = $resultado2 ? $resultado2->fetch_assoc() : null;
    $resultadoSlalom = $conexion->query($sqlResumenSlalom);
    $resumenSlalom = $resultadoSlalom ? $resultadoSlalom->fetch_assoc() : null;
    $resultadoBoccia = $conexion->query($sqlResumenBoccia);
    $resumenBoccia = $resultadoBoccia ? $resultadoBoccia->fetch_assoc() : null;

    if ($resumen):
        $total_competiciones_slalom = (int) $resumen['total_competiciones_slalom'];
        $total_competiciones_boccia = (int) $resumen['total_competiciones_boccia'];
        $total_competiciones_deportes = $total_competiciones_slalom + $total_competiciones_boccia;
        ?>
        <div class="tablas-resumen">
            <table>
                <tr>
                    <th colspan="2">Medallas</th>
                </tr>
                <tr>
                    <th class=oro>Oro</th>
                    <td class=oro><?php echo (int) $resumen['oro']; ?></td>
                </tr>
                <tr>
                    <th class=plata>Plata</th>
                    <td class=plata><?php echo (int) $resumen['plata']; ?></td>
                </tr>
                <tr>
                    <th class=bronce>Bronce</th>
                    <td class=bronce><?php echo (int) $resumen['bronce']; ?></td>
                </tr>
                <tr>
                    <td>Participante (4º puesto o inferior)</td>
                    <td><?php echo (int) $resumen['total_participantes']; ?></td>
                </tr>
                <tr>
                    <th colspan="2">Total de medallas: <?php echo (int) $resumen['total_medallas']; ?></th>
                </tr>
                <tr>
                    <td colspan="2">
                        de Slalom:
                        <?php echo (int) (($resumenSlalom['oro'] ?? 0) + ($resumenSlalom['plata'] ?? 0) + ($resumenSlalom['bronce'] ?? 0)); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        de Boccia:
                        <?php echo (int) (($resumenBoccia['oro'] ?? 0) + ($resumenBoccia['plata'] ?? 0) + ($resumenBoccia['bronce'] ?? 0)); ?>
                    </td>
                </tr>
            </table>
            <?php
            $sqlPorAnioPosicion = "SELECT year, posicion, COUNT(*) AS total FROM medallas" . $where . " GROUP BY year, posicion ORDER BY year ASC";
            $resultadoPorAnioPosicion = $conexion->query($sqlPorAnioPosicion);
            $conteoPorAnioPosicion = [];
            while ($fila = $resultadoPorAnioPosicion->fetch_assoc()) {
                $conteoPorAnioPosicion[$fila['year']][strtolower($fila['posicion'])] = (int) $fila['total'];
            }
            $anios = range($year_inicio, $year_actual);

            $posiciones = ['oro' => 'O', 'plata' => 'P', 'bronce' => 'B'];
            ?>
            <?php if (!empty($anios)): ?>
                <table>
                    <tr>
                        <th>Año</th>
                        <?php foreach ($posiciones as $clave => $etiqueta): ?>
                            <th class="<?php echo $clave; ?>"><?php echo $etiqueta; ?></th>
                        <?php endforeach; ?>
                        <th>Total</th>
                    </tr>
                    <?php
                    $totalesPorPosicion = array_fill_keys(array_keys($posiciones), 0);
                    $granTotal = 0;
                    foreach ($anios as $anio):
                        $totalAnio = 0;
                        ?>
                        <tr>
                            <th><?php echo $anio; ?></th>
                            <?php foreach ($posiciones as $clave => $etiqueta):
                                $valor = $conteoPorAnioPosicion[$anio][$clave] ?? 0;
                                $totalAnio += $valor;
                                $totalesPorPosicion[$clave] += $valor;
                                ?>
                                <td class="<?php echo $clave; ?>" , style="width:50px"><?php echo $valor; ?></td>
                            <?php endforeach; ?>
                            <th><?php echo $totalAnio; ?></th>
                        </tr>
                        <?php
                        $granTotal += $totalAnio;
                    endforeach;
                    ?>
                    <tr>
                        <th colspan="4">Total de medallas</th>
                        <th style="width: 50px;"><?php echo $granTotal; ?></th>
                    </tr>
                </table>
            <?php endif; ?>
            <table>
                <tr>
                    <th>Total de competiciones: <?php echo $total_competiciones_deportes; ?></th>
                </tr>
                <tr>
                    <td>de Slalom: <?php echo $total_competiciones_slalom; ?></td>
                </tr>
                <tr>
                    <td>de Boccia: <?php echo $total_competiciones_boccia; ?></td>
                </tr>
                <tr>
                    <td>Autonómicos (Cataluña):
                        <?php echo ((int) $resumen['total_catalunya_slalom'] + (int) $resumen['total_catalunya_boccia']); ?>
                    </td>
                </tr>
                <tr>
                    <td>Nacionales (España):
                        <?php echo ((int) $resumen['total_espanna_slalom'] + (int) $resumen['total_espanna_boccia']); ?> · Jugados en Cataluña: <?php echo (int) $resumen['total_nacional_catalunya']; ?>
                    </td>
                </tr>
            </table>
        </div>
    <?php else: ?>
        <p>No se han encontrado resultados</p>
    <?php endif; ?>

    <script src="js/script.js?v=<?= filemtime(__DIR__ . '/js/script.js') ?>"></script>
</body>

</html>