<?php

declare(strict_types=1);

namespace ISA3\Controller;

use ISA3\Repository\CasoRepository;
use ISA3\Service\CasoService;

final class CasoController
{
    public function __construct(private readonly CasoRepository $casos)
    {
    }

    public function guardar(array $post, array $usuario): array
    {
        $validacion = CasoService::validar($post);

        if (!$validacion['ok']) {
            return [
                'ok' => false,
                'errores' => $validacion['errores'],
            ];
        }

        $tecnica = trim((string) $post['tecnica']);
        $datos = [
            'codigo' => $this->casos->obtenerSiguienteCodigo(),
            'modulo' => trim((string) $post['modulo']),
            'tecnica' => str_replace(' ', '_', $tecnica),
            'subtecnica' => trim((string) $post['subtecnica']),
            'objetivo' => trim((string) $post['objetivo']),
            'precondiciones' => $this->valorOpcional($post['precondiciones'] ?? null),
            'datos_entrada' => $this->valorOpcional($post['datos_entrada'] ?? null),
            'pasos_ejecucion' => trim((string) $post['pasos_ejecucion']),
            'resultado_esperado' => trim((string) $post['resultado_esperado']),
            'resultado_obtenido' => $this->valorOpcional($post['resultado_obtenido'] ?? null),
            'estado' => trim((string) $post['estado']),
            'observaciones' => $this->valorOpcional($post['observaciones'] ?? null),
            'evidencia' => $this->valorOpcional($post['evidencia'] ?? null),
            'usuario_id' => (int) $usuario['id'],
        ];

        $this->casos->crear($datos);

        return [
            'ok' => true,
            'mensaje' => 'Caso generado correctamente.',
        ];
    }

    public function puedeVer(array $caso, array $usuario): bool
    {
        return $usuario['rol'] === 'admin' || (int) $caso['usuario_id'] === (int) $usuario['id'];
    }

    public function puedeEditar(array $caso, array $usuario): bool
    {
        return $this->puedeVer($caso, $usuario);
    }

    public function puedeEliminar(array $caso, array $usuario): bool
    {
        return $usuario['rol'] === 'admin';
    }

    private function valorOpcional(mixed $valor): ?string
    {
        $valor = trim((string) ($valor ?? ''));

        return $valor === '' ? null : $valor;
    }
}
