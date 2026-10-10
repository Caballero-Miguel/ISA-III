<?php

declare(strict_types=1);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

use ISA3\Controller\AuthController;
use ISA3\Repository\UsuarioRepository;
use ISA3\Support\Auth;
use ISA3\Support\Csrf;

if (Auth::esta_logueado()) {
    header('Location: dashboard.php');
    exit;
}

$errores = [];
$nombre = '';
$correo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $correo = trim((string) ($_POST['correo'] ?? ''));

    if (!Csrf::csrf_validar((string) ($_POST['csrf_token'] ?? ''))) {
        $errores[] = 'El formulario expiró. Recarga la página e inténtalo nuevamente.';
    } else {
        $controlador = new AuthController(new UsuarioRepository(db()));
        $resultado = $controlador->registrar($_POST);
        $errores = $resultado['errores'];

        if ($resultado['ok']) {
            Auth::guardar_mensaje('exito', $resultado['mensaje']);
            header('Location: login.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta | ISA3 QA System</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
<main class="contenedor-autenticacion">
    <section class="tarjeta-autenticacion">
        <p class="texto-suave">ISA3 QA System</p>
        <h1>Crear cuenta</h1>
        <p class="texto-suave">Regístrate para documentar casos de prueba.</p>

        <?php if ($errores !== []): ?>
            <div class="alerta alerta-error" role="alert">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="register.php">
            <?= Csrf::csrf_field() ?>
            <label for="nombre">Nombre</label>
            <input id="nombre" name="nombre" type="text" value="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>" required minlength="3" maxlength="120">

            <label for="correo">Correo electrónico</label>
            <input id="correo" name="correo" type="email" value="<?= htmlspecialchars($correo, ENT_QUOTES, 'UTF-8') ?>" required maxlength="150">

            <label for="password">Contraseña</label>
            <input id="password" name="password" type="password" required minlength="8">

            <label for="confirmar">Confirmar contraseña</label>
            <input id="confirmar" name="confirmar" type="password" required minlength="8">

            <button class="boton boton-principal" type="submit">Crear cuenta</button>
        </form>

        <p class="enlace-alternativo">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a>.</p>
    </section>
</main>
</body>
</html>
