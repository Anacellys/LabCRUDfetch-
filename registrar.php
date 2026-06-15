<?php
/**
 * CONTROLADOR - registrar.php
 * Punto de entrada para todas las peticiones Fetch
 * Implementa un SWITCH que dirige las acciones a la clase Producto
 * Responde siempre en JSON
 */

// Incluir la clase Producto
require_once 'Modelo/Productos.php';

// Establecer header JSON para respuestas
header('Content-Type: application/json; charset=utf-8');

// Variable para almacenar la respuesta
$respuesta = array(
    'success' => false,
    'message' => 'Acción no especificada',
    'accion' => '',
    'errors' => array()
);

try {
    // Verificar que sea una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $respuesta['message'] = 'Solo se aceptan peticiones POST';
        echo json_encode($respuesta);
        exit;
    }

    // Obtener la acción solicitada
    $accion = isset($_POST['action']) ? trim($_POST['action']) : '';

    // Si no hay acción, enviar error
    if (empty($accion)) {
        $respuesta['message'] = 'No se especificó la acción';
        echo json_encode($respuesta);
        exit;
    }

    // SWITCH para procesar las diferentes acciones
    switch ($accion) {

        // ========== ACCIÓN: GUARDAR ==========
        case 'guardar':
            // Instanciar un nuevo objeto Producto
            $producto = new Producto();

            // Asignar valores desde el formulario
            $producto->setCodigo($_POST['codigo'] ?? '');
            $producto->setProducto($_POST['producto'] ?? '');
            $producto->setPrecio($_POST['precio'] ?? '');
            $producto->setCantidad($_POST['cantidad'] ?? '');

            // Ejecutar método guardar
            $respuesta = $producto->guardar();
            break;

        // ========== ACCIÓN: BUSCAR ==========
        case 'buscar':
            // Instanciar objeto Producto
            $producto = new Producto();

            // Obtener parámetros de búsqueda (opcionales)
            $campo = isset($_POST['campo']) ? trim($_POST['campo']) : '';
            $valor = isset($_POST['valor']) ? trim($_POST['valor']) : '';

            // Ejecutar búsqueda
            $respuesta = $producto->buscar($campo, $valor);
            break;

        // ========== ACCIÓN: EDITAR ==========
        case 'editar':
            // Instanciar objeto Producto
            $producto = new Producto();

            // Asignar valores desde el formulario
            $producto->setId($_POST['id'] ?? '');
            $producto->setCodigo($_POST['codigo'] ?? '');
            $producto->setProducto($_POST['producto'] ?? '');
            $producto->setPrecio($_POST['precio'] ?? '');
            $producto->setCantidad($_POST['cantidad'] ?? '');

            // Ejecutar método editar
            $respuesta = $producto->editar();
            break;

        // ========== ACCIÓN: ELIMINAR ==========
        case 'eliminar':
            // Instanciar objeto Producto
            $producto = new Producto();

            // Asignar el ID a eliminar
            $producto->setId($_POST['id'] ?? '');

            // Ejecutar método eliminar
            $respuesta = $producto->eliminar();
            break;

        // ========== ACCIÓN DESCONOCIDA ==========
        default:
            $respuesta['message'] = 'Acción no reconocida: ' . htmlspecialchars($accion);
            $respuesta['accion'] = $accion;
            break;
    }

} catch (Exception $e) {
    // Capturar cualquier excepción no manejada
    $respuesta['success'] = false;
    $respuesta['message'] = 'Error en servidor: ' . $e->getMessage();
    $respuesta['errors'] = array($e->getMessage());
}

// Convertir respuesta a JSON y enviar al cliente
echo json_encode($respuesta);
exit;
?>
