/**
 * SCRIPT.JS - Lógica Frontend
 * Maneja:
 * - Validación de formulario
 * - Peticiones Fetch API
 * - Procesamiento de respuestas JSON
 * - Alertas con SweetAlert2
 * - Actualización dinámica de tabla
 */

// ==================== UTILIDADES ====================

/**
 * Obtener valores del formulario
 * @returns {Object} objeto con los valores del formulario
 */
function obtenerFormulario() {
    return {
        id: document.getElementById('id').value,
        codigo: document.getElementById('codigo').value.trim(),
        producto: document.getElementById('producto').value.trim(),
        precio: document.getElementById('precio').value.trim(),
        cantidad: document.getElementById('cantidad').value.trim()
    };
}

/**
 * Limpiar todos los campos del formulario
 */
function limpiarFormulario() {
    document.getElementById('formularioProducto').reset();
    document.getElementById('id').value = '';
    cambiarModoEdicion(false);
    console.log('Formulario limpiado');
}

/**
 * Rellenar el formulario con datos de un producto
 * @param {Object} producto - objeto con datos del producto
 */
function rellenarFormulario(producto) {
    document.getElementById('id').value = producto.id || '';
    document.getElementById('codigo').value = producto.codigo || '';
    document.getElementById('producto').value = producto.producto || '';
    document.getElementById('precio').value = producto.precio || '';
    document.getElementById('cantidad').value = producto.cantidad || '';
}

/**
 * Cambiar modo edición (visual)
 * @param {boolean} enEdicion - true si está en modo edición
 */
function cambiarModoEdicion(enEdicion) {
    const btnGuardar = document.getElementById('btnGuardar');
    const formularioSection = document.querySelector('.formulario-section');

    if (enEdicion) {
        btnGuardar.textContent = '✏️ Actualizar';
        btnGuardar.classList.add('btn-activo');
        formularioSection.classList.add('en-edicion');
    } else {
        btnGuardar.textContent = '💾 Guardar';
        btnGuardar.classList.remove('btn-activo');
        formularioSection.classList.remove('en-edicion');
    }
}

// ==================== VALIDACIONES ====================

/**
 * Validar que los campos no estén vacíos
 * @param {Object} datos - datos del formulario
 * @returns {boolean} true si pasa la validación
 */
function validarCliente(datos) {
    // Validar campos requeridos
    if (!datos.codigo) {
        Swal.fire({
            icon: 'warning',
            title: 'Campo requerido',
            text: 'El código del producto es obligatorio',
            confirmButtonColor: '#667eea'
        });
        return false;
    }

    if (!datos.producto) {
        Swal.fire({
            icon: 'warning',
            title: 'Campo requerido',
            text: 'El nombre del producto es obligatorio',
            confirmButtonColor: '#667eea'
        });
        return false;
    }

    if (!datos.precio || datos.precio <= 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Precio inválido',
            text: 'El precio debe ser mayor a 0',
            confirmButtonColor: '#667eea'
        });
        return false;
    }

    if (!datos.cantidad || datos.cantidad < 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Cantidad inválida',
            text: 'La cantidad no puede ser negativa',
            confirmButtonColor: '#667eea'
        });
        return false;
    }

    // Validar longitud de código
    if (datos.codigo.length > 10) {
        Swal.fire({
            icon: 'warning',
            title: 'Código muy largo',
            text: 'El código no puede exceder 10 caracteres',
            confirmButtonColor: '#667eea'
        });
        return false;
    }

    // Validar longitud de producto
    if (datos.producto.length < 3 || datos.producto.length > 100) {
        Swal.fire({
            icon: 'warning',
            title: 'Nombre inválido',
            text: 'El nombre debe tener entre 3 y 100 caracteres',
            confirmButtonColor: '#667eea'
        });
        return false;
    }

    return true;
}

// ==================== PETICIONES FETCH ====================

/**
 * Enviar datos al servidor mediante Fetch API
 * @param {Object} datos - datos a enviar
 * @param {string} accion - acción a ejecutar (guardar, buscar, editar)
 * @returns {Promise} promesa que resuelve con la respuesta del servidor
 */
async function enviarDatos(datos, accion) {
    try {
        // Crear un FormData para enviar los datos
        const formData = new FormData();
        formData.append('action', accion);

        // Agregar los datos al FormData
        Object.keys(datos).forEach(key => {
            if (datos[key] !== '') {
                formData.append(key, datos[key]);
            }
        });

        // Realizar petición Fetch
        const respuesta = await fetch('registrar.php', {
            method: 'POST',
            body: formData
        });

        // Verificar si la respuesta es válida
        if (!respuesta.ok) {
            throw new Error('Error HTTP: ' + respuesta.status);
        }

        // Parsear respuesta como JSON
        const datos_json = await respuesta.json();

        return datos_json;

    } catch (error) {
        console.error('Error en Fetch:', error);
        return {
            success: false,
            message: 'Error de conexión: ' + error.message,
            accion: accion,
            errors: [error.message]
        };
    }
}

// ==================== MANEJADORES DE ACCIONES ====================

/**
 * Manejar acción de guardar producto
 * Valida primero, luego envía al servidor
 */
function manejarGuardar() {
    const datos = obtenerFormulario();

    // Validar datos cliente
    if (!validarCliente(datos)) {
        return; // La validación muestra el error
    }

    // Determinar si es guardar o editar
    const accion = datos.id ? 'editar' : 'guardar';

    // Mostrar indicador de carga
    Swal.fire({
        title: 'Procesando...',
        text: 'Por favor espera',
        icon: 'info',
        allowOutsideClick: false,
        didOpen: (popup) => {
            Swal.showLoading();
        }
    });

    // Enviar datos al servidor
    enviarDatos(datos, accion).then(respuesta => {
        // Cerrar modal de carga
        Swal.close();

        if (respuesta.success) {
            // Éxito
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: respuesta.message,
                confirmButtonColor: '#667eea'
            });

            // Limpiar formulario
            limpiarFormulario();

            // Actualizar tabla
            listarProductos();

        } else {
            // Error
            const errorMsg = respuesta.errors.length > 0
                ? respuesta.errors.join('\n')
                : respuesta.message;

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMsg,
                confirmButtonColor: '#667eea'
            });
        }
    });
}

