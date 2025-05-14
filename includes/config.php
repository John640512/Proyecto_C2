<?php
session_start();
$host = 'localhost';
$dbname = 'citas_medicas';
$user = 'root';
$pass = '';
$port = '3307';  // Cambia esto al puerto que uses (ej: 3307, 3308, etc.)

try {
  // Añade el puerto en el DSN (cadena de conexión)
  $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Error de conexión: " . $e->getMessage());
}