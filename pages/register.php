<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/security.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitizeInput($_POST['nombre']);
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    $confirm_password = sanitizeInput($_POST['confirm_password']);

    // Validaciones
    $errors = [];
    
    if (empty($nombre)) {
        $errors[] = "El nombre completo es obligatorio.";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El formato del correo electrónico no es válido.";
    }
    
    if (strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden.";
    }

    if (empty($errors)) {
        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = "El correo electrónico ya está registrado.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$nombre, $email, $hash])) {
                $_SESSION['success'] = "Registro exitoso. Por favor inicia sesión.";
                header('Location: login.php');
                exit;
            } else {
                $error = "Error al registrar el usuario. Intente nuevamente.";
            }
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
    <title>Registro | Sistema de Citas Médicas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1><i class="material-icons">medical_services</i> Clinica de salud</h1>
                <p>Sistema de Gestión de Citas Médicas</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="material-icons">error</i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="nombre"><i class="material-icons">person</i> Nombre Completo *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" 
                           value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="material-icons">email</i> Correo Electrónico *</label>
                    <input type="email" id="email" name="email" class="form-control"
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="material-icons">lock</i> Contraseña *</label>
                    <input type="password" id="password" name="password" class="form-control" minlength="8" required>
                    <small class="form-hint">Mínimo 8 caracteres</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="material-icons">lock</i> Confirmar Contraseña *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" minlength="8" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="material-icons">how_to_reg</i> Registrarse
                </button>
            </form>
            
            <div class="auth-footer">
                <p>¿Ya tienes cuenta? <a href="login.php" class="link">Inicia sesión aquí</a></p>
            </div>
        </div>
    </div>
</body>
</html>