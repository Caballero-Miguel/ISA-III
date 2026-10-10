<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

use ISA3\Repository\UsuarioRepository;
use ISA3\Support\Auth;

Auth::requiere_login();
Auth::requiere_rol('admin');

$usuarios = (new UsuarioRepository(db()))->listarTodos();
$tituloPagina = 'Usuarios';

require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'header.php';
require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'sidebar.php';
?>
<main class="contenido-principal">
    <header class="encabezado-pagina">
        <p class="texto-suave">Administración</p>
        <h1>Usuarios</h1>
        <p>Total de usuarios registrados: <?= count($usuarios) ?></p>
    </header>

    <div class="tarjeta tabla-contenedor">
        <table class="tabla-formularios">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Fecha de creación</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= (int) $usuario['id'] ?></td>
                    <td><?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($usuario['correo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="badge <?= $usuario['rol'] === 'admin' ? 'badge-admin' : 'badge-exito' ?>">
                            <?= htmlspecialchars(ucfirst($usuario['rol']), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($usuario['fecha_creacion'])), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'footer.php'; ?>
