<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/security.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: dashboard.php?error=id_no_proporcionado');
    exit;
}

$cita_id = sanitizeInput($_GET['id']);

// Verificar que la cita pertenece al usuario
$stmt = $pdo->prepare("DELETE FROM citas WHERE id = ? AND usuario_id = ?");
if ($stmt->execute([$cita_id, $_SESSION['user_id']])) {
    header('Location: dashboard.php?success=cita_eliminada');
} else {
    header('Location: dashboard.php?error=no_se_pudo_eliminar');
}
exit;