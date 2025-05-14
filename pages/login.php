<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/security.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);

    // Primero verificar las credenciales
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Establecer datos de sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_rol'] = $user['rol'];
        $_SESSION['user_password'] = $user['password']; // Almacenar hash para validación

        // Verificar si es contraseña temporal
        if (password_verify('Clinica2024', $user['password'])) {
            $_SESSION['require_password_change'] = true;
            header('Location: change_password.php');
        } else {
            // Redirección normal según rol
            if ($user['rol'] === 'admin') {
                header('Location: /proyecto21/pages/admin/dashboard.php');
            } else {
                header('Location: /proyecto21/pages/dashboard.php');
            }
        }
        exit;
    } else {
        $error = "Credenciales incorrectas. Por favor, intente nuevamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Sistema de Citas Médicas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card animated">
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
                    <label for="email"><i class="material-icons">email</i> Correo Electrónico</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="material-icons">lock</i> Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="material-icons">login</i> Iniciar Sesión
                </button>
            </form>
            
            <div class="auth-footer">
                <p>¿No tienes cuenta? <a href="register.php" class="link">Regístrate aquí</a></p>
            </div>
        </div>
    </div>
</body>
</html>