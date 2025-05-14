<?php
require_once __DIR__ . '/auth.php';

// Verificar si el usuario está logueado para mostrar menú adecuado
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = ($isLoggedIn && ($_SESSION['user_rol'] ?? '') === 'admin');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Sistema de Citas Médicas' ?></title>
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <i class="material-icons">medical_services</i>
                <span>Clínica Salud</span>
            </div>
            <?php if ($isLoggedIn): ?>
                <nav class="nav-links">
                    <?php if ($isAdmin): ?>
                        <a href="/proyecto21/pages/admin/dashboard.php">
                            <i class="material-icons">admin_panel_settings</i> Admin
                        </a>
                    <?php endif; ?>
                    <a href="dashboard.php"><i class="material-icons">home</i> Inicio</a>
                    <a href="logout.php"><i class="material-icons">logout</i> Salir</a>
                </nav>
            <?php endif; ?>
        </div>
    </header>

    <main class="container"></main>