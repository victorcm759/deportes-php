<?php
function iconoMedalla($posicion)
{
    switch ($posicion) {
        case 'oro':
            return '🥇 Oro';
        case 'plata':
            return '🥈 Plata';
        case 'bronce':
            return '🥉 Bronce';
        default:
            return ucfirst($posicion);
    }
}

function formatoPosicionRNB($posicion, $totalRNB, $cambioPosicion = null)
{
    if ($posicion === null || $totalRNB === null) {
        return '-';
    }

    $texto = $posicion . 'º / ' . $totalRNB;

    if (!empty($cambioPosicion)) {
        $flecha = $cambioPosicion > 0 ? '&#9650;' : '&#9660;';
        $texto .= ' (' . $flecha . ' ' . abs($cambioPosicion) . ')';
    }

    return $texto;
}

function obtenerCodigoPais($nombre)
{
    static $mapa = null;

    if ($mapa === null) {
        $json = file_get_contents('js/paises.json');
        $mapa = json_decode($json, true);
    }

    return $mapa[$nombre] ?? 'xx';
}
?>