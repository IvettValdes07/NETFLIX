<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Cargar PHPMailer manualmente (sin composer)
require 'PhpMailer/Exception.php';
require 'PhpMailer/PHPMailer.php';
require 'PhpMailer/SMTP.php';

// Conexión a la base de datos
require 'conexion.php'; 

$mensaje = '';

// Función para generar contraseña segura
function generarPassword($longitud = 12) {
    $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
    $password = '';
    $max = strlen($caracteres) - 1;
    for ($i = 0; $i < $longitud; $i++) {
        $password .= $caracteres[random_int(0, $max)];
    }
    return $password;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar datos del formulario
    $nombre = htmlspecialchars($_POST['nombre'] ?? '');
    $correo = htmlspecialchars($_POST['email'] ?? '');
    $telefono = htmlspecialchars($_POST['telefono'] ?? '');
    $plan = htmlspecialchars($_POST['plan'] ?? '');
    $metodo_pago = htmlspecialchars($_POST['metodo_pago'] ?? '');
    
    // Campos de tarjeta (solo si el método es tarjeta)
    $nombre_tarjeta = htmlspecialchars($_POST['nombre_tarjeta'] ?? '');
    $numero_tarjeta = htmlspecialchars($_POST['numero_tarjeta'] ?? '');
    $fecha_expiracion = htmlspecialchars($_POST['fecha_expiracion'] ?? '');
    $cvv = htmlspecialchars($_POST['cvv'] ?? '');
    $codigo_postal = htmlspecialchars($_POST['codigo_postal'] ?? '');
    
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;

    // Generar contraseña automáticamente
    $password_generada = generarPassword(12);

    // Validar que los campos principales no estén vacíos
    if (!empty($nombre) && !empty($correo) && !empty($telefono) && !empty($plan)) {
        
        try {
            // ✅ VERIFICAR SI EL CORREO YA EXISTE
            $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
            $stmt->execute([$correo]);
            
            if ($stmt->rowCount() > 0) {
                $mensaje = '<div style="color: orange; padding: 10px; background: #fff3e0; border-radius: 5px; margin-bottom: 20px;">⚠ Este correo ya está registrado.</div>';
            } else {
                
                // Encriptar la contraseña generada
                $password_hash = password_hash($password_generada, PASSWORD_DEFAULT);
                
                // ✅ INSERTAR EN LA BASE DE DATOS
                $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, telefono, password, plan, metodo_pago, nombre_tarjeta, numero_tarjeta, fecha_expiracion, cvv, codigo_postal, newsletter) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $correo, $telefono, $password_hash, $plan, $metodo_pago, $nombre_tarjeta, $numero_tarjeta, $fecha_expiracion, $cvv, $codigo_postal, $newsletter]);
                
                // ✅ ENVIAR CORREO DE CONFIRMACIÓN CON LA CONTRASEÑA
                $mail = new PHPMailer(true);

                try {
                    // Configuración del servidor SMTP
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'acenewt@gmail.com'; 
                    $mail->Password = 'hvlb iepp bcli msqh';  
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;
                    $mail->CharSet = 'UTF-8';
                    
                    $mail->SMTPDebug = 0;
                    
                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );

                    // Remitente y destinatario
                    $mail->setFrom('acenewt@gmail.com', 'CHILL&SEX');
                    $mail->addAddress($correo, $nombre);

                    // Obtener el nombre del plan
                    $plan_nombres = [
                        'basico' => 'Plan Precoz - $149/mes',
                        'estandar' => 'Plan Aguantador - $249/mes',
                        'premium' => 'Plan Duradero - $329/mes'
                    ];
                    $plan_texto = $plan_nombres[$plan] ?? $plan;

                    // Contenido del correo
                    $mail->isHTML(true);
                    $mail->Subject = 'Bienvenido a CHILL&SEX - Tu Contraseña';
                    $mail->Body = "
                        <!DOCTYPE html>
                        <html lang='es'>
                        <head>
                            <meta charset='UTF-8'>
                            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        </head>
                        <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;'>
                            <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f4f4f4; padding: 20px;'>
                                <tr>
                                    <td align='center'>
                                        <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
                                            <tr>
                                                <td style='background: linear-gradient(135deg, #e50914 0%, #b20710 100%); padding: 40px 20px; text-align: center;'>
                                                    <h1 style='margin: 0; color: #ffffff; font-size: 32px; font-weight: bold;'>¡Bienvenido a CHILL&SEX!</h1>
                                                    <p style='margin: 10px 0 0 0; color: #ffffff; font-size: 16px; opacity: 0.9;'>Tu cuenta ha sido creada exitosamente</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style='padding: 40px 30px;'>
                                                    <p style='margin: 0 0 30px 0; color: #333333; font-size: 16px; line-height: 1.6;'>
                                                        Hola <strong style='color: #e50914;'>$nombre</strong>,
                                                    </p>
                                                    <p style='margin: 0 0 30px 0; color: #666666; font-size: 15px; line-height: 1.6;'>
                                                        Tu suscripción a CHILL&SEX ha sido completada. A continuación encontrarás tus credenciales de acceso:
                                                    </p>
                                                    
                                                    <table width='100%' cellpadding='0' cellspacing='0' style='margin-bottom: 15px;'>
                                                        <tr>
                                                            <td style='background-color: #f8f9ff; padding: 20px; border-radius: 8px; border-left: 4px solid #e50914;'>
                                                                <p style='margin: 0 0 5px 0; color: #999999; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;'>Correo Electrónico</p>
                                                                <p style='margin: 0; color: #333333; font-size: 16px; font-weight: bold;'>$correo</p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    
                                                    <table width='100%' cellpadding='0' cellspacing='0' style='margin-bottom: 15px;'>
                                                        <tr>
                                                            <td style='background-color: #fff3e0; padding: 20px; border-radius: 8px; border-left: 4px solid #ff9800;'>
                                                                <p style='margin: 0 0 5px 0; color: #999999; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;'>Tu Contraseña</p>
                                                                <p style='margin: 0; color: #333333; font-size: 20px; font-weight: bold; font-family: monospace; letter-spacing: 2px;'>$password_generada</p>
                                                                <p style='margin: 10px 0 0 0; color: #ff6f00; font-size: 12px;'>⚠️ Guarda esta contraseña en un lugar seguro</p>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    <table width='100%' cellpadding='0' cellspacing='0' style='margin-bottom: 30px;'>
                                                        <tr>
                                                            <td style='background-color: #f8f9ff; padding: 20px; border-radius: 8px; border-left: 4px solid #e50914;'>
                                                                <p style='margin: 0 0 5px 0; color: #999999; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;'>Plan Seleccionado</p>
                                                                <p style='margin: 0; color: #333333; font-size: 16px; font-weight: bold;'>$plan_texto</p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    
                                                    <table width='100%' cellpadding='0' cellspacing='0' style='margin-bottom: 30px;'>
                                                        <tr>
                                                            <td style='background-color: #e3f2fd; padding: 20px; border-radius: 8px; text-align: center;'>
                                                                <p style='margin: 0 0 15px 0; color: #1976d2; font-size: 14px; font-weight: bold;'>
                                                                    Accede a tu cuenta
                                                                </p>
                                                                <a href='http://localhost/Netflix/login.php' style='display: inline-block; background-color: #e50914; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: bold;'>Iniciar Sesión</a>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    <table width='100%' cellpadding='0' cellspacing='0'>
                                                        <tr>
                                                            <td style='background-color: #e8f5e9; padding: 20px; border-radius: 8px; text-align: center;'>
                                                                <p style='margin: 0; color: #2e7d32; font-size: 15px;'>
                                                                    ✓ Tu cuenta está lista para usar
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style='background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #eeeeee;'>
                                                    <p style='margin: 0 0 10px 0; color: #999999; font-size: 14px;'>
                                                        Gracias por unirte a CHILL&SEX
                                                    </p>
                                                    <p style='margin: 0; color: #cccccc; font-size: 12px;'>
                                                        © 2026 CHILL&SEX - Todos los derechos reservados
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </body>
                        </html>
                    ";

                    $mail->send();
                    $mensaje = '<div style="color: green; padding: 20px; background: #e8f5e9; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #2e7d32;">
                        <div style="display: flex; align-items: center; margin-bottom: 10px;">
                            <span style="font-size: 24px; margin-right: 10px;">✉️</span>
                            <strong style="font-size: 18px; color: #2e7d32;">¡Correo enviado exitosamente!</strong>
                        </div>
                        <p style="margin: 0; color: #2e7d32;">Tu contraseña ha sido enviada a <strong>' . $correo . '</strong></p>
                        <p style="margin: 10px 0 0 0; color: #558b2f; font-size: 14px;">Por favor revisa tu bandeja de entrada (y spam si no lo ves).</p>
                    </div>';
                    
                } catch (Exception $e) {
                    $mensaje = '<div style="color: orange; padding: 10px; background: #fff3e0; border-radius: 5px; margin-bottom: 20px;">✓ Registro guardado, pero no se pudo enviar el correo: ' . $mail->ErrorInfo . '</div>';
                }
            }
            
        } catch (PDOException $e) {
            $mensaje = '<div style="color: red; padding: 10px; background: #ffebee; border-radius: 5px; margin-bottom: 20px;">✗ Error al guardar: ' . $e->getMessage() . '</div>';
        }
        
    } else {
        $mensaje = '<div style="color: orange; padding: 10px; background: #fff3e0; border-radius: 5px; margin-bottom: 20px;">⚠ Completa todos los campos obligatorios.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - CHILL&SEX </title>
    <link rel="stylesheet" href="css/registro.css">
    <style>
        .campos-tarjeta {
            display: none;
        }
        .campos-tarjeta.mostrar {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="index.html" class="logo">CHILL&SEX</a>
        <div class="nav-buttons">
            <a href="login.php" class="btn btn-tertiary">Iniciar Sesión</a>
            <a href="registro.php" class="btn btn-secondary">Registro</a>
            <a href="registro.php" class="btn btn-primary">Suscribirse</a>
        </div>
    </nav>

    <!-- Register Form -->
    <div class="register-container">
        <div class="register-box">
            <h1>Registro</h1>
            
            <?php if (!empty($mensaje)) echo $mensaje; ?>
            
            <form action="registro.php" method="POST">
                <div class="form-group">
                    <input type="text" name="nombre" placeholder="Nombre completo" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="tel" name="telefono" placeholder="Número de teléfono" required>
                </div>
                <div class="form-group">
                    <select name="plan" required>
                        <option value="">Selecciona un plan</option>
                        <option value="basico">Plan Precoz - $149/mes</option>
                        <option value="estandar">Plan Aguantador - $249/mes</option>
                        <option value="premium">Plan Duradero - $329/mes</option>
                    </select>
                </div>

                <h2 style="color: white; font-size: 20px; margin-top: 30px; margin-bottom: 16px;">Información de Pago</h2>

                <div class="form-group">
                    <label style="color: white; font-size: 16px; margin-bottom: 12px;">Método de pago</label>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <label style="display: flex; align-items: center; color: #b3b3b3; cursor: pointer;">
                            <input type="radio" name="metodo_pago" value="tarjeta_credito" required style="width: auto; margin-right: 10px;" onchange="mostrarCamposTarjeta(true)">
                            Tarjeta de Crédito
                        </label>
                        <label style="display: flex; align-items: center; color: #b3b3b3; cursor: pointer;">
                            <input type="radio" name="metodo_pago" value="tarjeta_debito" required style="width: auto; margin-right: 10px;" onchange="mostrarCamposTarjeta(true)">
                            Tarjeta de Débito
                        </label>
                        <label style="display: flex; align-items: center; color: #b3b3b3; cursor: pointer;">
                            <input type="radio" name="metodo_pago" value="paypal" required style="width: auto; margin-right: 10px;" onchange="mostrarCamposTarjeta(false)">
                            PayPal
                        </label>
                    </div>
                </div>

                <!-- Campos de Tarjeta (se muestran solo si selecciona tarjeta) -->
                <div id="camposTarjeta" class="campos-tarjeta">
                    <div class="form-group">
                        <input type="text" name="nombre_tarjeta" placeholder="Nombre en la tarjeta" id="input_nombre_tarjeta">
                    </div>

                    <div class="form-group">
                        <input type="text" name="numero_tarjeta" placeholder="Número de tarjeta" maxlength="16" id="input_numero_tarjeta">
                    </div>

                    <div style="display: flex; gap: 16px;">
                        <div class="form-group" style="flex: 1;">
                            <input type="text" name="fecha_expiracion" placeholder="MM/AA" maxlength="5" id="input_fecha_exp">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <input type="text" name="cvv" placeholder="CVV" maxlength="4" id="input_cvv">
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="text" name="codigo_postal" placeholder="Código postal de facturación" id="input_codigo_postal">
                    </div>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" name="terms" id="terms" required>
                    <label for="terms">
                        Acepto los <a href="#">Términos y Condiciones</a> y la <a href="#">Política de Privacidad</a>
                    </label>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" name="newsletter" id="newsletter">
                    <label for="newsletter">
                        Deseo recibir ofertas especiales y actualizaciones por email
                    </label>
                </div>

                <button type="submit" class="btn-submit">Crear Cuenta</button>
            </form>

            <div class="signin-link">
                ¿Ya tienes cuenta? <a href="login.html">Inicia sesión</a>.
            </div>
        </div>
    </div>

    <script>
        function mostrarCamposTarjeta(mostrar) {
            const camposTarjeta = document.getElementById('camposTarjeta');
            const inputs = [
                document.getElementById('input_nombre_tarjeta'),
                document.getElementById('input_numero_tarjeta'),
                document.getElementById('input_fecha_exp'),
                document.getElementById('input_cvv'),
                document.getElementById('input_codigo_postal')
            ];

            if (mostrar) {
                camposTarjeta.classList.add('mostrar');
                // Hacer los campos requeridos
                inputs.forEach(input => {
                    if (input) input.required = true;
                });
            } else {
                camposTarjeta.classList.remove('mostrar');
                // Quitar el requerimiento
                inputs.forEach(input => {
                    if (input) {
                        input.required = false;
                        input.value = ''; // Limpiar los valores
                    }
                });
            }
        }
    </script>
</body>
</html>