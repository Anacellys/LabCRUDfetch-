# 📦 Gestión de Productos - CRUD

**Materia:** Desarrollo de Software VII  
**Estudiante:** Anacelis  Boniche 

---

## ¿Qué es este proyecto?

Sistema web para gestionar productos usando PHP orientado a objetos, MySQL y JavaScript con Fetch API.

---

## Tecnologías usadas

- PHP 
- MySQL 
- HTML 
- JavaScript 
- SweetAlert2

---

## Estructura de archivos

```
/
├── index.php           → Interfaz principal
├── registrar.php       → Controlador (recibe peticiones)
├── script.js           → Lógica del frontend
└── Modelo/
    ├── conexion.php    → Conexión a la base de datos
    └── Productos.php   → Clase Producto con CRUD
```

---

## Funcionalidades

- ✅ Guardar producto
- ✅ Buscar producto por código o nombre
- ✅ Editar producto existente
- ✅ Eliminar producto

---

## Base de datos

Crear la base de datos `productosdb` con la siguiente tabla:

```sql
CREATE DATABASE productosdb;

USE productosdb;

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(10) NOT NULL UNIQUE,
    producto VARCHAR(100) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    cantidad INT NOT NULL
);
```

---

## Cómo ejecutar

1. Copiar el proyecto en la carpeta `htdocs` (XAMPP) o `www` (WAMP)
2. Crear la base de datos con el SQL de arriba
3. Iniciar Apache y MySQL
4. Abrir el navegador en `http://localhost/nombre-carpeta/`

---

## Validaciones

- Código: alfanumérico, máximo 10 caracteres, no duplicado
- Nombre: entre 3 y 100 caracteres
- Precio: mayor a 0, máximo 999,999.99
- Cantidad: no negativa, máximo 999,999