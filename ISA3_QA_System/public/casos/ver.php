<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

use ISA3\Controller\CasoController;
use ISA3\Repository\CasoRepository;
use ISA3\Support\Auth;

Auth::requiere_login();
$usuario = Auth::usuario_actual();
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id < 1) {
    header('Location: ' . ISA3_BASE_URL . '/casos/listar.php');
    exit;
}

$repositorio = new CasoRepository(db());
$caso = $repositorio->buscarPorId($id);
$controlador = new CasoController($repositorio);
if ($caso === null) {
    header('Location: ' . ISA3_BASE_URL . '/casos/listar.php');
    exit;
}
if (!$controlador->puedeVer($caso, $usuario)) {
    $_SESSION['caso_mensaje_error'] = 'No tienes permiso para ver este caso.';
    header('Location: ' . ISA3_BASE_URL . '/casos/listar.php');
    exit;
}

$tecnica = ucwords(str_replace('_', ' ', $caso['tecnica']));
$campos = [
    'Código' => $caso['codigo'],
    'Módulo' => $caso['modulo'],
    'Técnica' => $tecnica,
    'Subtécnica' => $caso['subtecnica'],
    'Objetivo' => $caso['objetivo'],
    'Precondiciones' => $caso['precondiciones'],
    'Datos de entrada' => $caso['datos_entrada'],
    'Pasos de ejecución' => $caso['pasos_ejecucion'],
    'Resultado esperado' => $caso['resultado_esperado'],
    'Resultado obtenido' => $caso['resultado_obtenido'],
    'Estado' => $caso['estado'] === 'exito' ? 'Éxito' : 'Fallo',
    'Observaciones' => $caso['observaciones'],
    'Evidencia' => $caso['evidencia'],
    'Tester' => $caso['tester_nombre'],
    'Fecha de creación' => date('d/m/Y H:i', strtotime($caso['fecha_creacion'])),
];

$tituloPagina = 'Ver caso ' . $caso['codigo'];
require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'header.php';
require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'sidebar.php';
?>
<main class="contenido-principal">
    <header class="encabezado-pagina encabezado-con-accion">
        <div><p class="texto-suave">Detalle</p><h1><?= htmlspecialchars($caso['codigo'], ENT_QUOTES, 'UTF-8') ?></h1></div>
        <div class="grupo-botones">
            <a class="boton boton-secundario claro" href="<?= htmlspecialchars(ISA3_BASE_URL . '/casos/listar.php', ENT_QUOTES, 'UTF-8') ?>">Volver al listado</a>
            <?php if ($controlador->puedeEditar($caso, $usuario)): ?>
                <a class="boton boton-principal" href="<?= htmlspecialchars(ISA3_BASE_URL . '/casos/editar.php?id=' . (int) $caso['id'], ENT_QUOTES, 'UTF-8') ?>">Editar</a>
            <?php endif; ?>
        </div>
    </header>
    <dl class="tarjeta detalle-caso">
        <?php foreach ($campos as $etiqueta => $valor): ?>
            <dt><?= htmlspecialchars($etiqueta, ENT_QUOTES, 'UTF-8') ?></dt>
            <dd><?= nl2br(htmlspecialchars((string) ($valor ?? 'No registrado'), ENT_QUOTES, 'UTF-8')) ?></dd>
        <?php endforeach; ?>
    </dl>
</main>
<?php require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'footer.php'; ?>
