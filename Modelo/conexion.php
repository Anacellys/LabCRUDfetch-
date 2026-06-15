<?php
/**
 * CLASE DB - Gestor de Conexión a Base de Datos
 * Utiliza PDO para conexión segura y consultas preparadas
 * Implementa patrón Singleton para una única instancia
 */

class DB {

    // Propiedades privadas para la configuración
    private $host = 'localhost';
    private $usuario = 'root';
    private $contraseña = '';
    private $basedatos = 'productosdb';

    // Instancia única de la conexión (Singleton)
    private static $instancia = null;

    // Objeto PDO para la conexión
    private $conexion;

    /**
     * Constructor privado (patrón Singleton)
     * Inicializa la conexión con PDO
     */
    protected function __construct() {
        try {
            // DSN (Data Source Name) - especifica el tipo de BD y ubicación
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->basedatos . ";charset=utf8mb4";

            // Opciones de PDO
            $opciones = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            );

            // Crear instancia de PDO
            $this->conexion = new PDO($dsn, $this->usuario, $this->contraseña, $opciones);

        } catch (PDOException $e) {
            // Manejar errores de conexión
            die('Error de conexión: ' . $e->getMessage());
        }
    }

    /**
     * Método estático para obtener la instancia única
     * Patrón Singleton: garantiza una sola conexión activa
     *
     * @return DB instancia de la clase DB
     */
    public static function conectar() {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    /**
     * Obtener la conexión PDO
     * Utilizado por otras clases para ejecutar consultas
     *
     * @return PDO objeto de conexión
     */
    public function getConexion() {
        return $this->conexion;
    }

    /**
     * Ejecutar una consulta preparada
     *
     * @param string $sql Consulta SQL con placeholders
     * @param array $parametros Valores para los placeholders
     * @return PDOStatement resultado de la consulta
     */
    public function ejecutar($sql, $parametros = array()) {
        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($parametros);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception('Error en consulta: ' . $e->getMessage());
        }
    }

    /**
     * Obtener una fila de una consulta
     *
     * @param string $sql Consulta SQL
     * @param array $parametros Parámetros de la consulta
     * @return array fila de resultados
     */
    public function obtenerFila($sql, $parametros = array()) {
        $stmt = $this->ejecutar($sql, $parametros);
        return $stmt->fetch();
    }

    /**
     * Obtener todas las filas de una consulta
     *
     * @param string $sql Consulta SQL
     * @param array $parametros Parámetros de la consulta
     * @return array arreglo de filas
     */
    public function obtenerTodas($sql, $parametros = array()) {
        $stmt = $this->ejecutar($sql, $parametros);
        return $stmt->fetchAll();
    }

    /**
     * Obtener el ID del último registro insertado
     *
     * @return string ID del último registro
     */
    public function ultimoId() {
        return $this->conexion->lastInsertId();
    }

    // Evitar clonación (Singleton)
    private function __clone() {}

    // Evitar unserialization (Singleton)
    private function __wakeup() {}
}
?>
