<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/security.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Obtener citas del usuario
$stmt = $pdo->prepare("SELECT * FROM citas WHERE usuario_id = ? ORDER BY fecha, hora");
$stmt->execute([$_SESSION['user_id']]);
$citas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Citas | Sistema de Citas Médicas</title>
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <i class="material-icons">medical_services</i>
                <span>Clinica de salud del Morenazo</span>
            </div>
            <nav class="nav-links">
                <a href="add_cita.php"><i class="material-icons">add</i> Agendar Cita</a>
                <a href="../pages/logout.php"><i class="material-icons">logout</i> Cerrar Sesión</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <h1 class="page-title"><i class="material-icons">event</i> Mis Citas Médicas</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php
                $messages = [
                    'cita_agregada' => 'Cita agregada exitosamente!',
                    'cita_actualizada' => 'Cita actualizada correctamente!',
                    'cita_eliminada' => 'Cita eliminada con éxito!'
                ];
                echo $messages[$_GET['success']] ?? 'Operación realizada con éxito!';
                ?>
            </div>
        <?php endif; ?>

        <div class="table-container card">
            <?php if (count($citas) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Motivo de la consulta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($citas as $cita): ?>
                        <tr class="animated">
                            <td><?= date('d/m/Y', strtotime($cita['fecha'])) ?></td>
                            <td><?= substr($cita['hora'], 0, 5) ?></td>
                            <td><?= htmlspecialchars($cita['motivo']) ?></td>
                            <td class="table-actions">
                                <a href="edit_cita.php?id=<?= $cita['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="material-icons">edit</i> Editar
                                </a>
                                <a href="delete_cita.php?id=<?= $cita['id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('¿Estás seguro de eliminar esta cita?');">
                                    <i class="material-icons">delete</i> Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="material-icons">event_available</i>
                    <h3>No tienes citas programadas</h3>
                    <p>Agenda tu primera cita médica haciendo clic en el botón inferior</p>
                    <a href="add_cita.php" class="btn btn-primary">
                        <i class="material-icons">add</i> Agendar Cita
                    </a>
                </div>
            <?php endif; ?>
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