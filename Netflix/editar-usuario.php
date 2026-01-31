<?php
session_start();

// Simulación de usuario logueado - NO redirigir al login
if (!isset($_SESSION['usuario'])) {
    $_SESSION['usuario'] = [
        'id' => 1,
        'nombre' => 'Usuario Demo',
        'email' => 'usuario@chillsex.com',
        'imagen' => 'assets/images/default-avatar.png'
    ];
}

$usuario = $_SESSION['usuario'];
$mensaje = '';
$tipo_mensaje = '';

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Cambiar nombre de usuario
    if (isset($_POST['accion']) && $_POST['accion'] === 'cambiar_nombre') {
        $nuevo_nombre = trim($_POST['nombre']);
        
        if (!empty($nuevo_nombre)) {
            $_SESSION['usuario']['nombre'] = $nuevo_nombre;
            $usuario['nombre'] = $nuevo_nombre;
            $mensaje = 'Nombre de usuario actualizado correctamente';
            $tipo_mensaje = 'exito';
        } else {
            $mensaje = 'El nombre no puede estar vacío';
            $tipo_mensaje = 'error';
        }
    }
    
    // Cambiar contraseña
    if (isset($_POST['accion']) && $_POST['accion'] === 'cambiar_password') {
        $password_actual = $_POST['password_actual'];
        $password_nueva = $_POST['password_nueva'];
        $password_confirmar = $_POST['password_confirmar'];
        
        // En producción aquí verificarías la contraseña actual contra la BD
        if ($password_nueva === $password_confirmar) {
            if (strlen($password_nueva) >= 6) {
                // Aquí guardarías la nueva contraseña en la BD (hasheada)
                $mensaje = 'Contraseña actualizada correctamente';
                $tipo_mensaje = 'exito';
            } else {
                $mensaje = 'La contraseña debe tener al menos 6 caracteres';
                $tipo_mensaje = 'error';
            }
        } else {
            $mensaje = 'Las contraseñas no coinciden';
            $tipo_mensaje = 'error';
        }
    }
    
    // Cambiar imagen de perfil
    if (isset($_POST['accion']) && $_POST['accion'] === 'cambiar_imagen') {
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $tipo_archivo = $_FILES['imagen']['type'];
            
            if (in_array($tipo_archivo, $permitidos)) {
                $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $nombre_archivo = 'avatar_' . $usuario['id'] . '_' . time() . '.' . $extension;
                $ruta_destino = 'assets/images/avatars/' . $nombre_archivo;
                
                // Crear directorio si no existe
                if (!file_exists('assets/images/avatars/')) {
                    mkdir('assets/images/avatars/', 0777, true);
                }
                
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
                    $_SESSION['usuario']['imagen'] = $ruta_destino;
                    $usuario['imagen'] = $ruta_destino;
                    $mensaje = 'Imagen de perfil actualizada correctamente';
                    $tipo_mensaje = 'exito';
                } else {
                    $mensaje = 'Error al subir la imagen';
                    $tipo_mensaje = 'error';
                }
            } else {
                $mensaje = 'Tipo de archivo no permitido. Use JPG, PNG, GIF o WEBP';
                $tipo_mensaje = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - Chill&Sex</title>
    <link rel="stylesheet" href="assets/css/editar-usuario.css">
</head>
<body>
    <div class="container">
        <div class="header-page">
            <a href="dashboard.html" class="back-link">← Volver al inicio</a>
            <h1>Editar Usuario</h1>
        </div>

        <?php if ($mensaje): ?>
        <div class="mensaje mensaje-<?php echo $tipo_mensaje; ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
        <?php endif; ?>

        <!-- Sección de Imagen de Perfil -->
        <section class="profile-section">
            <h2>Imagen de Perfil</h2>
            <div class="profile-image-container">
                <img src="<?php echo htmlspecialchars($usuario['imagen']); ?>" 
                     alt="Avatar" 
                     class="profile-image"
                     id="previewImage">
                
                <form method="POST" enctype="multipart/form-data" class="image-form">
                    <input type="hidden" name="accion" value="cambiar_imagen">
                    <div class="file-input-wrapper">
                        <label for="imagen" class="file-label">Seleccionar imagen</label>
                        <input type="file" 
                               name="imagen" 
                               id="imagen" 
                               accept="image/*"
                               class="file-input">
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar Imagen</button>
                </form>
            </div>
        </section>

        <!-- Sección de Nombre de Usuario -->
        <section class="form-section">
            <h2>Nombre de Usuario</h2>
            <form method="POST" class="edit-form">
                <input type="hidden" name="accion" value="cambiar_nombre">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" 
                           name="nombre" 
                           id="nombre" 
                           value="<?php echo htmlspecialchars($usuario['nombre']); ?>"
                           required>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Nombre</button>
            </form>
        </section>

        <!-- Sección de Contraseña -->
        <section class="form-section">
            <h2>Cambiar Contraseña</h2>
            <form method="POST" class="edit-form">
                <input type="hidden" name="accion" value="cambiar_password">
                
                <div class="form-group">
                    <label for="password_actual">Contraseña Actual</label>
                    <input type="password" 
                           name="password_actual" 
                           id="password_actual" 
                           required>
                </div>

                <div class="form-group">
                    <label for="password_nueva">Nueva Contraseña</label>
                    <input type="password" 
                           name="password_nueva" 
                           id="password_nueva" 
                           required>
                </div>

                <div class="form-group">
                    <label for="password_confirmar">Confirmar Nueva Contraseña</label>
                    <input type="password" 
                           name="password_confirmar" 
                           id="password_confirmar" 
                           required>
                </div>

                <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
            </form>
        </section>

        <!-- Sección de Información de Cuenta -->
        <section class="info-section">
            <h2>Información de Cuenta</h2>
            <div class="info-item">
                <span class="info-label">Email:</span>
                <span class="info-value"><?php echo htmlspecialchars($usuario['email']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">ID de Usuario:</span>
                <span class="info-value">#<?php echo htmlspecialchars($usuario['id']); ?></span>
            </div>
        </section>
    </div>
</body>
</html>