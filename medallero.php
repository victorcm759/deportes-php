<?php include 'conexion.php';
include 'funciones.php'; ?>

<!DOCTYPE html>
<html>

<head>
    <title>Registro de medallas</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="shortcut icon" href="images/circle-icon.png" type="image/x-icon">
</head>

<body>
    <header>
        <h1>Registro digitalizado de medallas</h1>
        <h2>Víctor Català Mendoza</h2>
        <button id="theme-toggle" type="button">Modo oscuro</button>
        <a href="index.php">Volver a inicio</a>
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
    <a href="index.php">&larr; Volver a inicio</a>
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

    $whereCompeticionesSlalom = " WHERE 1=1";
    if (!empty($tipo)) {
        $whereCompeticionesSlalom .= " AND tipo = '" . $conexion->real_escape_string($tipo) . "'";
    }
    if (!empty($lugar)) {
        $whereCompeticionesSlalom .= " AND lugar LIKE '%" . $conexion->real_escape_string($lugar) . "%'";
    }
    if (!empty($provincia)) {
        $whereCompeticionesSlalom .= " AND provincia LIKE '%" . $conexion->real_escape_string($provincia) . "%'";
    }
    if (!empty($comunidad)) {
        $whereCompeticionesSlalom .= " AND comunidad LIKE '%" . $conexion->real_escape_string($comunidad) . "%'";
    }
    if (!empty($posicion)) {
        $whereCompeticionesSlalom .= " AND posicion = '" . $conexion->real_escape_string($posicion) . "'";
    }
    if (!empty($year)) {
        $whereCompeticionesSlalom .= " AND year = " . intval($year);
    }

    $whereCompeticionesBoccia = " WHERE deporte = 'Boccia'";
    if (!empty($tipo)) {
        $whereCompeticionesBoccia .= " AND tipo = '" . $conexion->real_escape_string($tipo) . "'";
    }
    if (!empty($lugar)) {
        $whereCompeticionesBoccia .= " AND lugar LIKE '%" . $conexion->real_escape_string($lugar) . "%'";
    }
    if (!empty($provincia)) {
        $whereCompeticionesBoccia .= " AND provincia LIKE '%" . $conexion->real_escape_string($provincia) . "%'";
    }
    if (!empty($comunidad)) {
        $whereCompeticionesBoccia .= " AND comunidad LIKE '%" . $conexion->real_escape_string($comunidad) . "%'";
    }
    if (!empty($posicion)) {
        $whereCompeticionesBoccia .= " AND posicion = '" . $conexion->real_escape_string($posicion) . "'";
    }
    if (!empty($year)) {
        $whereCompeticionesBoccia .= " AND year = " . intval($year);
    }

    $sql = "SELECT * FROM medallas" . $where;
    $sqlResumen = "SELECT
        SUM(posicion = 'oro') AS oro,
        SUM(posicion = 'plata') AS plata,
        SUM(posicion = 'bronce') AS bronce,
        SUM(posicion IN ('participante')) AS total_participantes,
        SUM(posicion IN ('oro', 'plata', 'bronce')) AS total_medallas,
        (SELECT COUNT(DISTINCT competicion) FROM competiciones_slalom" . $whereCompeticionesSlalom . ") AS total_slalom,
        (SELECT COUNT(DISTINCT competicion) FROM medallas" . $whereCompeticionesBoccia . ") AS total_boccia,
        (SELECT COUNT(DISTINCT competicion) FROM competiciones_slalom" . $whereCompeticionesSlalom . " AND tipo = 'autonomico') AS total_catalunya_slalom,
        (SELECT COUNT(DISTINCT competicion) FROM medallas" . $whereCompeticionesBoccia . " AND tipo = 'autonomico') AS total_catalunya_boccia,
        (SELECT COUNT(DISTINCT competicion) FROM competiciones_slalom" . $whereCompeticionesSlalom . " AND tipo = 'nacional') AS total_espanna_slalom,
        (SELECT COUNT(DISTINCT competicion) FROM medallas" . $whereCompeticionesBoccia . " AND tipo = 'nacional') AS total_espanna_boccia,
        COUNT(*) AS total_registros
    FROM medallas" . $where . " AND posicion IN ('oro', 'plata', 'bronce', 'participante')";

    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Posición</th>
                <th>Competición</th>
                <th>Deporte</th>
                <th>Municipio</th>
                <th>Provincia</th>
                <th>CC.AA. / Estado</th>
                <!-- <th>Paí­s</th> -->
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
                    <td><?php echo $fila['deporte']; ?></td>
                    <?php
                    if ($fila['lugar'] == $fila['provincia']) {
                        echo '<td colspan="2">' . $fila['lugar'] . '</td>';
                        echo '<td>' . $fila['comunidad'] . '</td>';
                    } elseif ($fila['provincia'] == $fila['comunidad']) {
                        echo '<td colspan="2">' . $fila['provincia'] . '</td>';
                        // echo '<td>' . $fila['pais'] . '</td>';
                    } else {
                        echo '<td>' . $fila['lugar'] . '</td>';
                        echo '<td>' . $fila['provincia'] . '</td>';
                        echo '<td>' . $fila['comunidad'] . '</td>';
                    }
                    ?>
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

    if ($resumen):
        $total_competiciones_slalom = (int) $resumen['total_slalom'];
        $total_competiciones_boccia = (int) $resumen['total_boccia'];
        $total_competiciones_deportes = $total_competiciones_slalom + $total_competiciones_boccia;
        ?>
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
        </table>
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
                <td>en Cataluña: <?php echo ((int) $resumen['total_catalunya_slalom'] + (int) $resumen['total_catalunya_boccia']); ?></td>
            </tr>
            <tr>
                <td>en España: <?php echo ((int) $resumen['total_espanna_slalom'] + (int) $resumen['total_espanna_boccia']); ?></td>
            </tr>   
        </table>
    <?php else: ?>
        <p>No se han encontrado resultados</p>
    <?php endif; ?>

    <script src="js/script.js"></script>
</body>

</html>