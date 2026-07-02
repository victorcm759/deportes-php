<?php include 'conexion.php';
include 'funciones.php'; ?>

<!DOCTYPE html>
<html>

<head>
    <title>Registro de partidos</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="shortcut icon" href="images/circle-icon.png" type="image/x-icon">
</head>

<body>
    <header>
        <h1>Registro digitalizado de partidos</h1>
        <h2>Víctor Català Mendoza</h2>
        <button id="theme-toggle" type="button">Modo oscuro</button>
        <h3>Nota: A la izquierda de la columna 'Participante', es el color que uso, mientras que el otro es el de mi rival </h3>
        <div class="header-links">
            <a href="index.php">Volver a inicio</a>
            <span>&ndash;</span>
            <a href="pasado/pasado.php">Consultar registros de temporadas pasadas</a>
            <span>&ndash;</span>
            <a href="#progresos">Progresos de temporada</a>
            <span>&ndash;</span>
            <a href="https://docs.google.com/document/d/1CwKa4SaaCesZDvThIQ9_6vSuLaOvqAgOY2TYRmLJsfU/edit?usp=sharing">Documento de resultados</a>
        </div>
    </header>

    <!-- FORMULARIO DE BÃšSQUEDA -->
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

            Participante:
            <input type="text" name="participante" id="participante" placeholder="Buscar por rival" autocomplete="off">
            <div id="sugerencias-participantes" class="sugerencias"></div>

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
    <a href="index.php">&larr; Volver a inicio</a>
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
    $participante = $_GET['participante'] ?? '';
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
        $whereSql .= " AND participante LIKE '%" . $conexion->real_escape_string($participante) . "%'";
    }

    $sql = "SELECT * FROM partidos WHERE 1=1" . $whereSql;

    $resultado = $conexion->query($sql);
    $filasPartidos = [];
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $filasPartidos[] = $fila;
        }
    }

    $temporadasResumen = [
        ['temporada' => '2024/2025', 'desempates' => 1,'partidos' => 11, 'victorias' => 7, 'bolasFavor' => 41, 'bolasContra' => 59],
        ['temporada' => '2025/2026', 'desempates' => 0, 'partidos' => 19, 'victorias' => 10, 'bolasFavor' => 75, 'bolasContra' => 76],
        // ['temporada' => '2026/2027', 'desempates' => 0, 'partidos' => 0, 'victorias' => 0, 'bolasFavor' => 0, 'bolasContra' => 0],
    ];

    $partidosTotales = array_sum(array_column($temporadasResumen, 'partidos'));
    $desempates = array_sum(array_column($temporadasResumen, 'desempates'));
    $victorias = array_sum(array_column($temporadasResumen, 'victorias'));
    $bolasFavor = array_sum(array_column($temporadasResumen, 'bolasFavor'));
    $bolasContra = array_sum(array_column($temporadasResumen, 'bolasContra'));

    $sqlCompetidoresNacionales = "SELECT nombre, club, provincia, comunidad, year FROM participantes_nacionales ORDER BY comunidad ASC";
    $resultadoCompetidoresNacionales = $conexion->query($sqlCompetidoresNacionales);
    $sqlEquiposBoccia = "SELECT nombre, integrantes, comunidad, year FROM equipos_boccia ORDER BY comunidad ASC";
    $resultadoEquiposBoccia = $conexion->query($sqlEquiposBoccia);

    if ($partidosTotales > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th colspan="3">Participante</th>
                <th>Fase</th>
                <th>Fecha</th>
                <th>Municipio</th>
                <th>Provincia</th>
                <th>CC.AA. / Estado</th>
                <!-- <th>Paí­s</th> -->
                <th colspan="2">Parcial 1</th>
                <th colspan="2">Parcial 2</th>
                <th colspan="2">Parcial 3</th>
                <th colspan="2">Parcial 4</th>
                <th colspan="2">Desempate</th>
                <th colspan="2">Resultado</th>
                <th>Final</th>
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
                    <td><?php echo $fila['id'] ?></td>
                    <td><?php echo $fila['tipo'] ?></td>
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
                    <!-- <?php
                    $pais = $fila['pais'];
                    $codigo = obtenerCodigoPais($pais);
                    ?>
                    <td>
                        <img src="https://flagcdn.com/h20/<?= $codigo ?>.png" alt="<?= $pais ?>"
                        style="vertical-align: middle;">
                        <?= $pais ?>
                    </td> -->
                    <?php
                    if ($fila['ubicacion'] == $fila['provincia']) { // Barcelona, Girona, etc. que son municipios y provincias a la vez
                        echo '<td colspan="2">' . $fila['ubicacion'] . '</td>';
                        echo '<td>' . $fila['comunidad'] . '</td>';
                    } elseif ($fila['provincia'] == $fila['comunidad']) { // Madrid, Murcia, etc. que son provincias y comunidades autónomas a la vez
                        echo '<td colspan="2">' . $fila['provincia'] . '</td>';
                        echo '<td>' . $fila['comunidad'] . '</td>';
                    } /* elseif ($fila['comunidad'] == $fila['pais']) { // Ciudades estado como Singapur, etc.
                       echo '<td colspan="3">' . $fila['comunidad'] . '</td>';
                   }*/ else {
                        echo '<td>' . $fila['ubicacion'] . '</td>';
                        echo '<td>' . $fila['provincia'] . '</td>';
                        echo '<td>' . $fila['comunidad'] . '</td>';
                        // echo "<td>" . $fila[$pais] . "</td>";
                    }
                    ?>
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
                <th>Comunidad</th>
                <th>Año</th>
            </tr>
            <?php while ($equipo = $resultadoEquiposBoccia->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $equipo['nombre']; ?></td>
                    <td><?php echo $equipo['integrantes'] ?: '-'; ?></td>
                    <td><?php echo $equipo['comunidad'] ?: '-'; ?></td>
                    <td><?php echo $equipo['year'] ?: '-'; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No hay equipos de boccia registrados</p>
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
        <tr>
            <th>2024/2025</th>
            <td>1</td>
            <td>11</td>
            <td>7</td>
            <td>-18</td>
            <td>41</td>
            <td>59</td>
            <td>14º / 22</td>
        </tr>
        <tr>
            <th>2025/2026</th>
            <td>0</td>
            <td>19</td>
            <td>10</td>
            <td>-1</td>
            <td>75</td>
            <td>76</td>
            <td>6º / 26 (&#9650; 8)</td>
        </tr>
        <!-- <tr>
            <th>2026/2027</th>
            <td>0</td>
            <td>19</td>
            <td>10</td>
            <td>-1</td>
            <td>75</td>
            <td>76</td>
            <td>6º / 26 ()</td>
        </tr> -->
        <!-- Subida: &#9650; -->
        <!-- Bajada: &#9660; -->
        <tr>
            <th>TOTAL</th>
            <td><?= $desempates ?></td>
            <td><?= $partidosTotales ?></td>
            <td><?= $victorias ?></td>
            <td><?= $bolasFavor - $bolasContra ?></td>
            <td><?= $bolasFavor ?></td>
            <td><?= $bolasContra ?></td>
            <td>-</td>
        </tr>
    </table>
    <script src="js/script.js"></script>
</body>

</html>