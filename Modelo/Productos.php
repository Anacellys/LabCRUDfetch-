<?php
/**
 * CLASE PRODUCTO - Modelado de Producto
 * Hereda de DB y contiene lógica CRUD con validaciones
 * Responde con arrays (convertidos a JSON en registrar.php)
 */

require_once 'conexion.php';

class Producto extends DB {

    // Propiedades privadas del producto
    private $id;
    private $codigo;
    private $producto;
    private $precio;
    private $cantidad;

    // Arreglo de errores de validación
    private $errores = array();

    /**
     * Constructor
     * Inicializa todas las propiedades en null
     */
    public function __construct() {
        parent::__construct();
        $this->id = null;
        $this->codigo = null;
        $this->producto = null;
        $this->precio = null;
        $this->cantidad = null;
        $this->errores = array();
    }

    // ==================== SETTERS ====================

    /**
     * Establecer ID del producto
     * @param int $id
     */
    public function setId($id) {
        $this->id = intval($id);
    }

    /**
     * Establecer código del producto
     * Limpia espacios en blanco y convierte a mayúsculas
     * @param string $codigo
     */
    public function setCodigo($codigo) {
        $this->codigo = strtoupper(trim($codigo));
    }

    /**
     * Establecer nombre del producto
     * @param string $producto
     */
    public function setProducto($producto) {
        $this->producto = trim($producto);
    }

    /**
     * Establecer precio del producto
     * @param float $precio
     */
    public function setPrecio($precio) {
        $this->precio = floatval($precio);
    }

    /**
     * Establecer cantidad disponible
     * @param int $cantidad
     */
    public function setCantidad($cantidad) {
        $this->cantidad = intval($cantidad);
    }

    // ==================== GETTERS ====================

    /**
     * Obtener ID
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Obtener código
     * @return string
     */
    public function getCodigo() {
        return $this->codigo;
    }

    /**
     * Obtener nombre del producto
     * @return string
     */
    public function getProducto() {
        return $this->producto;
    }

    /**
     * Obtener precio
     * @return float
     */
    public function getPrecio() {
        return $this->precio;
    }

    /**
     * Obtener cantidad
     * @return int
     */
    public function getCantidad() {
        return $this->cantidad;
    }

    /**
     * Obtener arreglo de errores
     * @return array
     */
    public function getErrores() {
        return $this->errores;
    }

    // ==================== VALIDACIONES ====================

    /**
     * Validar todos los campos del producto
     * Aplica reglas de negocio
     *
     * @return bool true si pasa todas las validaciones
     */
    private function validar() {
        $this->errores = array(); // Limpiar errores anteriores

        // Validar que el código no esté vacío
        if (empty($this->codigo)) {
            $this->errores[] = "El código es requerido";
        } else if (!preg_match('/^[A-Z0-9]{1,10}$/', $this->codigo)) {
            // Código alfanumérico, máximo 10 caracteres
            $this->errores[] = "El código debe ser alfanumérico (máx 10 caracteres)";
        }

        // Validar que el producto no esté vacío
        if (empty($this->producto)) {
            $this->errores[] = "El nombre del producto es requerido";
        } else if (strlen($this->producto) < 3 || strlen($this->producto) > 100) {
            // Entre 3 y 100 caracteres
            $this->errores[] = "El nombre debe tener entre 3 y 100 caracteres";
        }

        // Validar precio
        if ($this->precio === null || $this->precio === '') {
            $this->errores[] = "El precio es requerido";
        } else if ($this->precio <= 0) {
            // Precio mayor a 0
            $this->errores[] = "El precio debe ser mayor a 0";
        } else if ($this->precio > 999999.99) {
            // Límite superior
            $this->errores[] = "El precio no puede ser mayor a 999999.99";
        }

        // Validar cantidad
        if ($this->cantidad === null || $this->cantidad === '') {
            $this->errores[] = "La cantidad es requerida";
        } else if ($this->cantidad < 0) {
            // Cantidad no negativa
            $this->errores[] = "La cantidad no puede ser negativa";
        } else if ($this->cantidad > 999999) {
            // Límite superior
            $this->errores[] = "La cantidad no puede ser mayor a 999999";
        }

        // Retornar true si no hay errores
        return count($this->errores) === 0;
    }

    /**
     * Verificar si el código ya existe en la BD
     * Para evitar duplicados
     *
     * @param bool $excluirActual Si true, excluye el ID actual (para editar)
     * @return bool true si el código existe
     */
    private function codigoExiste($excluirActual = false) {
        $sql = "SELECT id FROM productos WHERE codigo = :codigo";
        $params = array(':codigo' => $this->codigo);

        // Si estamos editando, excluir el registro actual
        if ($excluirActual && $this->id !== null) {
            $sql .= " AND id != :id";
            $params[':id'] = $this->id;
        }

        $resultado = $this->obtenerFila($sql, $params);
        return $resultado !== false;
    }

    // ==================== MÉTODOS CRUD ====================

