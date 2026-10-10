<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';

use ISA3\Service\CasoService;
use ISA3\Support\Auth;
use ISA3\Support\Csrf;

Auth::requiere_login();
$usuario = Auth::usuario_actual();
$errores = $_SESSION['caso_errores'] ?? [];
$valores = $_SESSION['caso_valores'] ?? [];
unset($_SESSION['caso_errores'], $_SESSION['caso_valores']);

$tituloPagina = 'Crear caso de prueba';
require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'header.php';
require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'sidebar.php';

$valor = static fn (string $campo): string => htmlspecialchars((string) ($valores[$campo] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<main class="contenido-principal">
    <header class="encabezado-pagina">
        <p class="texto-suave">Formulario 1</p>
        <h1>Registro de Caso de Prueba</h1>
        <p>Completa la información para generar un caso documentado.</p>
    </header>

    <?php if ($errores !== []): ?>
        <div class="alerta alerta-error" role="alert">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form class="tarjeta formulario-caso" method="post" action="<?= htmlspecialchars(ISA3_BASE_URL . '/casos/guardar.php', ENT_QUOTES, 'UTF-8') ?>">
        <?= Csrf::csrf_field() ?>

        <label for="modulo">Módulo</label>
        <input id="modulo" name="modulo" type="text" value="<?= $valor('modulo') ?>" required maxlength="150">

        <label for="tecnica">Técnica</label>
        <select id="tecnica" name="tecnica" required>
            <option value="">Selecciona una técnica</option>
            <option value="caja negra" <?= ($valores['tecnica'] ?? '') === 'caja negra' ? 'selected' : '' ?>>Caja Negra</option>
            <option value="caja blanca" <?= ($valores['tecnica'] ?? '') === 'caja blanca' ? 'selected' : '' ?>>Caja Blanca</option>
        </select>

        <label for="subtecnica">Subtécnica</label>
        <select id="subtecnica" name="subtecnica" required data-subtecnica-seleccionada="<?= $valor('subtecnica') ?>">
            <option value="">Selecciona primero una técnica</option>
        </select>

        <div id="ayuda-contextual" class="ayuda-contextual" aria-live="polite"></div>

        <label for="objetivo">Objetivo</label>
        <textarea id="objetivo" name="objetivo" required><?= $valor('objetivo') ?></textarea>

        <label for="precondiciones">Precondiciones <span class="texto-suave">(opcional)</span></label>
        <textarea id="precondiciones" name="precondiciones"><?= $valor('precondiciones') ?></textarea>

        <label for="datos_entrada">Datos de entrada <span class="texto-suave">(opcional)</span></label>
        <textarea id="datos_entrada" name="datos_entrada"><?= $valor('datos_entrada') ?></textarea>

        <label for="pasos_ejecucion">Pasos de ejecución</label>
        <textarea id="pasos_ejecucion" name="pasos_ejecucion" required><?= $valor('pasos_ejecucion') ?></textarea>

        <label for="resultado_esperado">Resultado esperado</label>
        <textarea id="resultado_esperado" name="resultado_esperado" required><?= $valor('resultado_esperado') ?></textarea>

        <label for="resultado_obtenido">Resultado obtenido <span class="texto-suave">(opcional)</span></label>
        <textarea id="resultado_obtenido" name="resultado_obtenido"><?= $valor('resultado_obtenido') ?></textarea>

        <label for="estado">Estado</label>
        <select id="estado" name="estado" required>
            <option value="fallo" <?= ($valores['estado'] ?? 'fallo') === 'fallo' ? 'selected' : '' ?>>Fallo</option>
            <option value="exito" <?= ($valores['estado'] ?? '') === 'exito' ? 'selected' : '' ?>>Éxito</option>
        </select>

        <label for="observaciones">Observaciones <span class="texto-suave">(opcional)</span></label>
        <textarea id="observaciones" name="observaciones"><?= $valor('observaciones') ?></textarea>

        <label for="evidencia">Evidencia <span class="texto-suave">(opcional)</span></label>
        <textarea id="evidencia" name="evidencia"><?= $valor('evidencia') ?></textarea>

        <button class="boton boton-principal" type="submit">Guardar caso de prueba</button>
    </form>
</main>
<script src="<?= htmlspecialchars(ISA3_BASE_URL . '/assets/js/subtecnicas.js', ENT_QUOTES, 'UTF-8') ?>"></script>
<?php require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'footer.php'; ?>
