<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/security.php';

// Solo accesible si se requiere cambio de password
if (!isset($_SESSION['require_password_change'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nueva_password = $_POST['nueva_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validaciones
    if (empty($nueva_password) || empty($confirm_password)) {
        $error = "Ambos campos son obligatorios";
    } elseif ($nueva_password !== $confirm_password) {
        $error = "Las contraseñas no coinciden";
    } elseif (strlen($nueva_password) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres";
    } elseif (password_verify($nueva_password, $_SESSION['user_password'])) {
        $error = "No puedes usar la misma contraseña anterior";
    } else {
        // Actualizar contraseña
        $hash = password_hash($nueva_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $_SESSION['user_id']]);
        
        // Eliminar flag de cambio requerido
        unset($_SESSION['require_password_change']);
        
        // Redirigir según rol
        $redirect = ($_SESSION['user_rol'] === 'admin') ? 'admin/dashboard.php' : 'dashboard.php';
        header("Location: $redirect?success=password_changed");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña Temporal</title>
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card animated">
            <div class="auth-header">
                <h1><i class="material-icons">lock_reset</i> Cambiar Contraseña</h1>
                <p>Debes establecer una nueva contraseña permanente</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="material-icons">error</i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="nueva_password"><i class="material-icons">lock</i> Nueva Contraseña</label>
                    <input type="password" id="nueva_password" name="nueva_password" 
                           class="form-control" minlength="8" required>
                    <small class="form-hint">Mínimo 8 caracteres</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="material-icons">lock</i> Confirmar Contraseña</label>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           class="form-control" minlength="8" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="material-icons">save</i> Guardar Nueva Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>