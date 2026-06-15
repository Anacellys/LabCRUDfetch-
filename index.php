<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorio CRUD - Gestión de Productos</title>

    <!-- Bootstrap CSS para estilos básicos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert2 para alertas personalizadas -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <style>
        /* Estilos personalizados */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            padding: 30px;
            margin-top: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
        }

        .header h1 {
            color: #333;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .formulario-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 5px solid #667eea;
        }

        .formulario-section.en-edicion {
            background: #fff3e0;
            border-left-color: #ff6b6b;
            box-shadow: inset 0 0 15px rgba(255, 107, 107, 0.1);
        }

        .formulario-section h3 {
            color: #667eea;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 10px 15px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-guardar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-guardar:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-guardar.btn-activo {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
            box-shadow: 0 0 20px rgba(255, 107, 107, 0.5);
        }

        .btn-guardar.btn-activo:hover {
            box-shadow: 0 5px 20px rgba(255, 107, 107, 0.6);
        }

        .btn-buscar {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .btn-buscar:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(245, 87, 108, 0.4);
            color: white;
        }

        .btn-limpiar {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .btn-limpiar:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
            color: white;
        }

        .tabla-section {
            margin-top: 30px;
        }

        .tabla-section h3 {
            color: #667eea;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .table {
            background: white;
            border-collapse: collapse;
        }

        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .table thead th {
            border: none;
            padding: 15px;
            font-weight: 600;
            text-align: center;
        }

        .table tbody td {
            padding: 12px 15px;
            vertical-align: middle;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
            transition: background 0.2s ease;
        }

        .btn-editar {
            background: #4facfe;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            margin-right: 5px;
        }

        .btn-editar:hover {
            background: #3d8fe8;
        }

        .btn-eliminar {
            background: #f5576c;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-eliminar:hover {
            background: #e53e52;
        }

        .tabla-vacia {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .tabla-vacia p {
            font-size: 16px;
            margin: 0;
        }

        .info-campo {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .btn-section {
                grid-template-columns: 1fr;
            }

            .table {
                font-size: 12px;
            }

            .table thead th {
                padding: 10px 5px;
            }

            .table tbody td {
                padding: 8px 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- ========== ENCABEZADO ========== -->
        <div class="header">
            <h1>📦 Gestión de Productos</h1>
            <p>Sistema CRUD - PHP OOP + MySQL + Fetch API + SweetAlert2</p>
        </div>

        <!-- ========== FORMULARIO ========== -->
        <div class="formulario-section">
            <h3>Ingreso de Datos</h3>
            <form id="formularioProducto">
                <!-- Campo ID (oculto para editar) -->
                <input type="hidden" id="id" name="id" value="">

                <!-- Campo: Código -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="codigo" class="form-label">Código del Producto</label>
                            <input type="text" class="form-control" id="codigo" name="codigo"
                                   placeholder="Ej: P001" maxlength="10">
                            <div class="info-campo">Máximo 10 caracteres alfanuméricos</div>
                        </div>
                    </div>

                    <!-- Campo: Nombre -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="producto" class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control" id="producto" name="producto"
                                   placeholder="Ej: Laptop Dell">
                            <div class="info-campo">Entre 3 y 100 caracteres</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Campo: Precio -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="precio" class="form-label">Precio (USD)</label>
                            <input type="number" class="form-control" id="precio" name="precio"
                                   placeholder="0.00" step="0.01" min="0">
                            <div class="info-campo">Debe ser mayor a 0</div>
                        </div>
                    </div>

                    <!-- Campo: Cantidad -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cantidad" class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="cantidad" name="cantidad"
                                   placeholder="0" min="0">
                            <div class="info-campo">Unidades disponibles</div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="btn-section">
                    <button type="button" class="btn btn-guardar" id="btnGuardar" onclick="manejarGuardar()">
                        💾 Guardar
                    </button>
                    <button type="button" class="btn btn-buscar" id="btnBuscar" onclick="manejarBuscar()">
                        🔍 Buscar
                    </button>
                    <button type="button" class="btn btn-limpiar" id="btnLimpiar" onclick="limpiarFormulario()">
                        🗑️ Limpiar
                    </button>
                </div>
            </form>
        </div>

        <!-- ========== TABLA DE RESULTADOS ========== -->
        <div class="tabla-section">
            <h3>Productos Registrados</h3>
            <div style="overflow-x: auto;">
                <table class="table" id="tablaProductos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Precio ($)</th>
                            <th>Cantidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTabla">
                        <tr class="tabla-vacia">
                            <td colspan="6">
                                <p>📭 No hay productos registrados. ¡Comienza agregando uno!</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========== SCRIPTS ========== -->
    <!-- jQuery para facilitar DOM manipulation (opcional pero útil) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script principal de la aplicación -->
    <script src="script.js"></script>

    <!-- Inicializar la tabla al cargar la página -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Página cargada. Inicializando...');
            listarProductos();
        });
    </script>
</body>
</html>
