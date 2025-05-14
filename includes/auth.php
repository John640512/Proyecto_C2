<?php
function registrarUsuario($nombre, $email, $password, $pdo) {
  $hash = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
  return $stmt->execute([$nombre, $email, $hash]);
}

function login($email, $password, $pdo) {
  $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['nombre'];
    $_SESSION['user_rol'] = $user['rol'];
    
    // Redirección basada en el rol
    if ($user['rol'] === 'admin') {
        header('Location: /proyecto21/pages/admin/dashboard.php');
    } else {
        header('Location: /proyecto21/pages/dashboard.php');
    }
    exit();
    
    return true;
}
return false;
}

function isLoggedIn() {
  return isset($_SESSION['user_id']);
}