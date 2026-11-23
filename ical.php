<?php
// Lectura de calendarios en formato iCal (.ics), como la "dirección secreta"
// de Google Calendar. Sin dependencias externas.

const ICAL_ZONA = 'Europe/Madrid';

/**
 * Descarga el .ics (o lo lee de la caché) y devuelve sus eventos.
 * Si Google no responde, se usa la última copia guardada aunque esté caducada.
 *
 * @return array|null Lista de eventos, o null si no hay datos disponibles.
 */
function leerCalendarioIcs(string $url, int $segundosCache = 600): ?array
{
    $cache = sys_get_temp_dir() . '/calendario-' . md5($url) . '.ics';
    $contenido = null;

    if (is_file($cache) && time() - filemtime($cache) < $segundosCache) {
        $contenido = file_get_contents($cache);
    } else {
        $descargado = descargarUrl($url);
        if ($descargado !== false && str_contains($descargado, 'BEGIN:VCALENDAR')) {
            $contenido = $descargado;
            @file_put_contents($cache, $contenido);
        } elseif (is_file($cache)) {
            $contenido = file_get_contents($cache);
        }
    }

    return $contenido === null ? null : analizarIcs($contenido);
}

/**
 * Descarga una URL. Usa los certificados del sistema operativo: el archivo de
 * certificados que trae XAMPP (2022) no reconoce el certificado actual de Google.
 * No muestra errores, para no revelar la URL secreta en la página.
 */
function descargarUrl(string $url): string|false
{
    if (!function_exists('curl_init')) {
        return @file_get_contents($url, false, stream_context_create(['http' => ['timeout' => 10]]));
    }
    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_OPTIONS => defined('CURLSSLOPT_NATIVE_CA') ? CURLSSLOPT_NATIVE_CA : 0,
    ]);
    $respuesta = curl_exec($curl);
    $codigo = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    return $codigo === 200 ? $respuesta : false;
}

/**
 * Convierte el texto de un .ics en una lista de eventos con las claves:
 * titulo, lugar, descripcion, inicio, fin (DateTimeImmutable), todoElDia (bool).
 * Los eventos cancelados se descartan. Los periódicos (RRULE) se muestran
 * solo con su primera fecha.
 */
function analizarIcs(string $ics): array
{
    $zona = new DateTimeZone(ICAL_ZONA);
    // Las líneas largas continúan en la siguiente con un espacio o tabulador al principio
    $lineas = explode("\n", preg_replace("/\r?\n[ \t]/", '', str_replace("\r\n", "\n", $ics)));

    $eventos = [];
    $actual = null;
    foreach ($lineas as $linea) {
        $linea = rtrim($linea, "\r");
        if ($linea === 'BEGIN:VEVENT') {
            $actual = [];
            continue;
        }
        if ($linea === 'END:VEVENT') {
            if ($actual !== null && isset($actual['DTSTART']) && ($actual['STATUS']['valor'] ?? '') !== 'CANCELLED') {
                $eventos[] = construirEvento($actual, $zona);
            }
            $actual = null;
            continue;
        }
        if ($actual === null || !preg_match('/^([A-Z-]+)((?:;[^:]*)?):(.*)$/', $linea, $m)) {
            continue;
        }
        $parametros = [];
        foreach (array_filter(explode(';', $m[2])) as $parametro) {
            [$clave, $valor] = array_pad(explode('=', $parametro, 2), 2, '');
            $parametros[strtoupper($clave)] = trim($valor, '"');
        }
        $actual[$m[1]] = ['valor' => $m[3], 'parametros' => $parametros];
    }

    usort($eventos, fn($a, $b) => $a['inicio'] <=> $b['inicio']);
    return $eventos;
}

