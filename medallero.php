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
        
        <!-- País:
        <input type="text" name="pais" id="pais" placeholder="Buscar por país">
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
            $año_inicio = 2023;
            $año_actual = date('Y');

            for ($i = $año_inicio; $i <= $año_actual; $i++) {
                // Marcar el año seleccionado si ya estaba en la búsqueda
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
    $sql = "SELECT * FROM medallas WHERE 1=1";
    if (!empty($deporte)) {
        $sql .= " AND deporte LIKE '%" . $conexion->real_escape_string($deporte) . "%'";
    }
    if (!empty($lugar)) {
        $sql .= " AND lugar LIKE '%" . $conexion->real_escape_string($lugar) . "%'";
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
    if (!empty($posicion)) {
        $sql .= " AND posicion = '" . $conexion->real_escape_string($posicion) . "'";
    }
    if (!empty($year)) {
        $sql .= " AND year = " . intval($year);
    }
    if (!empty($tipo)) {
        $sql .= " AND tipo = '" . $conexion->real_escape_string($tipo) . "'";
    }


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
                <!-- <th>País</th> -->
                <th>Año</th>
            </tr>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <?php
                // Determinar clase CSS según el tipo de medalla
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
                // $codigo = obtenerCodigoPais($fila['pais']);
                ?>
                <tr>
                    <td><?php echo $fila['id']; ?></td>
                    <td><?php echo $fila['tipo']; ?></td>
                    <td class="<?php echo $fila['posicion']; ?>">
                        <?php echo iconoMedalla($fila['posicion']); ?>
                    </td>
                    <td><?php echo $fila['competicion']; ?></td>
                    <td><?php echo $fila['deporte']; ?></td>
                    <td><?php echo $fila['lugar']; ?></td>
                    <td><?php echo $fila['provincia']; ?></td>
                    <td><?php echo $fila['comunidad']; ?></td>
                    <!-- <?php
                    $pais = $fila['pais'];
                    $codigo = obtenerCodigoPais($pais);
                    ?>
                    <td>
                        <img src="https://flagcdn.com/h20/<?= $codigo ?>.png" alt="<?= $pais ?>"
                            style="vertical-align: middle;">
                        <?= $pais ?>
                    </td> -->
                    <td><?php echo $fila['year']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No se han encontrado resultados</p>
    <?php endif; ?>
    <script src="js/script.js"></script>
</body>

</html>