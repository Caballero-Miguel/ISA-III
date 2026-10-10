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
$correo = '';
$mensaje = Auth::obtener_mensaje();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim((string) ($_POST['correo'] ?? ''));

    if (!Csrf::csrf_validar((string) ($_POST['csrf_token'] ?? ''))) {
        $errores[] = 'El formulario expiró. Recarga la página e inténtalo nuevamente.';
    } else {
        $controlador = new AuthController(new UsuarioRepository(db()));
        $resultado = $controlador->login($_POST);
        $errores = $resultado['errores'];

        if ($resultado['ok']) {
            header('Location: dashboard.php');
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
    <title>Iniciar sesión | ISA3 QA System</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
<main class="contenedor-autenticacion">
    <section class="tarjeta-autenticacion">
        <p class="texto-suave">ISA3 QA System</p>
        <h1>Iniciar sesión</h1>
        <p class="texto-suave">Accede al sistema de calidad.</p>

        <?php if ($mensaje !== null): ?>
            <div class="alerta alerta-<?= htmlspecialchars((string) ($mensaje['tipo'] ?? 'exito'), ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars((string) ($mensaje['texto'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($errores !== []): ?>
            <div class="alerta alerta-error" role="alert">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="login.php">
            <?= Csrf::csrf_field() ?>
            <label for="correo">Correo electrónico</label>
            <input id="correo" name="correo" type="email" value="<?= htmlspecialchars($correo, ENT_QUOTES, 'UTF-8') ?>" required maxlength="150" autocomplete="email">

            <label for="password">Contraseña</label>
            <input id="password" name="password" type="password" required autocomplete="current-password">

            <button class="boton boton-principal" type="submit">Iniciar sesión</button>
        </form>

        <p class="enlace-alternativo">¿No tienes una cuenta? <a href="register.php">Regístrate</a>.</p>
    </section>
</main>
</body>
</html>
