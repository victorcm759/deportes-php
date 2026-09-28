<?php include 'conexion.php';
include 'funciones.php';

$participante = $_GET['participante'] ?? '';
$division = $_GET['division'] ?? '';

$sqlRivales = "SELECT DISTINCT participante FROM partidos WHERE division = 'Individual' ORDER BY participante ASC";
$resultadoRivales = $conexion->query($sqlRivales);

$sqlParejas = "SELECT DISTINCT participante FROM partidos WHERE division = 'Parejas' ORDER BY participante ASC";
$resultadoParejas = $conexion->query($sqlParejas);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Registro de partidos</title>
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
        <div class="menu-lateral-separador"></div>
        <ul class="menu-lateral-lista">
            <li><a href="#rival">Partidos por rival</a></li>
            <li><a href="#pareja">Partidos por pareja</a></li>
            <li><a href="#nacionales">Partidos entre jugadores nacionales</a></li>
            <li><a href="#comunidad-autonoma">Participantes por Comunidad Autónoma</a></li>
            <li><a href="#progresos">Progresos de temporada</a></li>
            <li><a href="https://docs.google.com/document/d/1CwKa4SaaCesZDvThIQ9_6vSuLaOvqAgOY2TYRmLJsfU/edit?usp=sharing">Documento de resultados</a></li>
        </ul>
    </nav>
    <header>
        <h1>Registro digitalizado de partidos</h1>
        <h2>Víctor Català Mendoza</h2>
        <button id="theme-toggle" type="button">Modo oscuro</button>
        <h3>Nota: A la izquierda de la columna 'Participante', es el color que uso, mientras que el otro es el de mi
            rival </h3>
    </header>

    <!-- FORMULARIO DE BÚSQUEDA -->
    <!-- <button type="button" id="toggle-filtros">Filtros</button> -->
    <div id="contenedor-filtros" class="filtros">
        <form method="GET">
            Tipo:
            <select name="tipo">
                <option value="">Seleccione uno...</option>
                <option value="autonomico">Autonómico</option>
                <option value="nacional">Nacional</option>
                <!-- <option value="internacional">Internacional</option> -->
            </select>

            División:
            <select name="division" id="division">
                <option value="">Seleccione uno...</option>
                <option value="individual" <?= $division === 'individual' ? 'selected' : '' ?>>Individual</option>
                <option value="parejas" <?= $division === 'parejas' ? 'selected' : '' ?>>Parejas</option>
            </select>

            <span id="label-participante-individual" class="<?= $division === 'individual' ? '' : 'oculto' ?>">Participante:</span>
            <span id="label-participante-parejas" class="<?= $division === 'parejas' ? '' : 'oculto' ?>">Pareja:</span>
            <select name="participante" id="participante-individual" class="<?= $division === 'individual' ? '' : 'oculto' ?>" <?= $division === 'individual' ? '' : 'disabled' ?>>
                <option value="">Todos</option>
                <?php while ($rival = $resultadoRivales->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($rival['participante']) ?>" <?= $participante === $rival['participante'] ? 'selected' : '' ?>><?= htmlspecialchars($rival['participante']) ?></option>
                <?php endwhile; ?>
            </select>
            <select name="participante" id="participante-parejas" class="<?= $division === 'parejas' ? '' : 'oculto' ?>" <?= $division === 'parejas' ? '' : 'disabled' ?>>
                <option value="">Todas</option>
                <?php while ($pareja = $resultadoParejas->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($pareja['participante']) ?>" <?= $participante === $pareja['participante'] ? 'selected' : '' ?>><?= htmlspecialchars($pareja['participante']) ?></option>
                <?php endwhile; ?>
            </select>

            Municipio:
            <input type="text" name="ubicacion" id="ubicacion" placeholder="Buscar por municipio">

            Provincia:
            <input type="text" name="provincia" id="provincia" placeholder="Buscar por provincia">

            CC.AA. / Estado:
            <input type="text" name="comunidad" id="comunidad" placeholder="Buscar por estado/comunidad autónoma">

            <!-- País:
            <input type="text" name="pais" id="pais" placeholder="Buscar por país">
            -->

            Fase:
            <select name="fase">
                <option value="">Elige fase</option>
                <option value="pool">Pool</option>
                <option value="eliminatoria">Eliminatoria</option>
                <option value="eliminatoria">Triangular</option>
            </select>
            Desde:
            <input type="date" name="desde">
            Hasta:
            <input type="date" name="hasta">
            Resultados:
            <select name="resultadoFinal">
                <option value="">Elige uno</option>
                <option value="victoria">Victoria</option>
                <option value="derrota">Derrota</option>
            </select>
            <input type="submit" value="Buscar">
            <button type="button" id="limpiar-filtros">Limpiar búsqueda</button>
        </form>
    </div>
    <?php
    // Construir consulta con filtros
    $tipo = $_GET['tipo'] ?? '';
    $ubicacion = $_GET['ubicacion'] ?? '';
    $provincia = $_GET['provincia'] ?? '';
    $comunidad = $_GET['comunidad'] ?? '';
    $pais = $_GET['pais'] ?? '';
    $posicion = $_GET['posicion'] ?? '';
    $fase = $_GET['fase'] ?? '';
    $desde = $_GET['desde'] ?? '';
    $hasta = $_GET['hasta'] ?? '';
    $resultPartido = $_GET['resultadoFinal'] ?? '';
    $whereSql = '';

    if (!empty($ubicacion)) {
        $whereSql .= " AND ubicacion LIKE '%" . $conexion->real_escape_string($ubicacion) . "%'";
    }
    if (!empty($provincia)) {
        $whereSql .= " AND provincia LIKE '%" . $conexion->real_escape_string($provincia) . "%'";
    }
    if (!empty($comunidad)) {
        $whereSql .= " AND comunidad LIKE '%" . $conexion->real_escape_string($comunidad) . "%'";
    }
    if (!empty($pais)) {
        $whereSql .= " AND pais LIKE '%" . $conexion->real_escape_string($pais) . "%'";
    }
    if (!empty($desde)) {
        $whereSql .= " AND fecha >= '$desde'";
    }

    if (!empty($hasta)) {
        $whereSql .= " AND fecha <= '$hasta'";
    }
    if (!empty($fase)) {
        $whereSql .= " AND fase = '" . $conexion->real_escape_string($fase) . "'";
    }
    if (!empty($resultPartido)) {
        $whereSql .= " AND resultadoFinal = '" . $conexion->real_escape_string($resultPartido) . "'";
    }
    if (!empty($participante)) {
        $whereSql .= " AND participante = '" . $conexion->real_escape_string($participante) . "'";
    }
    if (!empty($division)) {
        $whereSql .= " AND division = '" . $conexion->real_escape_string($division) . "'";
    }

    $sql = "SELECT * FROM partidos WHERE 1=1" . $whereSql;

    $resultado = $conexion->query($sql);
    $filasPartidos = [];
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $filasPartidos[] = $fila;
        }
    }

    $partidosEncontrados = count($filasPartidos);
    $victoriasEncontradas = count(array_filter($filasPartidos, fn($fila) => $fila['resultadoFinal'] === 'Victoria'));

    $temporadasResumen = [
        ['temporada' => '2024/2025', 'desempates' => 1, 'partidos' => 11, 'victorias' => 7, 'bolasFavor' => 41, 'bolasContra' => 59, 'posicion' => 14, 'totalRNB' => 22],
        ['temporada' => '2025/2026', 'desempates' => 0, 'partidos' => 19, 'victorias' => 10, 'bolasFavor' => 75, 'bolasContra' => 76, 'posicion' => 4, 'totalRNB' => 21],
        // ['temporada' => '2026/2027', 'desempates' => 0, 'partidos' => 0, 'victorias' => 0, 'bolasFavor' => 0, 'bolasContra' => 0, 'posicion' => null, 'totalRNB' => null],
        // ['temporada' => '20XX/20YY', 'desempates' => 0, 'partidos' => 0, 'victorias' => 0, 'bolasFavor' => 0, 'bolasContra' => 0, 'posicion' => null, 'totalRNB' => null],
    ];

    $partidosTotales = array_sum(array_column($temporadasResumen, 'partidos'));
    $desempates = array_sum(array_column($temporadasResumen, 'desempates'));
    $victorias = array_sum(array_column($temporadasResumen, 'victorias'));
    $bolasFavor = array_sum(array_column($temporadasResumen, 'bolasFavor'));
    $bolasContra = array_sum(array_column($temporadasResumen, 'bolasContra'));

    $sqlPartidosPorRival = "SELECT participante, COUNT(*) AS jugados, SUM(CASE WHEN resultadoFinal = 'Victoria' THEN 1 ELSE 0 END) AS victorias FROM partidos WHERE division = 'Individual' GROUP BY participante ORDER BY jugados DESC, victorias DESC, participante ASC";
    $resultadoPartidosPorRival = $conexion->query($sqlPartidosPorRival);
    $sqlPartidosPorPareja = "SELECT p.participante, eb.integrantes, COUNT(*) AS jugados, SUM(CASE WHEN p.resultadoFinal = 'Victoria' THEN 1 ELSE 0 END) AS victorias FROM partidos p LEFT JOIN equipos_boccia eb ON eb.nombre = p.participante WHERE p.division = 'Parejas' GROUP BY p.participante, eb.integrantes ORDER BY jugados DESC, victorias DESC, p.participante ASC";
    $resultadoPartidosPorPareja = $conexion->query($sqlPartidosPorPareja);
    $sqlPartidosNacionales = "SELECT p.participante, COUNT(*) AS jugados, SUM(CASE WHEN p.resultadoFinal = 'Victoria' THEN 1 ELSE 0 END) AS victorias FROM partidos p INNER JOIN participantes_nacionales pn ON pn.nombre = p.participante GROUP BY p.participante ORDER BY jugados DESC, victorias DESC, p.participante ASC";
    $resultadoPartidosNacionales = $conexion->query($sqlPartidosNacionales);

    $sqlCompetidoresNacionales = "SELECT nombre, club, provincia, comunidad, year FROM participantes_nacionales ORDER BY comunidad ASC";
    $resultadoCompetidoresNacionales = $conexion->query($sqlCompetidoresNacionales);
    $sqlCompetidoresComunidad = "SELECT comunidad, COUNT(*) AS total FROM participantes_nacionales GROUP BY comunidad ORDER BY total DESC, comunidad ASC";
    $resultadoCompetidoresComunidad = $conexion->query($sqlCompetidoresComunidad);
    $sqlEquiposBoccia = "SELECT nombre, integrantes, club, comunidad, year FROM equipos_boccia ORDER BY comunidad ASC";
    $resultadoEquiposBoccia = $conexion->query($sqlEquiposBoccia);

    if ($partidosEncontrados > 0): ?>
        <table>
            <tr>
                <th colspan="24">Resultado de la búsqueda: Partidos registrados: <?php echo $partidosEncontrados ?> · Partidos ganados: <?php echo $victoriasEncontradas ?></th>
            </tr>
            <tr>
                <th>Tipo</th>
                <th>División</th>
                <th colspan="3">Participante</th>
                <th>Fase</th>
                <th>Fecha</th>
                <th>Municipio</th>
                <th>Provincia</th>
                <th>CC.AA. / Estado</th>
                <!-- <th>País</th> -->
                <th colspan="2">P1</th>
                <th colspan="2">P2</th>
                <th colspan="2">P3</th>
                <th colspan="2">P4</th>
                <th colspan="2">PD</th>
                <th colspan="3">Resultado Final</th>
            </tr>
            <?php foreach ($filasPartidos as $fila): ?>
                <!-- <?php
                $clase = '';
                switch ($fila['miColor']) {
                    case 'Azul':
                        $clase = 'azul';
                        break;
                    case 'Rojo':
                        $clase = 'rojo';
                        break;
                }
                // $codigo = obtenerCodigoPais($fila['pais']);
                ?> -->
                <tr>
                    <td><?php echo $fila['tipo'] ?></td>
                    <td><?php echo $fila['division'] ?></td>
                    <td><?php echo $fila['participante'] ?></td>
                    <?php
                    $miColor = strtolower($fila['miColor']);
                    ?>
                    <td class="color-<?= $miColor ?>"></td>
                    <?php
                    $colorRival = strtolower($fila['colorRival']);
                    ?>
                    <td class="color-<?= $colorRival ?>"></td>
                    <td><?php echo $fila['fase'] ?></td>
                    <td><?php echo $fila['fecha'] ?></td>
                    <?php
                    if ($fila['ubicacion'] == $fila['provincia']) { // Barcelona, Girona, etc. que son municipios y provincias a la vez
                        echo '<td colspan="2">' . $fila['ubicacion'] . '</td>';
                        echo '<td>' . $fila['comunidad'] . '</td>';
                    } elseif ($fila['provincia'] == $fila['comunidad']) { // Madrid, Murcia, etc. que son provincias y comunidades autónomas a la vez
                        echo '<td colspan="2">' . $fila['provincia'] . '</td>';
                        echo '<td>' . $fila['comunidad'] . '</td>';
                    } else {
                        echo '<td>' . $fila['ubicacion'] . '</td>';
                        echo '<td>' . $fila['provincia'] . '</td>';
                        echo '<td>' . $fila['comunidad'] . '</td>';
                    }
                    $pais = $fila['pais'];
                    $codigo = obtenerCodigoPais($pais);
                    ?>
                    <!--<td class="pais">
                        <img class="bandera" src="https://flagcdn.com/h20/<?= $codigo ?>.png" alt="<?= $pais ?>">
                        <?= $pais ?>
                    </td>-->
                    <td class="color-rojo"><?php echo $fila['parcial1A'] ?></td>
                    <td class="color-azul"><?php echo $fila['parcial1B'] ?></td>
                    <td class="color-rojo"><?php echo $fila['parcial2A'] ?></td>
                    <td class="color-azul"><?php echo $fila['parcial2B'] ?></td>
                    <td class="color-rojo"><?php echo $fila['parcial3A'] ?></td>
                    <td class="color-azul"><?php echo $fila['parcial3B'] ?></td>
                    <td class="color-rojo"><?php echo $fila['parcial4A'] ?></td>
                    <td class="color-azul"><?php echo $fila['parcial4B'] ?></td>
                    <td class="color-gris"><?php echo $fila['desempateA'] ?></td>
                    <td class="color-gris"><?php echo $fila['desempateB'] ?></td>
                    <td class="color-rojo"><?php echo $fila['resultadoA'] ?></td>
                    <td class="color-azul"><?php echo $fila['resultadoB'] ?></td>
                    <td><?php echo $fila['resultadoFinal'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No se han encontrado resultados</p>
    <?php endif; ?>
    <p><br>P1 - P4: Parcial 1 - 4; PD = Parcial de Desempate<brh< /p>
            <h3><a id="rival"></a>Partidos por rival</h3>
            <?php if ($resultadoPartidosPorRival && $resultadoPartidosPorRival->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Nombre</th>
                        <th>Partidos jugados</th>
                        <th>Victorias</th>
                    </tr>
                    <?php while ($rivalResumen = $resultadoPartidosPorRival->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $rivalResumen['participante']; ?></td>
                            <td><?php echo $rivalResumen['jugados']; ?></td>
                            <td><?php echo $rivalResumen['victorias']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No hay partidos registrados</p>
            <?php endif; ?>
            <h3><a id="nacionales"></a>Partidos jugados entre jugadores nacionales</h3>
            <?php if ($resultadoPartidosNacionales && $resultadoPartidosNacionales->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Nombre</th>
                        <th>Partidos jugados</th>
                        <th>Victorias</th>
                    </tr>
                    <?php while ($nacionalResumen = $resultadoPartidosNacionales->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $nacionalResumen['participante']; ?></td>
                            <td><?php echo $nacionalResumen['jugados']; ?></td>
                            <td><?php echo $nacionalResumen['victorias']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No hay partidos registrados</p>
            <?php endif; ?>
            <h3><a id="comunidad-autonoma"></a>Participantes nacionales por Comunidad Autónoma</h3>
            <?php if ($resultadoCompetidoresComunidad && $resultadoCompetidoresComunidad->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Comunidad</th>
                        <th>Número de participantes</th>
                    </tr>
                    <?php while ($comunidadResumen = $resultadoCompetidoresComunidad->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $comunidadResumen['comunidad'] ?: 'Desconocida'; ?></td>
                            <td><?php echo $comunidadResumen['total']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No hay participantes nacionales registrados</p>
            <?php endif; ?>
            <h3>Competidores nacionales</h3>
            <?php if ($resultadoCompetidoresNacionales && $resultadoCompetidoresNacionales->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Nombre</th>
                        <th>Club</th>
                        <th>Provincia</th>
                        <th>Comunidad</th>
                        <th>Año</th>
                    </tr>
                    <?php while ($competidor = $resultadoCompetidoresNacionales->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $competidor['nombre']; ?></td>
                            <td><?php echo $competidor['club'] ?: 'desconocido'; ?></td>
                            <?php
                            if ($competidor['provincia'] == $competidor['comunidad']) {
                                echo '<td colspan="2">' . ($competidor['provincia'] ?: 'desconocido') . '</td>';
                            } else {
                                echo '<td>' . ($competidor['provincia'] ?: 'desconocido') . '</td>';
                                echo '<td>' . ($competidor['comunidad'] ?: '-') . '</td>';
                            }
                            ?>
                            <td><?php echo $competidor['year'] ?: '-'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No hay competidores nacionales registrados</p>
            <?php endif; ?>
            <h3>Equipos de boccia</h3>
            <?php if ($resultadoEquiposBoccia && $resultadoEquiposBoccia->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Nombre</th>
                        <th>Integrantes</th>
                        <th>Club/es</th>
                        <th>Comunidad</th>
                        <th>Año</th>
                    </tr>
                    <?php while ($equipo = $resultadoEquiposBoccia->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $equipo['nombre']; ?></td>
                            <td><?php echo $equipo['integrantes'] ?: '-'; ?></td>
                            <td><?php echo $equipo['club'] ?: '-'; ?></td>
                            <td><?php echo $equipo['comunidad'] ?: '-'; ?></td>
                            <td><?php echo $equipo['year'] ?: '-'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No hay equipos de boccia registrados</p>
            <?php endif; ?>
            <h3><a id="pareja"></a>Partidos por pareja</h3>
            <?php if ($resultadoPartidosPorPareja && $resultadoPartidosPorPareja->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Nombre pareja</th>
                        <th>Integrantes</th>
                        <th>Partidos jugados</th>
                        <th>Victorias</th>
                    </tr>
                    <?php while ($parejaResumen = $resultadoPartidosPorPareja->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $parejaResumen['participante']; ?></td>
                            <td><?php echo $parejaResumen['integrantes'] ?: '-'; ?></td>
                            <td><?php echo $parejaResumen['jugados']; ?></td>
                            <td><?php echo $parejaResumen['victorias']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No hay partidos registrados</p>
            <?php endif; ?>
            <h3><a id="progresos"></a>Progresos</h3>
            <table>
                <tr>
                    <th colspan="8">Estadísticas de la temporada<br>Última actualización: 14 de junio de 2026</th>
                </tr>
                <tr>
                    <th>Temporada</th>
                    <th>Desempates ganados</th>
                    <th>Partidos jugados</th>
                    <th>Partidos ganados</th>
                    <th>Diferencia de bolas</th>
                    <th>Bolas a favor</th>
                    <th>Bolas en contra</th>
                    <th>Posición en el RNB</th>
                </tr>
                <?php $posicionAnterior = null; ?>
                <?php foreach ($temporadasResumen as $temporada): ?>
                    <?php
                    $cambioPosicion = ($posicionAnterior !== null && $temporada['posicion'] !== null) ? $posicionAnterior - $temporada['posicion'] : null;
                    if ($temporada['posicion'] !== null) {
                        $posicionAnterior = $temporada['posicion'];
                    }
                    ?>
                    <tr>
                        <th><?= $temporada['temporada'] ?></th>
                        <td><?= $temporada['desempates'] ?></td>
                        <td><?= $temporada['partidos'] ?></td>
                        <td><?= $temporada['victorias'] ?></td>
                        <td><?= $temporada['bolasFavor'] - $temporada['bolasContra'] ?></td>
                        <td><?= $temporada['bolasFavor'] ?></td>
                        <td><?= $temporada['bolasContra'] ?></td>
                        <td><?= formatoPosicionRNB($temporada['posicion'], $temporada['totalRNB'], $cambioPosicion) ?></td>
                    </tr>
                <?php endforeach; ?>
                <!-- Subida: &#9650; -->
                <!-- Bajada: &#9660; -->
                <tr>
                    <th>TOTAL</th>
                    <th><?= $desempates ?></th>
                    <th><?= $partidosTotales ?></th>
                    <th><?= $victorias ?></th>
                    <th><?= $bolasFavor - $bolasContra ?></th>
                    <th><?= $bolasFavor ?></th>
                    <th><?= $bolasContra ?></th>
                    <th>TOTAL</th>
                </tr>
            </table>
            <script src="js/script.js?v=<?= filemtime(__DIR__ . '/js/script.js') ?>"></script>
</body>

</html>