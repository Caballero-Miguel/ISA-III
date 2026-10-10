<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

use ISA3\Controller\CasoController;
use ISA3\Repository\CasoRepository;
use ISA3\Support\Auth;
use ISA3\Support\Csrf;

Auth::requiere_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ISA3_BASE_URL . '/casos/crear.php');
    exit;
}

if (!Csrf::csrf_validar((string) ($_POST['csrf_token'] ?? ''))) {
    $_SESSION['caso_errores'] = ['El formulario expiró. Recarga la página e inténtalo nuevamente.'];
    $_SESSION['caso_valores'] = $_POST;
    header('Location: ' . ISA3_BASE_URL . '/casos/crear.php');
    exit;
}

$usuario = Auth::usuario_actual();
$controlador = new CasoController(new CasoRepository(db()));
$resultado = $controlador->guardar($_POST, $usuario);

if ($resultado['ok']) {
    header('Location: ' . ISA3_BASE_URL . '/casos/listar.php?mensaje=creado');
    exit;
}

$_SESSION['caso_errores'] = $resultado['errores'];
$_SESSION['caso_valores'] = $_POST;
header('Location: ' . ISA3_BASE_URL . '/casos/crear.php');
exit;
