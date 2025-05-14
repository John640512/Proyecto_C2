<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/security.php';

// Verificar rol de administrador
if (!isLoggedIn() || $_SESSION['user_rol'] !== 'admin') {
    header('Location: /proyecto21/pages/login.php');
    exit;
}

// Obtener estadísticas
$stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
$total_usuarios = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM citas WHERE fecha = CURDATE()");
$citas_hoy = $stmt->fetch()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link href="../../assets/css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <i class="material-icons">admin_panel_settings</i>
                <span>Panel administrativo de la clínica de salud del Morenazo</span>
            </div>
            <nav class="nav-links">
                <a href="reset_user_password.php"><i class="material-icons">autorenew</i> Resetear Contraseñas</a>
                <a href="/proyecto21/pages/logout.php"><i class="material-icons">logout</i> Salir</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <h1 class="page-title">Bienvenido, <?= $_SESSION['user_name'] ?></h1>
        
        <div class="admin-stats">
            <div class="stat-card">
                <i class="material-icons">people</i>
                <h3>Usuarios Registrados</h3>
                <p><?= $total_usuarios ?></p>
            </div>
            
            <div class="stat-card">
                <i class="material-icons">event</i>
                <h3>Citas Hoy</h3>
                <p><?= $citas_hoy ?></p>
            </div>
        </div>
        
        <div class="admin-actions">
            <a href="reset_user_password.php" class="btn btn-primary">
                <i class="material-icons">autorenew</i> Gestionar Contraseñas
            </a>
            <a href="manage_users.php" class="btn btn-primary">
                <i class="material-icons">people</i> Gestionar Usuarios
            </a>
            <a href="reports.php" class="btn btn-primary">
                <i class="material-icons">assessment</i> Reportes
            </a>
        </div>
    </main>
</body>
</html>