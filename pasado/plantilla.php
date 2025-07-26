<?php include '../conexion.php';
include '../funciones.php'; ?>

<!DOCTYPE html>
<html>

<head>
    <title>Registro de partidos</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="shortcut icon" href="../images/circle-icon.png" type="image/x-icon">
</head>

<body>
    <header>
        <h1>Registro digitalizado de partidos</h1>
        <h2>Víctor Català Mendoza</h2>
        <h3>Nota: A la izquierda de la columna 'Participante', es el color que usé, mientras que el otro es el de mi rival
        </h3>
    </header>
    <a href="../index.php">&larr; Volver a inicio</a>
    <a href="../partidos.php">&larr; Volver a registros actuales</a>
    <a href="pasado.php">&larr; Volver a temporadas pasadas</a>
    <?php
    $tipo = $_GET['tipo'] ?? '';
    $ubicacion = $_GET['ubicacion'] ?? '';
    $provincia = $_GET['provincia'] ?? '';
    $comunidad = $_GET['comunidad'] ?? '';
    $pais = $_GET['pais'] ?? '';
    $posicion = $_GET['posicion'] ?? '';
    $fase = $_GET['fase'] ?? '';
    $fecha = $_GET['fecha'] ??'';
    $fechainicio = '2024-12-01';
    $fechafin = '2025-06-30';
    $inicio = date('Y-m-d', strtotime($fechainicio));
    $final = date('Y-m-d', strtotime($fechafin));
    $resultPartido = $_GET['resultadoFinal'] ?? '';
    $participante = $_GET['participante'] ?? '';
    $sql = "SELECT * FROM partidos WHERE fecha BETWEEN '$inicio' AND '$final'";


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
    <table>
        <tr>
            <th colspan="7">Estadísticas de la temporada (20XX/XY)<br>Última actualización: 24 de junio de 2025</th>
        </tr>
        <tr>
            <th>Desempates ganados</th>
            <th>Partidos jugados</th>
            <th>Partidos ganados</th>
            <th>Diferencia de bolas</th>
            <th>Bolas a favor</th>
            <th>Bolas en contra</th>
            <th>Posición en el RNB</th>
        </tr>
        <tr>
            <td>1</td>
            <td>11</td>
            <td>7</td>
            <td>-18</td>
            <td>41</td>
            <td>59</td>
            <td>14º / 22</td>
        </tr>
    </table>
    <script src="js/script.js"></script>
</body>

</html>