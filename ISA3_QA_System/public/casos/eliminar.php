<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

use ISA3\Repository\CasoRepository;
use ISA3\Support\Auth;
use ISA3\Support\Csrf;

Auth::requiere_login();
if (!Auth::es_admin()) {
    $_SESSION['caso_mensaje_error'] = 'Solo un administrador puede eliminar casos.';
    header('Location: ' . ISA3_BASE_URL . '/casos/listar.php');
    exit;
}

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id === false || $id < 1 || !Csrf::csrf_validar((string) ($_POST['csrf_token'] ?? ''))) {
    $_SESSION['caso_mensaje_error'] = 'La solicitud de eliminación no es válida.';
    header('Location: ' . ISA3_BASE_URL . '/casos/listar.php');
    exit;
}

(new CasoRepository(db()))->eliminar($id);
header('Location: ' . ISA3_BASE_URL . '/casos/listar.php?mensaje=eliminado');
exit;
