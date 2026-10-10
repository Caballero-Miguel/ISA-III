<?php

declare(strict_types=1);

namespace ISA3\Service;

final class CasoService
{
    public const SUBTECNICAS_CAJA_NEGRA = [
        'Partición de equivalencia',
        'Análisis de valores límite',
        'Tabla de decisiones',
        'Pruebas de transición de estados',
        'Grafos causa-efecto',
        'Pruebas combinatorias por pares',
        'Pruebas basadas en casos de uso',
        'Pruebas basadas en escenarios',
        'Pruebas basadas en modelos',
        'Pruebas aleatorias',
    ];

    public const SUBTECNICAS_CAJA_BLANCA = [
        'Cobertura de sentencias',
        'Cobertura de decisiones',
        'Cobertura de condiciones',
        'Cobertura de decisión/condición',
        'Cobertura de condiciones múltiples',
        'Cobertura de caminos',
        'Cobertura de caminos básicos',
        'Cobertura de bucles',
        'Cobertura de funciones/métodos',
        'Cobertura de clases',
    ];

    public static function validar(array $post): array
    {
        $errores = [];
        $modulo = trim((string) ($post['modulo'] ?? ''));
        $tecnica = trim((string) ($post['tecnica'] ?? ''));
        $subtecnica = trim((string) ($post['subtecnica'] ?? ''));
        $objetivo = trim((string) ($post['objetivo'] ?? ''));
        $pasos = trim((string) ($post['pasos_ejecucion'] ?? ''));
        $resultado = trim((string) ($post['resultado_esperado'] ?? ''));
        $estado = trim((string) ($post['estado'] ?? ''));

        if ($modulo === '') {
            $errores[] = 'El módulo es obligatorio.';
        }

        if (!in_array($tecnica, ['caja negra', 'caja blanca'], true)) {
            $errores[] = 'Selecciona una técnica válida.';
        }

        $subtecnicasPermitidas = match ($tecnica) {
            'caja negra' => self::SUBTECNICAS_CAJA_NEGRA,
            'caja blanca' => self::SUBTECNICAS_CAJA_BLANCA,
            default => [],
        };

        if ($subtecnica === '') {
            $errores[] = 'La subtécnica es obligatoria.';
        } elseif (!in_array($subtecnica, $subtecnicasPermitidas, true)) {
            $errores[] = 'La subtécnica no corresponde a la técnica seleccionada.';
        }

        if ($objetivo === '') {
            $errores[] = 'El objetivo es obligatorio.';
        }

        if ($pasos === '') {
            $errores[] = 'Los pasos de ejecución son obligatorios.';
        }

        if ($resultado === '') {
            $errores[] = 'El resultado esperado es obligatorio.';
        }

        if (!in_array($estado, ['exito', 'fallo'], true)) {
            $errores[] = 'Selecciona un estado válido.';
        }

        return [
            'ok' => $errores === [],
            'errores' => $errores,
        ];
    }
}
