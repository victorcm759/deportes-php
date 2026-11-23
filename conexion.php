<?php
// Conexión a PostgreSQL (Supabase) mediante PDO.
// Los datos de conexión se leen de config.php (ver config.example.php).
//
// $conexion imita la parte de mysqli que usa la web (query, real_escape_string,
// num_rows, fetch_assoc) para no tener que reescribir cada página.

$configFile = __DIR__ . '/config.php';
if (!file_exists($configFile)) {
    die('Error de conexión: falta config.php (copia config.example.php y rellénalo).');
}
$config = require $configFile;

class ResultadoConsulta
{
    // PostgreSQL devuelve los nombres de columna en minúsculas; se restauran
    // los nombres originales en camelCase que usa el código PHP.
    private const COLUMNAS = [
        'micolor' => 'miColor',
        'colorrival' => 'colorRival',
        'parcial1a' => 'parcial1A',
        'parcial1b' => 'parcial1B',
        'parcial2a' => 'parcial2A',
        'parcial2b' => 'parcial2B',
        'parcial3a' => 'parcial3A',
        'parcial3b' => 'parcial3B',
        'parcial4a' => 'parcial4A',
        'parcial4b' => 'parcial4B',
        'desempatea' => 'desempateA',
        'desempateb' => 'desempateB',
        'resultadoa' => 'resultadoA',
        'resultadob' => 'resultadoB',
        'resultadofinal' => 'resultadoFinal',
    ];

    public int $num_rows;
    private array $filas;
    private int $posicion = 0;

    public function __construct(PDOStatement $sentencia)
    {
        $this->filas = $sentencia->fetchAll(PDO::FETCH_ASSOC);
        $this->num_rows = count($this->filas);
    }

    public function fetch_assoc(): ?array
    {
        if ($this->posicion >= $this->num_rows) {
            return null;
        }
        $fila = [];
        foreach ($this->filas[$this->posicion++] as $columna => $valor) {
            $fila[self::COLUMNAS[$columna] ?? $columna] = $valor;
        }
        return $fila;
    }
}

class ConexionPostgres
{
    private PDO $pdo;

    public function __construct(array $config)
    {
        $dsn = sprintf(
            'pgsql:host=%s;port=%d;dbname=%s;sslmode=%s',
            $config['host'],
            $config['port'] ?? 5432,
            $config['database'] ?? 'postgres',
            $config['sslmode'] ?? 'require' // Supabase exige SSL
        );
        $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    // Devuelve el resultado, o false si la consulta falla (como mysqli).
    public function query(string $sql): ResultadoConsulta|false
    {
        try {
            return new ResultadoConsulta($this->pdo->query($sql));
        } catch (PDOException $e) {
            error_log('Error SQL: ' . $e->getMessage() . ' | ' . $sql);
            return false;
        }
    }

    // Escapa un texto para insertarlo entre comillas simples en una consulta.
    public function real_escape_string(string $texto): string
    {
        return substr($this->pdo->quote($texto), 1, -1);
    }
}

try {
    $conexion = new ConexionPostgres($config);
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}
?>
