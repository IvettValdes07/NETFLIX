<?php
// conexion.php

$host = 'localhost';
$usuario = 'root'; // Cambia si es necesario
$password = ''; // Cambia si tienes contraseña
$base_datos = 'CHILLSEX';

try {
    // ✅ PASO 1: Conectar a MySQL sin especificar base de datos
    $conexion_temp = new PDO("mysql:host=$host;charset=utf8", $usuario, $password);
    $conexion_temp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // ✅ PASO 2: Crear la base de datos si no existe
    $sql_crear_bd = "CREATE DATABASE IF NOT EXISTS $base_datos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $conexion_temp->exec($sql_crear_bd);
    
    // ✅ PASO 3: Conectar a la base de datos recién creada
    $conexion = new PDO("mysql:host=$host;dbname=$base_datos;charset=utf8mb4", $usuario, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // ✅ PASO 4: Crear la tabla con TODOS los campos necesarios
    $sql_crear_tabla = "
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            correo VARCHAR(100) NOT NULL UNIQUE,
            telefono VARCHAR(20) NOT NULL,
            password VARCHAR(255) NOT NULL,
            plan VARCHAR(20) NOT NULL,
            metodo_pago VARCHAR(20) NOT NULL,
            nombre_tarjeta VARCHAR(100) NOT NULL,
            numero_tarjeta VARCHAR(16) NOT NULL,
            fecha_expiracion VARCHAR(5) NOT NULL,
            cvv VARCHAR(4) NOT NULL,
            codigo_postal VARCHAR(10) NOT NULL,
            newsletter TINYINT(1) DEFAULT 0,
            fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_correo (correo)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    $conexion->exec($sql_crear_tabla);
    
    // ✅ Cerrar la conexión temporal
    $conexion_temp = null;
    
} catch (PDOException $e) {
    die("❌ Error de conexión o configuración: " . $e->getMessage());
}
?>