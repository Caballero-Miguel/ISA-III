<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

use ISA3\Controller\CasoController;
use ISA3\Repository\CasoRepository;
use ISA3\Service\CasoService;
use ISA3\Support\Auth;
use ISA3\Support\Csrf;

Auth::requiere_login();
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
$volver = static function (int $casoId): never {
    header('Location: ' . ISA3_BASE_URL . '/casos/editar.php?id=' . $casoId);
    exit;
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id === false || $id < 1) {
    header('Location: ' . ISA3_BASE_URL . '/casos/listar.php');
    exit;
}

$repositorio = new CasoRepository(db());
$caso = $repositorio->buscarPorId($id);
$usuario = Auth::usuario_actual();
$controlador = new CasoController($repositorio);
if ($caso === null || !$controlador->puedeEditar($caso, $usuario)) {
    $_SESSION['caso_mensaje_error'] = 'No tienes permiso para actualizar este caso.';
    header('Location: ' . ISA3_BASE_URL . '/casos/listar.php');
    exit;
}

if (!Csrf::csrf_validar((string) ($_POST['csrf_token'] ?? ''))) {
    $_SESSION['caso_errores'] = ['El formulario expiró. Recarga la página e inténtalo nuevamente.'];
    $_SESSION['caso_valores'] = $_POST;
    $volver($id);
}

$validacion = CasoService::validar($_POST);
if (!$validacion['ok']) {
    $_SESSION['caso_errores'] = $validacion['errores'];
    $_SESSION['caso_valores'] = $_POST;
    $volver($id);
}

$tecnica = str_replace(' ', '_', trim((string) $_POST['tecnica']));
$datos = [
    'modulo' => trim((string) $_POST['modulo']),
    'tecnica' => $tecnica,
    'subtecnica' => trim((string) $_POST['subtecnica']),
    'objetivo' => trim((string) $_POST['objetivo']),
    'precondiciones' => trim((string) ($_POST['precondiciones'] ?? '')) ?: null,
    'datos_entrada' => trim((string) ($_POST['datos_entrada'] ?? '')) ?: null,
    'pasos_ejecucion' => trim((string) $_POST['pasos_ejecucion']),
    'resultado_esperado' => trim((string) $_POST['resultado_esperado']),
    'resultado_obtenido' => trim((string) ($_POST['resultado_obtenido'] ?? '')) ?: null,
    'estado' => trim((string) $_POST['estado']),
    'observaciones' => trim((string) ($_POST['observaciones'] ?? '')) ?: null,
    'evidencia' => trim((string) ($_POST['evidencia'] ?? '')) ?: null,
];
$repositorio->actualizar($id, $datos);
header('Location: ' . ISA3_BASE_URL . '/casos/listar.php?mensaje=actualizado');
exit;
