<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/security.php';

// Solo para administradores
if (!isLoggedIn() || $_SESSION['user_rol'] !== 'admin') {
    header('Location: /proyecto21/pages/login.php');
    exit;
}

// Obtener lista de usuarios
$stmt = $pdo->query("SELECT id, nombre, email, rol FROM usuarios ORDER BY nombre");
$usuarios = $stmt->fetchAll();

// Cambiar rol de usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_rol'])) {
    $usuario_id = $_POST['usuario_id'];
    $nuevo_rol = $_POST['nuevo_rol'];
    
    $stmt = $pdo->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
    $stmt->execute([$nuevo_rol, $usuario_id]);
    
    header('Location: manage_users.php?success=rol_actualizado');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link href="../../assets/css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <i class="material-icons">people</i>
                <span>Gestión de Usuarios</span>
            </div>
            <nav class="nav-links">
                <a href="dashboard.php"><i class="material-icons">home</i> Inicio</a>
                <a href="/proyecto21/pages/logout.php"><i class="material-icons">logout</i> Salir</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <h1 class="page-title"><i class="material-icons">group</i> Administración de Usuarios</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="material-icons">check_circle</i> Rol de usuario actualizado correctamente.
            </div>
        <?php endif; ?>

        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol Actual</th>
                        <th>Cambiar Rol</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                        <td>
                            <span class="badge <?= $usuario['rol'] === 'admin' ? 'badge-primary' : 'badge-secondary' ?>">
                                <?= ucfirst($usuario['rol']) ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" class="form-inline">
                                <input type="hidden" name="usuario_id" value="<?= $usuario['id'] ?>">
                                <select name="nuevo_rol" class="form-control form-control-sm">
                                    <option value="paciente" <?= $usuario['rol'] === 'paciente' ? 'selected' : '' ?>>Paciente</option>
                                    <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                </select>
                                <button type="submit" name="cambiar_rol" class="btn btn-sm btn-primary ml-2">
                                    <i class="material-icons">sync</i> Actualizar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>