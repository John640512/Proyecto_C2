<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/security.php';

// Solo accesible para administradores
if (!isLoggedIn() || $_SESSION['user_rol'] !== 'admin') {
    header('Location: /proyecto21/pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'];
    $nueva_password = 'Clinica2024'; // Contraseña temporal estándar
    
    $hash = password_hash($nueva_password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
    $stmt->execute([$hash, $usuario_id]);
    
    $success = "Contraseña restablecida a: <strong>Clinica2024</strong>. El usuario debe cambiarla al iniciar sesión.";
}

// Obtener lista de usuarios
$stmt = $pdo->query("SELECT id, nombre, email FROM usuarios ORDER BY nombre");
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseñas</title>
    <link href="../../assets/css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <i class="material-icons">autorenew</i>
                <span>Restablecer Contraseñas</span>
            </div>
            <nav class="nav-links">
                <a href="dashboard.php"><i class="material-icons">home</i> Panel</a>
                <a href="/proyecto21/pages/logout.php"><i class="material-icons">logout</i> Salir</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="card">
            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <i class="material-icons">check_circle</i> <?= $success ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="form">
                <div class="form-group">
                    <label><i class="material-icons">person</i> Seleccionar Usuario:</label>
                    <select name="usuario_id" class="form-control" required>
                        <?php foreach ($usuarios as $user): ?>
                            <option value="<?= $user['id'] ?>">
                                <?= htmlspecialchars($user['nombre']) ?> (<?= $user['email'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="material-icons">autorenew</i> Restablecer Contraseña
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="material-icons">arrow_back</i> Volver al Panel
                    </a>
                </div>
            </form>
            
            <div class="alert alert-info mt-3">
                <i class="material-icons">info</i> 
                <strong>Proceso seguro:</strong> La contraseña se establecerá a un valor temporal que el usuario debe cambiar en su primer login.
            </div>
        </div>
    </main>
</body>
</html>