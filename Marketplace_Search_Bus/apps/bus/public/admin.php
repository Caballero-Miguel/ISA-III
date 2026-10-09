<?php
$ttl = Marketplace\Bus\Config\Config::int('SEARCH_TTL_SECONDS', 60);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administración · Marketplace Search Bus</title>
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
<header class="topbar compact">
    <div>
        <p class="eyebrow">Marketplace Search Bus</p>
        <h1>Administración de búsquedas temporales</h1>
    </div>
    <a class="nav-link" href="/">Volver al buscador</a>
</header>

<main>
    <section id="login-panel" class="panel login-panel">
        <p class="eyebrow">Acceso de demostración</p>
        <h2>Iniciar sesión</h2>
        <p>Este acceso protege las operaciones destructivas durante la exposición.</p>
        <form id="login-form">
            <label>Usuario<input name="username" autocomplete="username" required></label>
            <label>Contraseña<input name="password" type="password" autocomplete="current-password" required></label>
            <button class="button primary" type="submit">Ingresar</button>
        </form>
        <div id="login-message" class="message error" role="alert" hidden></div>
    </section>

    <section id="admin-panel" hidden>
        <section class="panel admin-summary">
            <div>
                <p class="eyebrow">Estado temporal</p>
                <h2><span id="active-count">0</span> búsquedas almacenadas</h2>
                <p>TTL configurado: <strong><?= htmlspecialchars((string) $ttl, ENT_QUOTES, 'UTF-8') ?> segundos</strong></p>
            </div>
            <div class="button-group">
                <button id="refresh-button" class="button secondary" type="button">Actualizar</button>
                <button id="cleanup-button" class="button secondary" type="button">Limpiar expiradas</button>
                <button id="delete-all-button" class="button danger" type="button">Eliminar todas</button>
                <button id="logout-button" class="button quiet" type="button">Salir</button>
            </div>
        </section>
        <div id="admin-message" class="message" role="status" aria-live="polite" hidden></div>
        <section class="panel table-panel">
            <div class="table-wrap">
                <table>
                    <thead><tr><th>ID</th><th>Criterios</th><th>Creada</th><th>Expira</th><th>Restante</th><th>Resultados</th><th>Acción</th></tr></thead>
                    <tbody id="searches-body"></tbody>
                </table>
            </div>
            <div id="admin-empty" class="empty-state small" hidden>
                <h3>No hay búsquedas temporales</h3>
                <p>Crea una búsqueda desde la página principal o espera la siguiente demostración.</p>
            </div>
        </section>
    </section>
</main>
<script src="/assets/admin.js" defer></script>
</body>
</html>

