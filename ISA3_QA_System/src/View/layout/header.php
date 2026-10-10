<?php

declare(strict_types=1);

$tituloPagina = $tituloPagina ?? 'ISA3 QA System';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina, ENT_QUOTES, 'UTF-8') ?> | ISA3 QA System</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(ISA3_BASE_URL . '/assets/css/estilos.css', ENT_QUOTES, 'UTF-8') ?>">
</head>
<body class="app-layout">
<div class="layout-wrapper">
