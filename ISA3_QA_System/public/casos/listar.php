<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

use ISA3\Controller\CasoController;
use ISA3\Repository\CasoRepository;
use ISA3\Support\Auth;
use ISA3\Support\Csrf;

Auth::requiere_login();
$usuario = Auth::usuario_actual();
$repositorio = new CasoRepository(db());
$casos = Auth::es_admin()
    ? $repositorio->listarTodos()
    : $repositorio->listarPorUsuario((int) $usuario['id']);
$controlador = new CasoController($repositorio);

$mensajes = [
    'creado' => 'Caso creado correctamente.',
    'actualizado' => 'Caso actualizado correctamente.',
    'eliminado' => 'Caso eliminado correctamente.',
];
$mensaje = $mensajes[$_GET['mensaje'] ?? ''] ?? null;
$error = $_SESSION['caso_mensaje_error'] ?? null;
unset($_SESSION['caso_mensaje_error']);

$tituloPagina = 'Casos de prueba';
require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'header.php';
require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'sidebar.php';
?>
<main class="contenido-principal">
    <header class="encabezado-pagina encabezado-con-accion">
        <div>
            <p class="texto-suave">Formulario 1</p>
            <h1>Casos de Prueba</h1>
        </div>
        <a class="boton boton-principal" href="<?= htmlspecialchars(ISA3_BASE_URL . '/casos/crear.php', ENT_QUOTES, 'UTF-8') ?>">Generar caso de prueba</a>
    </header>

    <?php if ($mensaje !== null): ?>
        <div class="alerta alerta-exito mensaje-flash"><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($error !== null): ?>
        <div class="alerta alerta-error mensaje-flash"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if ($casos === []): ?>
        <div class="tarjeta estado-vacio">
            <p>Aún no hay casos registrados.</p>
        </div>
    <?php else: ?>
        <div class="tarjeta tabla-contenedor">
            <table class="tabla-formularios">
                <thead>
                <tr>
                    <th>ID</th><th>Código</th><th>Módulo</th><th>Técnica</th>
                    <th>Subtécnica</th><th>Estado</th><th>Tester</th><th>Fecha</th><th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($casos as $caso): ?>
                    <tr>
                        <td><?= (int) $caso['id'] ?></td>
                        <td><?= htmlspecialchars($caso['codigo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($caso['modulo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(ucwords(str_replace('_', ' ', $caso['tecnica'])), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($caso['subtecnica'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="badge <?= $caso['estado'] === 'exito' ? 'badge-exito' : 'badge-error' ?>">
                            <?= $caso['estado'] === 'exito' ? 'Éxito' : 'Fallo' ?>
                        </span></td>
                        <td><?= htmlspecialchars($caso['tester_nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($caso['fecha_creacion'])), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="acciones-tabla">
                            <a href="<?= htmlspecialchars(ISA3_BASE_URL . '/casos/ver.php?id=' . (int) $caso['id'], ENT_QUOTES, 'UTF-8') ?>">Ver</a>
                            <?php if ($controlador->puedeEditar($caso, $usuario)): ?>
                                <a href="<?= htmlspecialchars(ISA3_BASE_URL . '/casos/editar.php?id=' . (int) $caso['id'], ENT_QUOTES, 'UTF-8') ?>">Editar</a>
                            <?php endif; ?>
                            <?php if ($controlador->puedeEliminar($caso, $usuario)): ?>
                                <form method="post" action="<?= htmlspecialchars(ISA3_BASE_URL . '/casos/eliminar.php', ENT_QUOTES, 'UTF-8') ?>">
                                    <?= Csrf::csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $caso['id'] ?>">
                                    <button class="enlace-boton" type="submit" data-confirmar="¿Deseas eliminar este caso de prueba?">Eliminar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>
<?php require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'footer.php'; ?>
