<?php
session_start();

// Simulación de usuario logueado
if (!isset($_SESSION['usuario'])) {
    $_SESSION['usuario'] = [
        'id' => 1,
        'nombre' => 'Usuario Demo',
        'email' => 'usuario@chillsex.com',
        'imagen' => 'assets/images/default-avatar.png'
    ];
}

$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajustes - Chill&Sex</title>
    <link rel="stylesheet" href="assets/css/ajustes.css">
</head>
<body>
    <div class="container">
        <div class="header-page">
            <a href="dashboard.html" class="back-link">← Volver al inicio</a>
            <h1>Ajustes</h1>
        </div>

        <div class="settings-grid">
            <!-- Preferencias de reproducción -->
            <section class="settings-section">
                <h2>Reproducción</h2>
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Reproducción automática</h3>
                        <p>Reproduce automáticamente el siguiente episodio</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Saltar intro</h3>
                        <p>Mostrar botón para saltar intros automáticamente</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
            </section>

            <!-- Calidad de video -->
            <section class="settings-section">
                <h2>Calidad de Video</h2>
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Calidad de reproducción</h3>
                        <p>Ajustar calidad según tu conexión</p>
                    </div>
                    <select class="setting-select">
                        <option value="auto" selected>Automática</option>
                        <option value="high">Alta (HD)</option>
                        <option value="medium">Media</option>
                        <option value="low">Baja</option>
                    </select>
                </div>
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Uso de datos</h3>
                        <p>Controla cuántos datos usa por pantalla</p>
                    </div>
                    <select class="setting-select">
                        <option value="auto" selected>Automático</option>
                        <option value="low">Solo Wi-Fi</option>
                        <option value="medium">Ahorro de datos</option>
                        <option value="high">Máxima calidad</option>
                    </select>
                </div>
            </section>

            <!-- Control parental -->
            <section class="settings-section">
                <h2>Control Parental</h2>
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Restricciones de edad</h3>
                        <p>Limitar contenido según clasificación</p>
                    </div>
                    <select class="setting-select">
                        <option value="all" selected>Todo el contenido</option>
                        <option value="18">Mayores de 18</option>
                        <option value="16">Mayores de 16</option>
                        <option value="13">Mayores de 13</option>
                        <option value="7">Mayores de 7</option>
                    </select>
                </div>
            </section>

            <!-- Notificaciones -->
            <section class="settings-section">
                <h2>Notificaciones</h2>
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Nuevos episodios</h3>
                        <p>Recibir notificaciones de nuevos episodios</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Recomendaciones</h3>
                        <p>Recibir sugerencias personalizadas</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>
            </section>

            <!-- Idioma y subtítulos -->
            <section class="settings-section">
                <h2>Idioma y Subtítulos</h2>
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Idioma de audio</h3>
                        <p>Idioma preferido para reproducción</p>
                    </div>
                    <select class="setting-select">
                        <option value="es" selected>Español</option>
                        <option value="en">Inglés</option>
                        <option value="fr">Francés</option>
                        <option value="pt">Portugués</option>
                    </select>
                </div>
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Subtítulos</h3>
                        <p>Idioma de subtítulos predeterminado</p>
                    </div>
                    <select class="setting-select">
                        <option value="none">Sin subtítulos</option>
                        <option value="es" selected>Español</option>
                        <option value="en">Inglés</option>
                    </select>
                </div>
            </section>

            <!-- Privacidad -->
            <section class="settings-section">
                <h2>Privacidad</h2>
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Historial de reproducción</h3>
                        <p>Guardar lo que ves para recomendaciones</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Compartir actividad</h3>
                        <p>Permitir que otros vean tu actividad</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>
            </section>
        </div>

        <!-- Botón de acción -->
        <div class="action-section">
            <a href="editar-usuario.php" class="btn btn-secondary">Editar Perfil</a>
            <a href="dashboard.php" class="btn btn-primary">Guardar Cambios</a>
        </div>
    </div>
</body>
</html>