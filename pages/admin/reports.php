<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/security.php';

// Solo para administradores
if (!isLoggedIn() || $_SESSION['user_rol'] !== 'admin') {
    header('Location: /proyecto21/pages/login.php');
    exit;
}

// Obtener estadísticas
$stmt = $pdo->query("SELECT 
    COUNT(*) as total_usuarios,
    SUM(rol = 'admin') as total_admins,
    SUM(rol = 'paciente') as total_pacientes
    FROM usuarios");
$stats = $stmt->fetch();

$stmt = $pdo->query("SELECT 
    DATE_FORMAT(fecha, '%Y-%m') as mes,
    COUNT(*) as total_citas
    FROM citas
    GROUP BY mes
    ORDER BY mes DESC
    LIMIT 6");
$citas_por_mes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes del Sistema</title>
    <link href="../../assets/css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <i class="material-icons">assessment</i>
                <span>Reportes del Sistema</span>
            </div>
            <nav class="nav-links">
                <a href="dashboard.php"><i class="material-icons">home</i> Inicio</a>
                <a href="/proyecto21/pages/logout.php"><i class="material-icons">logout</i> Salir</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <h1 class="page-title"><i class="material-icons">insert_chart</i> Estadísticas del Sistema</h1>
        
        <div class="grid-container">
            <!-- Tarjeta de Usuarios -->
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="material-icons">people</i>
                </div>
                <div class="stat-content">
                    <h3>Usuarios Registrados</h3>
                    <p><?= $stats['total_usuarios'] ?></p>
                    <div class="stat-detail">
                        <span><?= $stats['total_admins'] ?> Administradores</span>
                        <span><?= $stats['total_pacientes'] ?> Pacientes</span>
                    </div>
                </div>
            </div>
            
            <!-- Tarjeta de Citas -->
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="material-icons">event</i>
                </div>
                <div class="stat-content">
                    <h3>Citas Totales</h3>
                    <p><?= $stats['total_citas'] ?? 0 ?></p>
                </div>
            </div>
        </div>

        <!-- Gráfico de Citas -->
        <div class="card">
            <h2><i class="material-icons">show_chart</i> Citas por Mes</h2>
            <canvas id="citasChart" height="300"></canvas>
        </div>
    </main>

    <script>
        // Datos para el gráfico
        const citasData = {
            labels: [<?= implode(',', array_map(function($item) { 
                return "'" . date('M Y', strtotime($item['mes'] . '-01')) . "'"; 
            }, $citas_por_mes)) ?>],
            datasets: [{
                label: 'Citas por Mes',
                data: [<?= implode(',', array_column($citas_por_mes, 'total_citas')) ?>],
                backgroundColor: '#3bb4c1',
                borderColor: '#0a7c8c',
                tension: 0.1
            }]
        };

        // Configuración del gráfico
        const ctx = document.getElementById('citasChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: citasData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>