function construirEvento(array $propiedades, DateTimeZone $zona): array
{
    $inicio = fechaIcs($propiedades['DTSTART'], $zona);
    $todoElDia = ($propiedades['DTSTART']['parametros']['VALUE'] ?? '') === 'DATE'
        || strlen($propiedades['DTSTART']['valor']) === 8;

    if (isset($propiedades['DTEND'])) {
        $fin = fechaIcs($propiedades['DTEND'], $zona);
        // En los eventos de día completo, la fecha de fin es el día siguiente al último
        if ($todoElDia) {
            $fin = $fin->modify('-1 day');
        }
    } else {
        $fin = $inicio;
    }

    return [
        'titulo' => textoIcs($propiedades['SUMMARY']['valor'] ?? '(Sin título)'),
        'lugar' => textoIcs($propiedades['LOCATION']['valor'] ?? ''),
        // Google Calendar guarda la descripción con etiquetas HTML (<br>, <b>…): se dejan solo los saltos de línea
        'descripcion' => trim(html_entity_decode(strip_tags(preg_replace('#<br\s*/?>|</p>#i', "\n", textoIcs($propiedades['DESCRIPTION']['valor'] ?? ''))), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
        'inicio' => $inicio,
        'fin' => max($inicio, $fin),
        'todoElDia' => $todoElDia,
    ];
}

// Fechas: 20261205 (día completo), 20261205T090000Z (UTC) o con TZID
function fechaIcs(array $propiedad, DateTimeZone $zona): DateTimeImmutable
{
    $valor = $propiedad['valor'];
    if (strlen($valor) === 8) {
        return DateTimeImmutable::createFromFormat('!Ymd', $valor, $zona);
    }
    if (str_ends_with($valor, 'Z')) {
        $fecha = DateTimeImmutable::createFromFormat('Ymd\THis\Z', $valor, new DateTimeZone('UTC'));
    } else {
        try {
            $zonaEvento = new DateTimeZone($propiedad['parametros']['TZID'] ?? ICAL_ZONA);
        } catch (Exception $e) {
            $zonaEvento = $zona;
        }
        $fecha = DateTimeImmutable::createFromFormat('Ymd\THis', $valor, $zonaEvento);
    }
    return $fecha->setTimezone($zona);
}

// Deshace los escapes de iCal (\n, \, \; \\)
function textoIcs(string $texto): string
{
    return strtr($texto, ['\\n' => "\n", '\\N' => "\n", '\\,' => ',', '\\;' => ';', '\\\\' => '\\']);
}

/**
 * Competiciones del calendario configurado en config.php ('calendario_ics')
 * que aún no han terminado, ordenadas por fecha.
 *
 * @return array|null Lista de eventos ([] si no hay ninguna), o null si el
 *                    calendario no está configurado o no se ha podido cargar.
 */
function proximasCompeticiones(): ?array
{
    $config = is_file(__DIR__ . '/config.php') ? require __DIR__ . '/config.php' : [];
    $url = $config['calendario_ics'] ?? '';
    $eventos = $url !== '' ? leerCalendarioIcs($url) : null;
    if ($eventos === null) {
        return null;
    }

    $hoy = new DateTimeImmutable('today', new DateTimeZone(ICAL_ZONA));
    return array_values(array_filter($eventos, fn($evento) => $evento['fin']->setTime(0, 0) >= $hoy));
}

// ---------------------------------------------------------------------------
// Datos de competición a partir del título y la ubicación del evento
// ---------------------------------------------------------------------------

const DEPORTES_CONOCIDOS = ['Boccia', 'Slalom'];

// Primeras dos cifras del código postal → provincia
const PROVINCIAS_POR_CP = [
    '01' => 'Álava', '02' => 'Albacete', '03' => 'Alicante', '04' => 'Almería', '05' => 'Ávila',
    '06' => 'Badajoz', '07' => 'Islas Baleares', '08' => 'Barcelona', '09' => 'Burgos', '10' => 'Cáceres',
    '11' => 'Cádiz', '12' => 'Castellón', '13' => 'Ciudad Real', '14' => 'Córdoba', '15' => 'A Coruña',
    '16' => 'Cuenca', '17' => 'Girona', '18' => 'Granada', '19' => 'Guadalajara', '20' => 'Gipuzkoa',
    '21' => 'Huelva', '22' => 'Huesca', '23' => 'Jaén', '24' => 'León', '25' => 'Lleida',
    '26' => 'La Rioja', '27' => 'Lugo', '28' => 'Madrid', '29' => 'Málaga', '30' => 'Murcia',
    '31' => 'Navarra', '32' => 'Ourense', '33' => 'Asturias', '34' => 'Palencia', '35' => 'Las Palmas',
    '36' => 'Pontevedra', '37' => 'Salamanca', '38' => 'Santa Cruz de Tenerife', '39' => 'Cantabria', '40' => 'Segovia',
    '41' => 'Sevilla', '42' => 'Soria', '43' => 'Tarragona', '44' => 'Teruel', '45' => 'Toledo',
    '46' => 'Valencia', '47' => 'Valladolid', '48' => 'Bizkaia', '49' => 'Zamora', '50' => 'Zaragoza',
    '51' => 'Ceuta', '52' => 'Melilla',
];

// Provincia (sin tildes, en minúsculas, con sus variantes) → comunidad autónoma
const COMUNIDADES_POR_PROVINCIA = [
    'Andalucía' => ['almeria', 'cadiz', 'cordoba', 'granada', 'huelva', 'jaen', 'malaga', 'sevilla'],
    'Aragón' => ['huesca', 'teruel', 'zaragoza'],
    'Asturias' => ['asturias'],
    'Islas Baleares' => ['islas baleares', 'illes balears', 'baleares'],
    'Islas Canarias' => ['las palmas', 'las palmas de gran canaria', 'santa cruz de tenerife'],
    'Cantabria' => ['cantabria'],
    'Castilla y León' => ['avila', 'burgos', 'leon', 'palencia', 'salamanca', 'segovia', 'soria', 'valladolid', 'zamora'],
    'Castilla-La Mancha' => ['albacete', 'ciudad real', 'cuenca', 'guadalajara', 'toledo'],
    'Cataluña' => ['barcelona', 'girona', 'gerona', 'lleida', 'lerida', 'tarragona'],
    'Comunidad Valenciana' => ['alicante', 'alacant', 'castellon', 'castello', 'valencia'],
    'Extremadura' => ['badajoz', 'caceres'],
    'Galicia' => ['a coruna', 'la coruna', 'lugo', 'ourense', 'orense', 'pontevedra'],
    'Comunidad de Madrid' => ['madrid'],
    'Región de Murcia' => ['murcia'],
    'Navarra' => ['navarra'],
    'País Vasco' => ['alava', 'araba', 'gipuzkoa', 'guipuzcoa', 'bizkaia', 'vizcaya'],
    'La Rioja' => ['la rioja'],
    'Ceuta' => ['ceuta'],
    'Melilla' => ['melilla'],
];

function sinTildes(string $texto): string
{
    return mb_strtolower(strtr($texto, [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n', 'à' => 'a', 'è' => 'e', 'ò' => 'o', 'ç' => 'c',
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ñ' => 'N', 'À' => 'A', 'È' => 'E', 'Ò' => 'O', 'Ç' => 'C',
    ]));
}

function comunidadDeProvincia(string $provincia): ?string
{
    $clave = sinTildes(trim($provincia));
    foreach (COMUNIDADES_POR_PROVINCIA as $comunidad => $provincias) {
        if (in_array($clave, $provincias, true)) {
            return $comunidad;
        }
    }
    return null;
}

/**
 * "Boccia: Liga Catalana (Jornada 1)" o "Boccia - Concentración" → ['Boccia', 'Liga Catalana (Jornada 1)'].
 * Si el título no empieza por el deporte, se busca el nombre del deporte dentro del título.
 */
function separarDeporte(string $titulo): array
{
    if (preg_match('/^(\S+)\s*(?::|\s[-–])\s*(.+)$/u', $titulo, $m)) {
        return [$m[1], $m[2]];
    }
    foreach (DEPORTES_CONOCIDOS as $deporte) {
        if (stripos($titulo, $deporte) !== false) {
            return [$deporte, $titulo];
        }
    }
    return ['', $titulo];
}

/**
 * Ubicación de Google Calendar → ['sede' => municipio, 'provincia' => ..., 'comunidad' => ...].
 * Ejemplos: "Molins de Rei, Barcelona, España",
 *           "La Pobla de Mafumet, 43140 La Pobla de Mafumet, Tarragona, España",
 *           "CEM Mundet, Carrer ..., 08035 Barcelona, España", "Sevilla, España".
 */
function separarUbicacion(string $lugar): array
{
    $partes = array_values(array_filter(array_map('trim', explode(',', $lugar)), 'strlen'));
    if ($partes && sinTildes(end($partes)) === 'espana') {
        array_pop($partes);
    }
    if (!$partes) {
        return ['sede' => '', 'provincia' => '', 'comunidad' => ''];
    }

    $sede = $partes[0];
    $provincia = count($partes) > 1 ? end($partes) : '';

    // Si hay código postal, el municipio va detrás y la provincia sale de sus dos primeras cifras
    foreach ($partes as $parte) {
        if (preg_match('/^(\d{2})\d{3}\s+(.+)$/', $parte, $m)) {
            $sede = $m[2];
            $provincia = PROVINCIAS_POR_CP[$m[1]] ?? $provincia;
            break;
        }
    }
    // Ubicación de una sola palabra que ya es una provincia ("Sevilla", "Valencia")
    if ($provincia === '' && comunidadDeProvincia($sede) !== null) {
        $provincia = $sede;
    }

    return [
        'sede' => $sede,
        'provincia' => $provincia,
        'comunidad' => $provincia !== '' ? (comunidadDeProvincia($provincia) ?? '') : '',
    ];
}

// "En curso", "Mañana" o "5 días"
function textoFaltan(array $evento): string
{
    $hoy = new DateTimeImmutable('today', new DateTimeZone(ICAL_ZONA));
    $inicio = $evento['inicio']->setTime(0, 0);
    if ($inicio <= $hoy) {
        return 'En curso';
    }
    $dias = (int) $hoy->diff($inicio)->days;
    return $dias === 1 ? 'Mañana' : $dias . ' días';
}

// "Todo el día", "10:00" o "10:00 – 14:00"
function textoHorario(array $evento): string
{
    if ($evento['todoElDia']) {
        return 'Todo el día';
    }
    $horario = $evento['inicio']->format('H:i');
    if ($evento['fin'] > $evento['inicio'] && $evento['fin']->format('Y-m-d') === $evento['inicio']->format('Y-m-d')) {
        $horario .= ' – ' . $evento['fin']->format('H:i');
    }
    return $horario;
}

// "5 de diciembre de 2026" o "5–7 de diciembre de 2026"
function formatoFechasEvento(array $evento): string
{
    $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    $i = $evento['inicio'];
    $f = $evento['fin'];
    $dia = fn($d) => $d->format('j') . ' de ' . $meses[$d->format('n') - 1];

    if ($i->format('Y-m-d') === $f->format('Y-m-d')) {
        return $dia($i) . ' de ' . $i->format('Y');
    }
    if ($i->format('Y-m') === $f->format('Y-m')) {
        return $i->format('j') . '–' . $dia($f) . ' de ' . $f->format('Y');
    }
    if ($i->format('Y') === $f->format('Y')) {
        return $dia($i) . ' – ' . $dia($f) . ' de ' . $f->format('Y');
    }
    return $dia($i) . ' de ' . $i->format('Y') . ' – ' . $dia($f) . ' de ' . $f->format('Y');
}