    /**
     * Guardar un nuevo producto
     * Inserta en la tabla productos
     *
     * @return array respuesta con success, message y errors
     */
    public function guardar() {
        // Validar los datos
        if (!$this->validar()) {
            return array(
                'success' => false,
                'message' => 'El producto contiene errores de validación',
                'accion' => 'guardar',
                'errors' => $this->errores
            );
        }

        // Verificar código duplicado
        if ($this->codigoExiste()) {
            return array(
                'success' => false,
                'message' => 'El código del producto ya existe',
                'accion' => 'guardar',
                'errors' => array('El código ' . $this->codigo . ' ya está registrado')
            );
        }

        try {
            // Preparar SQL INSERT
            $sql = "INSERT INTO productos (codigo, producto, precio, cantidad)
                    VALUES (:codigo, :producto, :precio, :cantidad)";

            // Ejecutar consulta
            $this->ejecutar($sql, array(
                ':codigo' => $this->codigo,
                ':producto' => $this->producto,
                ':precio' => $this->precio,
                ':cantidad' => $this->cantidad
            ));

            return array(
                'success' => true,
                'message' => 'Producto guardado exitosamente',
                'accion' => 'guardar',
                'errors' => array()
            );

        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => 'Error al guardar el producto: ' . $e->getMessage(),
                'accion' => 'guardar',
                'errors' => array($e->getMessage())
            );
        }
    }

    /**
     * Editar un producto existente
     * Actualiza el registro en la tabla
     *
     * @return array respuesta con success, message y errors
     */
    public function editar() {
        // Validar que el ID esté presente
        if ($this->id === null || empty($this->id)) {
            return array(
                'success' => false,
                'message' => 'El ID del producto no está definido',
                'accion' => 'editar',
                'errors' => array('ID requerido para editar')
            );
        }

        // Validar los datos
        if (!$this->validar()) {
            return array(
                'success' => false,
                'message' => 'El producto contiene errores de validación',
                'accion' => 'editar',
                'errors' => $this->errores
            );
        }

        // Verificar código duplicado (excluyendo el actual)
        if ($this->codigoExiste(true)) {
            return array(
                'success' => false,
                'message' => 'El código del producto ya existe en otro registro',
                'accion' => 'editar',
                'errors' => array('El código ' . $this->codigo . ' ya existe')
            );
        }

        try {
            // Preparar SQL UPDATE
            $sql = "UPDATE productos
                    SET codigo = :codigo,
                        producto = :producto,
                        precio = :precio,
                        cantidad = :cantidad
                    WHERE id = :id";

            // Ejecutar consulta
            $this->ejecutar($sql, array(
                ':id' => $this->id,
                ':codigo' => $this->codigo,
                ':producto' => $this->producto,
                ':precio' => $this->precio,
                ':cantidad' => $this->cantidad
            ));

            return array(
                'success' => true,
                'message' => 'Producto actualizado exitosamente',
                'accion' => 'editar',
                'errors' => array()
            );

        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => 'Error al actualizar el producto: ' . $e->getMessage(),
                'accion' => 'editar',
                'errors' => array($e->getMessage())
            );
        }
    }

    /**
     * Buscar productos
     * Si no se especifica campo/valor, retorna todos
     *
     * @param string $campo campo por el cual buscar (código, producto)
     * @param string $valor valor a buscar
     * @return array respuesta con productos encontrados
     */
    public function buscar($campo = '', $valor = '') {
        try {
            $sql = "SELECT id, codigo, producto, precio, cantidad FROM productos";

            // Construir WHERE dinámicamente
            if (!empty($campo) && !empty($valor)) {
                // Validar que el campo sea permitido
                $camposPermitidos = array('codigo', 'producto');
                if (!in_array($campo, $camposPermitidos)) {
                    $campo = 'codigo'; // Por defecto buscar por código
                }

                // Usar LIKE para búsqueda parcial
                $sql .= " WHERE " . $campo . " LIKE :valor";
                $parametros = array(':valor' => '%' . $valor . '%');
            } else {
                $parametros = array();
            }

            $sql .= " ORDER BY codigo ASC";

            // Ejecutar búsqueda
            $productos = $this->obtenerTodas($sql, $parametros);

            return array(
                'success' => true,
                'message' => count($productos) > 0 ? 'Búsqueda completada' : 'No se encontraron productos',
                'accion' => 'buscar',
                'data' => $productos,
                'errors' => array()
            );

        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => 'Error en búsqueda: ' . $e->getMessage(),
                'accion' => 'buscar',
                'data' => array(),
                'errors' => array($e->getMessage())
            );
        }
    }

    /**
     * Obtener un producto por ID
     *
     * @param int $id ID del producto
     * @return array producto encontrado o null
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT id, codigo, producto, precio, cantidad FROM productos WHERE id = :id";
            return $this->obtenerFila($sql, array(':id' => $id));
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Eliminar un producto
     *
     * @return array respuesta con success y message
     */
    public function eliminar() {
        if ($this->id === null || empty($this->id)) {
            return array(
                'success' => false,
                'message' => 'El ID del producto no está definido',
                'accion' => 'eliminar',
                'errors' => array('ID requerido')
            );
        }

        try {
            $sql = "DELETE FROM productos WHERE id = :id";
            $this->ejecutar($sql, array(':id' => $this->id));

            return array(
                'success' => true,
                'message' => 'Producto eliminado exitosamente',
                'accion' => 'eliminar',
                'errors' => array()
            );

        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage(),
                'accion' => 'eliminar',
                'errors' => array($e->getMessage())
            );
        }
    }
}
?>
