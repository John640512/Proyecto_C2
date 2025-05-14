<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/security.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Obtener el nombre del usuario actual para sugerirlo como valor por defecto
$stmt = $pdo->prepare("SELECT nombre FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$usuario = $stmt->fetch();
$nombre_default = $usuario['nombre'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_paciente = sanitizeInput($_POST['nombre_paciente']);
    $fecha = sanitizeInput($_POST['fecha']);
    $hora = sanitizeInput($_POST['hora']);
    $motivo = sanitizeInput($_POST['motivo']);
    
    // Validaciones
    $errors = [];
    
    if (empty($nombre_paciente)) {
        $errors[] = "El nombre del paciente es obligatorio.";
    }
    
    if (!validateDate($fecha)) {
        $errors[] = "Fecha inválida. Use el formato YYYY-MM-DD.";
    } elseif (strtotime($fecha) < strtotime(date('Y-m-d'))) {
        $errors[] = "No puedes agendar citas en fechas pasadas.";
    }
    
    if (!validateTime($hora)) {
        $errors[] = "Hora inválida. Use el formato HH:MM.";
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO citas (usuario_id, nombre_paciente, fecha, hora, motivo) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $nombre_paciente, $fecha, $hora, $motivo])) {
            header('Location: dashboard.php?success=cita_agregada');
            exit;
        } else {
            $error = "Error al guardar la cita. Intente nuevamente.";
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Cita | Sistema Médico</title>
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <i class="material-icons">medical_services</i>
                <span>Clínica de salud del Morenazo</span>
            </div>
            <nav class="nav-links">
                <a href="dashboard.php"><i class="material-icons">home</i> Inicio</a>
                <a href="../pages/logout.php"><i class="material-icons">logout</i> Salir</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="card animated fadeIn">
            <div class="card-header">
                <h1><i class="material-icons">event_available</i> Nueva Cita Médica</h1>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="material-icons">error</i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="card-body">
                <div class="form-group">
                    <label for="nombre_paciente"><i class="material-icons">person</i> Nombre completo del paciente</label>
                    <input type="text" id="nombre_paciente" name="nombre_paciente" class="form-control" 
                           value="<?= htmlspecialchars($nombre_default) ?>" required
                           placeholder="Nombre completo del paciente">
                </div>
                
                <div class="form-group">
                    <label for="fecha"><i class="material-icons">calendar_today</i> Fecha de cita</label>
                    <input type="date" id="fecha" name="fecha" class="form-control" 
                           min="<?= date('Y-m-d') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="hora"><i class="material-icons">access_time</i> Hora</label>
                    <input type="time" id="hora" name="hora" class="form-control" 
                           min="08:00" max="18:00" required>
                    <small class="form-hint">Horario de atención: 8:00 AM - 6:00 PM</small>
                </div>
                
                <div class="form-group">
                    <label for="motivo"><i class="material-icons">note</i> Motivo de la consulta</label>
                    <textarea id="motivo" name="motivo" class="form-control" rows="4" 
                              placeholder="Describa el motivo de su consulta..." required></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="material-icons">save</i> Guardar Cita
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="material-icons">cancel</i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="wave"></div>
        <div class="footer-content">
            <p>Sistema de Citas Médicas &copy; <?= date('Y') ?></p>
        </div>
    </footer>
</body>
</html>