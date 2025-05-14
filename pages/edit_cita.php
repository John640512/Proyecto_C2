<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/security.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Validar ID de cita
if (!isset($_GET['id'])) {
    header('Location: dashboard.php?error=id_no_valido');
    exit;
}

$cita_id = sanitizeInput($_GET['id']);

// Obtener cita actual
$stmt = $pdo->prepare("SELECT * FROM citas WHERE id = ? AND usuario_id = ?");
$stmt->execute([$cita_id, $_SESSION['user_id']]);
$cita = $stmt->fetch();

if (!$cita) {
    header('Location: dashboard.php?error=cita_no_encontrada');
    exit;
}

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
        $stmt = $pdo->prepare("UPDATE citas SET nombre_paciente = ?, fecha = ?, hora = ?, motivo = ? WHERE id = ? AND usuario_id = ?");
        if ($stmt->execute([$nombre_paciente, $fecha, $hora, $motivo, $cita_id, $_SESSION['user_id']])) {
            header('Location: dashboard.php?success=cita_actualizada');
            exit;
        } else {
            $error = "Error al actualizar la cita. Intente nuevamente.";
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
    <title>Editar Cita | Sistema Médico</title>
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
                <h1><i class="material-icons">edit_calendar</i> Editar Cita Médica</h1>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="material-icons">error</i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="card-body">
                <div class="form-group">
                    <label for="nombre_paciente"><i class="material-icons">person</i> Nombre del Paciente</label>
                    <input type="text" id="nombre_paciente" name="nombre_paciente" class="form-control" 
                           value="<?= htmlspecialchars($cita['nombre_paciente']) ?>" required
                           placeholder="Nombre completo del paciente">
                </div>
                
                <div class="form-group">
                    <label for="fecha"><i class="material-icons">calendar_today</i> Fecha</label>
                    <input type="date" id="fecha" name="fecha" class="form-control" 
                           value="<?= $cita['fecha'] ?>" min="<?= date('Y-m-d') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="hora"><i class="material-icons">access_time</i> Hora</label>
                    <input type="time" id="hora" name="hora" class="form-control" 
                           value="<?= substr($cita['hora'], 0, 5) ?>" min="08:00" max="18:00" required>
                    <small class="form-hint">Horario de atención: 8:00 AM - 6:00 PM</small>
                </div>
                
                <div class="form-group">
                    <label for="motivo"><i class="material-icons">note</i> Motivo de la consulta</label>
                    <textarea id="motivo" name="motivo" class="form-control" rows="4" required><?= htmlspecialchars($cita['motivo']) ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="material-icons">save</i> Guardar Cambios
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