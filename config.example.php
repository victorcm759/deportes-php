<?php
// Copia este archivo como config.php y rellena los datos de tu proyecto de Supabase.
// Los encontrarás en Supabase: botón "Connect" (arriba) → "Session pooler".
// config.php está en .gitignore: no lo subas nunca al repositorio.

return [
    'host'     => 'aws-0-eu-west-1.pooler.supabase.com', // Host del Session pooler
    'port'     => 5432,
    'database' => 'postgres',
    'username' => 'postgres.TU_REFERENCIA_DE_PROYECTO',
    'password' => 'TU_CONTRASEÑA_DE_LA_BASE_DE_DATOS',

    // Próximas competiciones (página de inicio): en Google Calendar, Configuración →
    // tu calendario → Integrar el calendario → "Dirección secreta en formato iCal".
    // Es secreta: quien la tenga puede leer el calendario.
    'calendario_ics' => 'https://calendar.google.com/calendar/ical/.../private-.../basic.ics',
];
