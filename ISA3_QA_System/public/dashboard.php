<?php

declare(strict_types=1);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

use ISA3\Support\Auth;

Auth::requiere_login();
$usuario = Auth::usuario_actual();
$conexion = db();

$filtroUsuario = '';
$parametros = [];

if (!Auth::es_admin()) {
    $filtroUsuario = ' WHERE usuario_id = :usuario_id';
    $parametros['usuario_id'] = Auth::usuario_id();
}

$consulta = $conexion->prepare(
    'SELECT
        COUNT(*) AS total,
        COALESCE(SUM(estado = \'exito\'), 0) AS exitosos,
        COALESCE(SUM(estado = \'fallo\'), 0) AS fallidos,
        COALESCE(SUM(tecnica = \'caja_negra\'), 0) AS caja_negra,
        COALESCE(SUM(tecnica = \'caja_blanca\'), 0) AS caja_blanca
     FROM casos_prueba' . $filtroUsuario
);
$consulta->execute($parametros);
$metricas = $consulta->fetch() ?: [
    'total' => 0,
    'exitosos' => 0,
    'fallidos' => 0,
    'caja_negra' => 0,
    'caja_blanca' => 0,
];

$formularios = [
    ['numero' => 1, 'nombre' => 'Registro de Caso de Prueba', 'estado' => 'Disponible', 'enlace' => ISA3_BASE_URL . '/casos/listar.php'],
    ['numero' => 2, 'nombre' => 'Matriz de Clases de Equivalencia', 'estado' => 'Pendiente'],
    ['numero' => 3, 'nombre' => 'Análisis de Valor Límite', 'estado' => 'Pendiente'],
    ['numero' => 4, 'nombre' => 'Tabla de Decisión', 'estado' => 'Pendiente'],
    ['numero' => 5, 'nombre' => 'Cobertura de Caja Blanca', 'estado' => 'Pendiente'],
    ['numero' => 6, 'nombre' => 'Plan de Pruebas del Proyecto', 'estado' => 'Pendiente'],
    ['numero' => 7, 'nombre' => 'Rúbrica de Evaluación', 'estado' => 'Pendiente'],
    ['numero' => 8, 'nombre' => 'Autoevaluación y Coevaluación', 'estado' => 'Pendiente'],
    ['numero' => 9, 'nombre' => 'Portafolio de Evidencias', 'estado' => 'Pendiente'],
    ['numero' => 10, 'nombre' => 'Registro de Incidentes', 'estado' => 'Pendiente'],
];

$tituloPagina = 'Dashboard';
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'header.php';
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'sidebar.php';
?>
<main class="contenido-principal">
    <header class="encabezado-pagina">
        <p class="texto-suave">Panel de control</p>
        <h1>Dashboard</h1>
        <p>Bienvenido, <?= htmlspecialchars((string) $usuario['nombre'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars((string) $usuario['rol'], ENT_QUOTES, 'UTF-8') ?>).</p>
    </header>

    <section class="grid-metricas" aria-label="Métricas de casos de prueba">
        <article class="tarjeta metrica">
            <span class="metrica-etiqueta">Total de casos</span>
            <strong><?= (int) $metricas['total'] ?></strong>
        </article>
        <article class="tarjeta metrica">
            <span class="metrica-etiqueta">Casos exitosos</span>
            <strong class="texto-exito"><?= (int) $metricas['exitosos'] ?></strong>
        </article>
        <article class="tarjeta metrica">
            <span class="metrica-etiqueta">Casos fallidos</span>
            <strong class="texto-error"><?= (int) $metricas['fallidos'] ?></strong>
        </article>
    </section>

    <div class="tarjeta resumen-tecnicas">
        <strong>Distribución por técnica:</strong>
        Caja Negra: <?= (int) $metricas['caja_negra'] ?> casos |
        Caja Blanca: <?= (int) $metricas['caja_blanca'] ?> casos
    </div>

    <section class="seccion-formularios">
        <h2>Resumen de Formularios</h2>
        <div class="tarjeta tabla-contenedor">
            <table class="tabla-formularios">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Formulario</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($formularios as $formulario): ?>
                    <tr>
                        <td><?= (int) $formulario['numero'] ?></td>
                        <td>
                            <?php if (isset($formulario['enlace'])): ?>
                                <a href="<?= htmlspecialchars($formulario['enlace'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($formulario['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            <?php else: ?>
                                <?= htmlspecialchars($formulario['nombre'], ENT_QUOTES, 'UTF-8') ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?= $formulario['estado'] === 'Disponible' ? 'badge-exito' : 'badge-pendiente' ?>">
                                <?= htmlspecialchars($formulario['estado'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'footer.php'; ?>
