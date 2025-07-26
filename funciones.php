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