/**
 * Manejar acción de buscar
 * Búsqueda por código por defecto
 */
function manejarBuscar() {
    const datos = obtenerFormulario();
    const valor = datos.codigo || '';

    if (!valor) {
        // Si no hay búsqueda, mostrar todos
        listarProductos();
    } else {
        // Realizar búsqueda
        const datosEnvio = {
            campo: 'codigo',
            valor: valor
        };

        Swal.fire({
            title: 'Buscando...',
            icon: 'info',
            allowOutsideClick: false,
            didOpen: (popup) => {
                Swal.showLoading();
            }
        });

        enviarDatos(datosEnvio, 'buscar').then(respuesta => {
            Swal.close();

            if (respuesta.success) {
                // Actualizar tabla con resultados
                actualizarTabla(respuesta.data || []);

                if ((respuesta.data || []).length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Sin resultados',
                        text: 'No se encontraron productos con ese código',
                        confirmButtonColor: '#667eea'
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en búsqueda',
                    text: respuesta.message,
                    confirmButtonColor: '#667eea'
                });
            }
        });
    }
}

/**
 * Editar un producto
 * Carga sus datos en el formulario
 * @param {number} id - ID del producto a editar
 */
async function editarProducto(id) {
    try {
        // Obtener todos los productos
        const formData = new FormData();
        formData.append('action', 'buscar');

        const respuesta = await fetch('registrar.php', {
            method: 'POST',
            body: formData
        });

        const datos = await respuesta.json();

        // Encontrar el producto con ese ID
        if (datos.data) {
            const producto = datos.data.find(p => p.id == id);
            if (producto) {
                rellenarFormulario(producto);
                cambiarModoEdicion(true);
                // Scroll al formulario
                document.querySelector('.formulario-section').scrollIntoView({ behavior: 'smooth' });
            }
        }
    } catch (error) {
        console.error('Error al cargar producto:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cargar el producto',
            confirmButtonColor: '#667eea'
        });
    }
}

/**
 * Eliminar un producto
 * Solicita confirmación
 * @param {number} id - ID del producto a eliminar
 */
function eliminarProducto(id) {
    // Solicitar confirmación
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f5576c',
        cancelButtonColor: '#999',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceder con eliminación
            const formData = new FormData();
            formData.append('action', 'eliminar');
            formData.append('id', id);

            Swal.fire({
                title: 'Eliminando...',
                icon: 'info',
                allowOutsideClick: false,
                didOpen: (popup) => {
                    Swal.showLoading();
                }
            });

            fetch('registrar.php', {
                method: 'POST',
                body: formData
            })
            .then(respuesta => respuesta.json())
            .then(datos => {
                Swal.close();

                if (datos.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: 'El producto fue eliminado correctamente',
                        confirmButtonColor: '#667eea'
                    });
                    listarProductos();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: datos.message,
                        confirmButtonColor: '#667eea'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al eliminar el producto',
                    confirmButtonColor: '#667eea'
                });
            });
        }
    });
}

// ==================== ACTUALIZACIÓN DE TABLA ====================

/**
 * Actualizar la tabla con los datos de productos
 * @param {Array} productos - arreglo de productos
 */
function actualizarTabla(productos) {
    const cuerpo = document.getElementById('cuerpoTabla');

    // Limpiar tabla anterior
    cuerpo.innerHTML = '';

    if (!productos || productos.length === 0) {
        // Mostrar mensaje de tabla vacía
        cuerpo.innerHTML = `
            <tr class="tabla-vacia">
                <td colspan="6">
                    <p>📭 No hay productos registrados. ¡Comienza agregando uno!</p>
                </td>
            </tr>
        `;
        return;
    }

    // Llenar tabla con datos
    productos.forEach(producto => {
        const fila = document.createElement('tr');

        // Formatear precio
        const precioFormato = parseFloat(producto.precio).toFixed(2);

        fila.innerHTML = `
            <td>${producto.id}</td>
            <td><strong>${producto.codigo}</strong></td>
            <td>${producto.producto}</td>
            <td>$${precioFormato}</td>
            <td>${producto.cantidad}</td>
            <td>
                <button class="btn-editar" onclick="editarProducto(${producto.id})">
                    ✏️ Editar
                </button>
                <button class="btn-eliminar" onclick="eliminarProducto(${producto.id})">
                    🗑️ Eliminar
                </button>
            </td>
        `;

        cuerpo.appendChild(fila);
    });
}

/**
 * Listar todos los productos
 * Se ejecuta al cargar la página y después de cada operación
 */
async function listarProductos() {
    try {
        // Crear FormData vacío con acción buscar
        const formData = new FormData();
        formData.append('action', 'buscar');

        // Realizar petición
        const respuesta = await fetch('registrar.php', {
            method: 'POST',
            body: formData
        });

        const datos = await respuesta.json();

        // Actualizar tabla
        if (datos.success) {
            actualizarTabla(datos.data || []);
        } else {
            console.error('Error al listar:', datos.message);
            actualizarTabla([]);
        }

    } catch (error) {
        console.error('Error en listarProductos:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cargar los productos',
            confirmButtonColor: '#667eea'
        });
    }
}

console.log('script.js cargado correctamente');
