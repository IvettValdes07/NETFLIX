<?php
session_start();

// Conexión a la base de datos
require 'conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email'] ?? '');
    $password = htmlspecialchars($_POST['password'] ?? '');
    
    if (!empty($email) && !empty($password)) {
        try {
            // Buscar el usuario por correo
            $stmt = $conexion->prepare("SELECT id, nombre, correo, password, plan FROM usuarios WHERE correo = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verificar la contraseña
                if (password_verify($password, $usuario['password'])) {
                    // ✅ Login exitoso - crear sesión
                    $_SESSION['user_id'] = $usuario['id'];
                    $_SESSION['user_nombre'] = $usuario['nombre'];
                    $_SESSION['user_email'] = $usuario['correo'];
                    $_SESSION['user_plan'] = $usuario['plan'];
                    
                    // Redirigir al dashboard
                    header('Location: dashboard.html');
                    exit();
                } else {
                    // ❌ Contraseña incorrecta
                    $mensaje = '<div style="color: #ff1493; padding: 18px; background: rgba(255, 20, 147, 0.1); border-radius: 12px; margin-bottom: 24px; border-left: 4px solid #ff1493;">
                        ⚠️ Contraseña incorrecta. Por favor intenta de nuevo.
                    </div>';
                }
            } else {
                // ❌ Usuario no encontrado
                $mensaje = '<div style="color: #ff1493; padding: 18px; background: rgba(255, 20, 147, 0.1); border-radius: 12px; margin-bottom: 24px; border-left: 4px solid #ff1493;">
                    ⚠️ No existe una cuenta con este correo electrónico.
                </div>';
            }
            
        } catch (PDOException $e) {
            $mensaje = '<div style="color: #ff1493; padding: 18px; background: rgba(255, 20, 147, 0.1); border-radius: 12px; margin-bottom: 24px;">
                ✗ Error: ' . $e->getMessage() . '
            </div>';
        }
    } else {
        $mensaje = '<div style="color: #8a2be2; padding: 18px; background: rgba(138, 43, 226, 0.1); border-radius: 12px; margin-bottom: 24px;">
            ⚠ Por favor completa todos los campos.
        </div>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - CHILL&SEX</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="index.html" class="logo">CHILL&SEX</a>
        <div class="nav-buttons">
            <a href="login.html" class="btn btn-tertiary">Iniciar Sesión</a>
            <a href="registro.php" class="btn btn-secondary">Registro</a>
            <a href="registro.php" class="btn btn-primary">Suscribirse</a>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="login-container">
        <div class="login-box">
            <h1>Iniciar Sesión</h1>
            
            <?php if (!empty($mensaje)) echo $mensaje; ?>
            
            <form action="login.php" method="POST">
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Contraseña" required>
                </div>
                <button type="submit" class="btn-submit">Iniciar Sesión</button>
                
                <div class="checkbox-group">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Recuérdame</label>
                </div>

                <div class="form-footer">
                    <a href="#">¿Necesitas ayuda?</a>
                </div>
            </form>

            <div class="signup-link">
                ¿Primera vez en CHILL&SEX? <a href="registro.php">Suscríbete ahora</a>.
            </div>
        </div>
    </div>
</body>
</html>