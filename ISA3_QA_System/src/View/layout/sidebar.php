<?php

declare(strict_types=1);

use ISA3\Support\Auth;

$usuarioSidebar = Auth::usuario_actual();
?>
<aside class="barra-lateral">
    <div class="marca">
        <a href="<?= htmlspecialchars(ISA3_BASE_URL . '/dashboard.php', ENT_QUOTES, 'UTF-8') ?>" class="marca-titulo">ISA3 QA System</a>
        <p>Formulario 1 — Casos de Prueba</p>
    </div>

    <nav class="navegacion" aria-label="Navegación principal">
        <a href="<?= htmlspecialchars(ISA3_BASE_URL . '/dashboard.php', ENT_QUOTES, 'UTF-8') ?>">Dashboard</a>
        <a href="<?= htmlspecialchars(ISA3_BASE_URL . '/casos/listar.php', ENT_QUOTES, 'UTF-8') ?>">Casos de Prueba</a>
        <?php if (Auth::es_admin()): ?>
            <a href="<?= htmlspecialchars(ISA3_BASE_URL . '/admin/usuarios.php', ENT_QUOTES, 'UTF-8') ?>">Usuarios</a>
        <?php endif; ?>
    </nav>

    <?php if ($usuarioSidebar !== null): ?>
        <div class="usuario-sidebar">
            <strong><?= htmlspecialchars($usuarioSidebar['nombre'], ENT_QUOTES, 'UTF-8') ?></strong>
            <span><?= htmlspecialchars($usuarioSidebar['correo'], ENT_QUOTES, 'UTF-8') ?></span>
            <span class="badge badge-rol"><?= htmlspecialchars(ucfirst($usuarioSidebar['rol']), ENT_QUOTES, 'UTF-8') ?></span>
            <a href="<?= htmlspecialchars(ISA3_BASE_URL . '/logout.php', ENT_QUOTES, 'UTF-8') ?>" class="boton boton-secundario boton-logout">Cerrar sesión</a>
        </div>
    <?php endif; ?>
</aside>
