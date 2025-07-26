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
        <h3>Nota: A la izquierda de la columna 'Participante', es el color que usé, mientras que el otro es el de mi rival
        </h3>
        <a href="pasado/pasado.php">Consultar registros de temporadas pasadas</a> - <a href="#progresos">Progresos de temporada</a>
    </header>

    <!-- FORMULARIO DE BÚSQUEDA -->
    <!-- <button type="button" id="toggle-filtros">🔍 Filtros</button> -->
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

            <!-- CC.AA. / Estado:
            <input type="text" name="comunidad" id="comunidad" placeholder="Buscar por estado/comunidad autónoma"> -->

            <!-- País:
            <input type="text" name="pais" id="pais" placeholder="Buscar por país">
            -->

            Fase:
            <select name="fase">
                <option value="">Elige fase</option>
                <option value="pool">Pool</option>
                <option value="eliminatoria">Eliminatoria</option>
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
    $sql = "SELECT * FROM partidos WHERE 1=1";
    if (!empty($ubicacion)) {
        $sql .= " AND ubicacion LIKE '%" . $conexion->real_escape_string($ubicacion) . "%'";
    }
    if (!empty($provincia)) {
        $sql .= " AND provincia LIKE '%" . $conexion->real_escape_string($provincia) . "%'";
    }
    if (!empty($comunidad)) {
        $sql .= " AND comunidad LIKE '%" . $conexion->real_escape_string($comunidad) . "%'";
    }
    if (!empty($pais)) {
        $sql .= " AND pais LIKE '%" . $conexion->real_escape_string($pais) . "%'";
    }
    if (!empty($desde)) {
        $sql .= " AND fecha >= '$desde'";
    }

    if (!empty($hasta)) {
        $sql .= " AND fecha <= '$hasta'";
    }
    if (!empty($fase)) {
        $sql .= " AND fase = '" . $conexion->real_escape_string($fase) . "'";
    }
    if (!empty($resultPartido)) {
        $sql .= " AND resultadoFinal = '" . $conexion->real_escape_string($resultPartido) . "'";
    }
    if (!empty($participante)) {
        $sql .= " AND participante LIKE '%" . $conexion->real_escape_string($participante) . "%'";
    }


    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0): ?>
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
                <!-- <th>País</th> -->
                <th colspan="2">Parcial 1</th>
                <th colspan="2">Parcial 2</th>
                <th colspan="2">Parcial 3</th>
                <th colspan="2">Parcial 4</th>
                <th colspan="2">Desempate</th>
                <th colspan="2">Resultado</th>
                <th>Final</th>
            </tr>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
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
                    <td><?php echo $fila['ubicacion'] ?></td>
                    <td><?php echo $fila['provincia'] ?></td>
                    <td><?php echo $fila['comunidad'] ?></td>
                    <!-- <?php
                    $pais = $fila['pais'];
                    $codigo = obtenerCodigoPais($pais);
                    ?>
                    <td>
                        <img src="https://flagcdn.com/h20/<?= $codigo ?>.png" alt="<?= $pais ?>"
                        style="vertical-align: middle;">
                        <?= $pais ?>
                    </td> -->
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
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No se han encontrado resultados</p>
    <?php endif; ?>
    <h3><a id="progresos"></a>Progresos</h3>
    <table>
        <tr>
            <th colspan="8">Estadísticas de la temporada<br>Última actualización: 24 de junio de 2025</th>
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
        <!-- <tr>
            <th>20XX/XY</th>
            <td>1</td>
            <td>11</td>
            <td>7</td>
            <td>-18</td>
            <td>41</td>
            <td>59</td>
            <td>14º / 22</td>
        </tr> -->
    </table>
    <script src="js/script.js"></script>
</body>

</html>